<?php
class Healthedia_Activator {
	public static function activate() {
		require_once HEALTHEDIA_PLUGIN_DIR . 'includes/class-healthedia-db.php';
		Healthedia_DB::create_tables();

		self::register_custom_roles();

		self::auto_provision_pages();

		require_once HEALTHEDIA_PLUGIN_DIR . 'includes/class-healthedia-seeder.php';
		Healthedia_Seeder::seed_mock_data();

		require_once HEALTHEDIA_PLUGIN_DIR . 'includes/class-healthedia-router.php';
		$router = new Healthedia_Router();
		$router->add_rewrite_rules();
		flush_rewrite_rules();
	}

	private static function register_custom_roles() {
		// Member Role
		add_role('member', 'Member', array(
			'read' => true,
		));

		// Researcher Role
		add_role('researcher', 'Researcher', array(
			'read' => true,
			'submit_articles' => true,
			'submit_ext_res' => true,
			'submit_journal' => true,
			'request_verification' => true
		));

		// Reviewer Role
		add_role('reviewer', 'Reviewer', array(
			'read' => true,
			'review_journal_submissions' => true
		));
	}

	private static function auto_provision_pages() {
		$pages = array(
			// 1. Central Gateway (Archive Search)
			'gateway' => array('title' => 'Central Gateway', 'content' => '[healthedia_gateway]'),
			// 2. Authentication Portal
			'auth' => array('title' => 'Authentication Portal', 'content' => '[healthedia_auth]'),
			// 3. Global Directory of Researchers
			'directory' => array('title' => 'Global Directory of Researchers', 'content' => '[healthedia_directory_researchers]'),
			// 4. Global Encyclopedia of Academies
			'academies' => array('title' => 'Global Encyclopedia of Academies', 'content' => '[healthedia_directory_academies]'),
			// 5. Scientific Journal Archive
			'journal' => array('title' => 'Scientific Journal Archive', 'content' => '[healthedia_journal_archive]'),
			// 8. Standalone Internal Dashboard
			'dashboard' => array('title' => 'Standalone Internal Dashboard', 'content' => '[healthedia_dashboard]'),
			// 9. User Member Portal
			'account-settings' => array('title' => 'Account Settings', 'content' => '[healthedia_member_settings]'),
			'saved-research' => array('title' => 'Saved Research', 'content' => '[healthedia_member_saved]'),
			'my-requests' => array('title' => 'My Requests', 'content' => '[healthedia_member_requests]'),
			// 10. Submission Portals
			'submit-article' => array('title' => 'Submit Article', 'content' => ''),
			'submit-research' => array('title' => 'Add Published Research', 'content' => ''),
			'submit-journal' => array('title' => 'Submit to Journal', 'content' => ''),
			// 11. Legal & Support Pages
			'privacy-policy' => array('title' => 'Privacy Policy', 'content' => ''),
			'terms-of-service' => array('title' => 'Terms of Service', 'content' => ''),
			'publication-policies' => array('title' => 'Publication Policies', 'content' => ''),
			'certificate-verification' => array('title' => 'Certificate Verification', 'content' => ''),
			'support' => array('title' => 'Support', 'content' => ''),
		);

		foreach ($pages as $slug => $page_data) {
			$page_check = get_page_by_path($slug);
			if (!isset($page_check->ID)) {
				$new_page_id = wp_insert_post(array(
					'post_name'      => $slug,
					'post_title'     => $page_data['title'],
					'post_content'   => $page_data['content'],
					'post_status'    => 'publish',
					'post_type'      => 'page',
					'comment_status' => 'closed',
					'ping_status'    => 'closed',
				));

				// Set Central Gateway as static front page
				if ($slug === 'gateway' && !is_wp_error($new_page_id)) {
					update_option('show_on_front', 'page');
					update_option('page_on_front', $new_page_id);
				}
			}
		}
	}
}
