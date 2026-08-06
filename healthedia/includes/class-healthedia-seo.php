<?php
class Healthedia_SEO {
	public function inject_metadata() {
		// Prevent double injection if themes do it, but since we use custom headers, we are the main source.
		// However, wp_head() might print title if theme supports title-tag.

		$is_noindex = false;
		$title = get_bloginfo('name');
		$description = get_bloginfo('description');
		$canonical = home_url($_SERVER['REQUEST_URI']);
		$og_type = 'website';
		$image = ''; // Default image if any
		$schema = null;

		// 1. Check for Admin/Dashboard or Portal Pages
		if (get_query_var('healthedia_dashboard') || get_query_var('healthedia_auth_page')) {
			$is_noindex = true;
		}

		$page = get_query_var('healthedia_page');
		if (in_array($page, ['member_settings', 'member_saved', 'member_requests', 'submit_manuscript', 'archive_search'])) {
			$is_noindex = true;
		}

		// 2. Check for Public Profile
		$profile_id = get_query_var('healthedia_profile');
		if (!$profile_id && get_query_var('healthedia_profile_user')) {
			$user_obj = get_query_var('healthedia_profile_user');
			$profile_id = $user_obj->ID;
		}

		if ($profile_id) {
			$privacy_mode = get_user_meta($profile_id, '_healthedia_privacy_mode', true) ?: 'public';
			if ($privacy_mode !== 'public') {
				$is_noindex = true;
			} else {
				$user = get_userdata($profile_id);
				$specialty = get_user_meta($profile_id, '_healthedia_specialty', true);
				$institution = get_user_meta($profile_id, '_healthedia_institution', true);
				$desc = get_user_meta($profile_id, 'description', true);

				$title = $user->display_name . ' - ' . ($specialty ?: 'Researcher') . ' | ' . get_bloginfo('name');
				$description = wp_trim_words($desc, 25, '...');
				if (empty($description)) $description = "View the academic profile and research impact of " . $user->display_name . " on Healthedia.";

				$og_type = 'profile';

				$schema = array(
					'@context' => 'https://schema.org',
					'@type' => 'Person',
					'name' => $user->display_name,
					'jobTitle' => $specialty,
					'affiliation' => array(
						'@type' => 'Organization',
						'name' => $institution
					),
					'url' => $canonical
				);
			}
		}

		// 3. Check for Article
		global $post;
		if (is_single() && in_array($post->post_type, ['healthedia_article', 'post', 'healthedia_ext_res', 'healthedia_journal'])) {
			$title = $post->post_title . ' | ' . get_bloginfo('name');
			$description = wp_trim_words(strip_tags($post->post_content), 25, '...');
			$doi = get_post_meta($post->ID, '_healthedia_doi', true);
			$author_id = $post->post_author;
			$author = get_userdata($author_id);

			$og_type = 'article';

			$schema = array(
				'@context' => 'https://schema.org',
				'@type' => 'ScholarlyArticle',
				'headline' => $post->post_title,
				'description' => $description,
				'author' => array(
					'@type' => 'Person',
					'name' => $author ? $author->display_name : 'Unknown Author'
				),
				'publisher' => array(
					'@type' => 'Organization',
					'name' => get_bloginfo('name')
				),
				'datePublished' => get_the_date('Y-m-d', $post->ID)
			);
			if ($doi) {
				$schema['sameAs'] = 'https://doi.org/' . $doi;
			}
		}

		// 4. Output Meta Tags
		if ($is_noindex) {
			echo '<meta name="robots" content="noindex, nofollow">' . "\n";
		} else {
			echo '<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">' . "\n";
			echo '<link rel="canonical" href="' . esc_url($canonical) . '">' . "\n";
			echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";

			// Open Graph
			echo '<meta property="og:locale" content="' . get_locale() . '">' . "\n";
			echo '<meta property="og:type" content="' . esc_attr($og_type) . '">' . "\n";
			echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
			echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
			echo '<meta property="og:url" content="' . esc_url($canonical) . '">' . "\n";
			echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";

			// Twitter Cards
			echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
			echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
			echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";

			// Schema.org
			if ($schema) {
				echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>' . "\n";
			}
		}
	}

	public function change_title($orig_title) {
		// If WP theme support 'title-tag' is on, we might want to override it.
		// For now, our layout-header.php uses wp_title() which is fine, but we can hook into document_title_parts
		return $orig_title;
	}
}
