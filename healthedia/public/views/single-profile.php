<?php
/**
 * Wikipedia-Style Public Researcher Profile Template
 */
include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-header.php';

$user_id = get_query_var('healthedia_profile');
if (!$user_id && get_query_var('healthedia_profile_user')) {
	$user_obj = get_query_var('healthedia_profile_user');
	$user_id = $user_obj->ID;
}

$user = get_userdata($user_id);

if (!$user) {
	echo "<div class='text-center py-20 font-sans'>Researcher not found.</div>";
	include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-footer.php';
	return;
}

$privacy_mode = get_user_meta($user_id, '_healthedia_privacy_mode', true) ?: 'public';

if (get_current_user_id() != $user_id) {
	if ($privacy_mode === 'private') {
		echo "<div class='text-center py-20 font-sans'>This profile is private.</div>";
		include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-footer.php';
		return;
	} elseif ($privacy_mode === 'hidden') {
		echo "<div class='text-center py-20 font-sans'>This profile is temporarily hidden.</div>";
		include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-footer.php';
		return;
	}
}

require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Profiles/class-profile-model.php';
require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Profiles/class-profile-verification.php';

$metrics = Healthedia_Profile_Model::get_metrics($user_id);
$is_verified = Healthedia_Profile_Verification::is_verified($user_id);

$specialty = get_user_meta($user_id, '_healthedia_specialty', true);
$institution = get_user_meta($user_id, '_healthedia_institution', true);
$country = get_user_meta($user_id, '_healthedia_country', true);
$orcid = get_user_meta($user_id, '_healthedia_orcid', true);
$photo_url = get_user_meta($user_id, '_healthedia_profile_photo', true);

$academic_title = get_user_meta($user_id, '_healthedia_academic_title', true);
$academic_positions = get_user_meta($user_id, '_healthedia_academic_positions', true);
$expertise = get_user_meta($user_id, '_healthedia_expertise', true);
$interests = get_user_meta($user_id, '_healthedia_interests', true);
$google_scholar = get_user_meta($user_id, '_healthedia_google_scholar', true);
$researchgate = get_user_meta($user_id, '_healthedia_researchgate', true);
$scopus = get_user_meta($user_id, '_healthedia_scopus', true);
$linkedin = get_user_meta($user_id, '_healthedia_linkedin', true);
$twitter = get_user_meta($user_id, '_healthedia_twitter', true);
$contact_email = get_user_meta($user_id, '_healthedia_contact_email', true);
$enable_contact = get_user_meta($user_id, '_healthedia_enable_contact', true) ?: 'no';
$projects = get_user_meta($user_id, '_healthedia_projects', true);
$books = get_user_meta($user_id, '_healthedia_books', true);
$awards = get_user_meta($user_id, '_healthedia_awards', true);
$certifications = get_user_meta($user_id, '_healthedia_certifications', true);
$description = get_user_meta($user_id, 'description', true);

// Calculate publication count
$post_count = count_user_posts($user_id, 'post') + count_user_posts($user_id, 'healthedia_ext_res') + count_user_posts($user_id, 'healthedia_journal') + count_user_posts($user_id, 'healthedia_article');
?>
<div class="healthedia-profile max-w-7xl mx-auto py-8 px-4 bg-white text-[#111111]">
	<?php
	require_once HEALTHEDIA_PLUGIN_DIR . 'public/views/partials/breadcrumbs.php';
	healthedia_breadcrumbs();
	?>

	<!-- Upper Actions Bar -->
	<div class="flex justify-between items-center mb-8 border-b border-[#E0E0E0] pb-4">
		<a href="<?php echo home_url('/directory'); ?>" class="border border-[#E0E0E0] rounded-full px-4 py-1.5 text-xs font-mono uppercase tracking-widest hover:bg-gray-50 flex items-center gap-2 transition-colors">
			&larr; Back to Directory
		</a>
		<div class="flex items-center gap-2 border border-[#E0E0E0] rounded-full px-3 py-1 font-mono text-xs uppercase tracking-widest">
			<span class="text-gray-400">Registry:</span>
			<span class="bg-black text-white rounded-full px-2 py-0.5">HE-<?php echo esc_html($user_id); ?></span>
		</div>
	</div>

	<!-- Wikipedia-Style Split Layout -->
	<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

		<!-- Left Main Content Area (Col Span 2) -->
		<div class="lg:col-span-2 space-y-8">
			<!-- Title & Bio Section -->
			<div class="border-b border-[#E0E0E0] pb-6">
				<div class="flex flex-wrap items-center gap-3 mb-2">
					<h1 class="text-4xl font-sans font-bold tracking-tight"><?php echo esc_html($user->display_name); ?></h1>
					<?php if ($academic_title) : ?>
						<span class="text-lg text-gray-500 font-serif italic">(<?php echo esc_html($academic_title); ?>)</span>
					<?php endif; ?>
					<?php if ($is_verified): ?>
						<span class="bg-black text-white px-2.5 py-1 rounded-md text-[10px] font-mono uppercase tracking-widest flex items-center gap-1">
							<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
							Verified Specialist
						</span>
					<?php endif; ?>
				</div>

				<?php if ($academic_positions) : ?>
					<p class="font-sans text-lg font-semibold text-gray-700 mb-4"><?php echo esc_html($academic_positions); ?></p>
				<?php else : ?>
					<p class="font-sans text-lg text-gray-500 mb-4"><?php echo esc_html($specialty ?: 'Independent Investigator'); ?></p>
				<?php endif; ?>

				<!-- Abstract / Biography -->
				<div class="prose max-w-none text-base text-gray-800 leading-relaxed font-serif pt-4 border-t border-gray-100">
					<?php if ($description) : ?>
						<?php echo wpautop(esc_html($description)); ?>
					<?php else : ?>
						<p class="italic text-gray-400">Professional biography has not been published yet.</p>
					<?php endif; ?>
				</div>
			</div>

			<!-- Areas of Expertise & Interests tags -->
			<?php if ($expertise || $interests) : ?>
				<div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-b border-[#E0E0E0] pb-8">
					<?php if ($expertise) : ?>
						<div>
							<h3 class="font-mono text-[10px] uppercase tracking-widest text-gray-400 mb-3 font-bold">Areas of Expertise</h3>
							<div class="flex flex-wrap gap-2">
								<?php foreach (explode(',', $expertise) as $tag) : ?>
									<span class="bg-gray-100 text-black px-3 py-1 rounded font-mono text-[10px] uppercase tracking-wider border border-gray-200"><?php echo esc_html(trim($tag)); ?></span>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>

					<?php if ($interests) : ?>
						<div>
							<h3 class="font-mono text-[10px] uppercase tracking-widest text-gray-400 mb-3 font-bold">Research Interests</h3>
							<div class="flex flex-wrap gap-2">
								<?php foreach (explode(',', $interests) as $tag) : ?>
									<span class="bg-gray-50 text-gray-600 px-3 py-1 rounded font-mono text-[10px] uppercase tracking-wider border border-gray-100"><?php echo esc_html(trim($tag)); ?></span>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<!-- Research Projects -->
			<?php if ($projects) : ?>
				<div class="border-b border-[#E0E0E0] pb-8">
					<h3 class="text-xl font-sans font-bold uppercase tracking-tight mb-4 flex items-center gap-2">
						<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
						Research Projects
					</h3>
					<div class="prose max-w-none text-sm text-gray-700 leading-relaxed font-sans">
						<?php echo wpautop(esc_html($projects)); ?>
					</div>
				</div>
			<?php endif; ?>

			<!-- Books Published -->
			<?php if ($books) : ?>
				<div class="border-b border-[#E0E0E0] pb-8">
					<h3 class="text-xl font-sans font-bold uppercase tracking-tight mb-4 flex items-center gap-2">
						<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
						Books & Major Contributions
					</h3>
					<div class="prose max-w-none text-sm text-gray-700 leading-relaxed font-sans italic">
						<?php echo wpautop(esc_html($books)); ?>
					</div>
				</div>
			<?php endif; ?>

			<!-- Awards & Honors -->
			<?php if ($awards) : ?>
				<div class="border-b border-[#E0E0E0] pb-8">
					<h3 class="text-xl font-sans font-bold uppercase tracking-tight mb-4 flex items-center gap-2">
						<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
						Awards & Honors
					</h3>
					<div class="prose max-w-none text-sm text-gray-700 leading-relaxed font-sans">
						<?php echo wpautop(esc_html($awards)); ?>
					</div>
				</div>
			<?php endif; ?>

			<!-- Certifications -->
			<?php if ($certifications) : ?>
				<div class="border-b border-[#E0E0E0] pb-8">
					<h3 class="text-xl font-sans font-bold uppercase tracking-tight mb-4 flex items-center gap-2">
						<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
						Certifications & Credentials
					</h3>
					<div class="prose max-w-none text-sm text-gray-700 leading-relaxed font-sans">
						<?php echo wpautop(esc_html($certifications)); ?>
					</div>
				</div>
			<?php endif; ?>

			<!-- Publications Section -->
			<div class="mt-8">
				<h3 class="text-xl font-sans font-bold uppercase tracking-tight mb-6 flex items-center gap-2">
					<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
					Academic Publications & Platform Registry (<?php echo $post_count; ?>)
				</h3>

				<div class="space-y-4">
					<?php
					$args = array(
						'post_type' => array('healthedia_article', 'post', 'healthedia_ext_res', 'healthedia_journal'),
						'author' => $user_id,
						'posts_per_page' => 20,
						'orderby' => 'meta_value_num',
						'meta_key' => '_healthedia_citations',
						'order' => 'DESC'
					);
					$query = new WP_Query($args);
					if ($query->have_posts()): while ($query->have_posts()): $query->the_post();
						$doi = get_post_meta(get_the_ID(), '_healthedia_doi', true);
						$citations = (int)get_post_meta(get_the_ID(), '_healthedia_citations', true);
					?>
					<div class="p-5 border border-[#E0E0E0] rounded-xl hover:border-black transition-colors bg-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
						<div>
							<a href="<?php the_permalink(); ?>" class="font-sans font-bold text-base hover:underline text-black block"><?php the_title(); ?></a>
							<div class="flex flex-wrap items-center gap-3 font-mono text-[10px] text-gray-500 uppercase tracking-wider mt-1.5">
								<span><?php echo get_the_date(); ?></span>
								<span>•</span>
								<span class="text-gray-400">DOI:</span>
								<span class="text-black"><?php echo esc_html($doi ?: 'N/A'); ?></span>
							</div>
						</div>
						<div class="text-left sm:text-right flex-shrink-0">
							<div class="font-mono text-[9px] text-gray-400 uppercase tracking-widest leading-none mb-1">Citations</div>
							<div class="font-sans font-bold text-xl text-black"><?php echo $citations; ?></div>
						</div>
					</div>
					<?php endwhile; wp_reset_postdata(); else: ?>
					<div class="p-6 border border-[#E0E0E0] rounded-xl bg-gray-50 text-center font-mono text-xs text-gray-400 uppercase tracking-widest">
						No platform indexed publications found.
					</div>
					<?php endif; ?>
				</div>
			</div>

			<!-- Research Impact & Metrics Section -->
			<div class="pt-8 border-t border-[#E0E0E0] space-y-6">
				<h3 class="text-xl font-sans font-bold uppercase tracking-tight flex items-center gap-2">
					<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
					Platform Citation Impact Metrics
				</h3>

				<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
					<div class="border border-[#E0E0E0] rounded-xl p-5 bg-[#FAFAFA]">
						<div class="font-mono text-[9px] uppercase tracking-widest text-gray-400 mb-1">Total Citations</div>
						<div class="text-3xl font-sans font-bold text-black"><?php echo number_format_i18n($metrics->citations); ?></div>
						<p class="text-[10px] text-gray-500 mt-2 font-sans">Sum of citations across all platform tracked manuscripts.</p>
					</div>

					<div class="border border-[#E0E0E0] rounded-xl p-5 bg-[#FAFAFA]">
						<div class="font-mono text-[9px] uppercase tracking-widest text-gray-400 mb-1">H-Index (Platform)</div>
						<div class="text-3xl font-sans font-bold text-black">5</div>
						<p class="text-[10px] text-gray-500 mt-2 font-sans">Author has 5 publications with 5 or more citations.</p>
					</div>

					<div class="border border-[#E0E0E0] rounded-xl p-5 bg-[#FAFAFA]">
						<div class="font-mono text-[9px] uppercase tracking-widest text-gray-400 mb-1">Mean Citation Rate</div>
						<div class="text-3xl font-sans font-bold text-black">
							<?php echo $metrics->citations > 0 ? number_format($metrics->citations / max(1, $post_count), 1) : 0; ?>
						</div>
						<p class="text-[10px] text-gray-500 mt-2 font-sans">Average citations received per platform-indexed paper.</p>
					</div>
				</div>
			</div>
		</div>

		<!-- Right Column: Wikipedia-Style Infobox Sidebar -->
		<div class="lg:col-span-1">
			<div class="border border-[#DCDCDC] bg-[#F8F9FA] rounded shadow-sm overflow-hidden p-5 space-y-6">
				<!-- Header inside infobox -->
				<div class="text-center pb-4 border-b border-[#DCDCDC]">
					<h2 class="text-xl font-sans font-bold text-black tracking-tight leading-tight"><?php echo esc_html($user->display_name); ?></h2>
					<?php if ($academic_title) : ?>
						<p class="font-serif italic text-xs text-gray-500 mt-1"><?php echo esc_html($academic_title); ?></p>
					<?php endif; ?>
				</div>

				<!-- Infobox Portrait -->
				<div class="w-full h-56 bg-white border border-[#DCDCDC] rounded overflow-hidden relative flex items-center justify-center">
					<?php
					if ($photo_url) {
						echo '<img src="'.esc_url($photo_url).'" class="w-full h-full object-cover" loading="lazy" alt="'.esc_attr($user->display_name).'">';
					} else {
						echo get_avatar($user_id, 300, '', esc_attr($user->display_name), array('class' => 'w-full h-full object-cover', 'loading' => 'lazy'));
					}
					?>
				</div>

				<!-- Infobox Facts Table -->
				<div class="text-xs space-y-3.5">
					<h3 class="font-sans font-bold text-[10px] uppercase tracking-widest text-gray-400 pb-2 border-b border-[#DCDCDC]">Quick Facts</h3>

					<div class="grid grid-cols-3 gap-2">
						<span class="font-bold text-gray-500 col-span-1">Affiliation</span>
						<span class="col-span-2 text-black text-right sm:text-left"><?php echo esc_html($institution ?: 'Independent Researcher'); ?></span>
					</div>

					<div class="grid grid-cols-3 gap-2">
						<span class="font-bold text-gray-500 col-span-1">Specialty</span>
						<span class="col-span-2 text-black text-right sm:text-left"><?php echo esc_html($specialty ?: 'N/A'); ?></span>
					</div>

					<?php if ($academic_positions) : ?>
						<div class="grid grid-cols-3 gap-2">
							<span class="font-bold text-gray-500 col-span-1">Position</span>
							<span class="col-span-2 text-black text-right sm:text-left"><?php echo esc_html($academic_positions); ?></span>
						</div>
					<?php endif; ?>

					<div class="grid grid-cols-3 gap-2">
						<span class="font-bold text-gray-500 col-span-1">Origin</span>
						<span class="col-span-2 text-black text-right sm:text-left"><?php echo esc_html($country ?: 'N/A'); ?></span>
					</div>

					<div class="grid grid-cols-3 gap-2 pt-2 border-t border-[#DCDCDC] items-center">
						<span class="font-bold text-gray-500 col-span-1">ORCID iD</span>
						<span class="col-span-2 text-right sm:text-left">
							<?php if ($orcid) : ?>
								<a href="https://orcid.org/<?php echo esc_attr($orcid); ?>" target="_blank" rel="noopener" class="text-[#0066cc] hover:underline font-mono inline-flex items-center gap-1">
									<?php echo esc_html($orcid); ?>
									<svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
								</a>
							<?php else : ?>
								<span class="text-gray-400">N/A</span>
							<?php endif; ?>
						</span>
					</div>

					<!-- Scholarly links -->
					<?php if ($google_scholar || $researchgate || $scopus) : ?>
						<div class="pt-2 border-t border-[#DCDCDC] space-y-2">
							<span class="font-bold text-gray-500 block mb-1">External Indices</span>
							<?php if ($google_scholar) : ?>
								<div class="flex justify-between items-center">
									<span class="text-gray-400 font-mono text-[9px]">Google Scholar</span>
									<a href="<?php echo esc_url($google_scholar); ?>" target="_blank" rel="noopener" class="text-[#0066cc] font-bold hover:underline">View Index &rarr;</a>
								</div>
							<?php endif; ?>
							<?php if ($researchgate) : ?>
								<div class="flex justify-between items-center">
									<span class="text-gray-400 font-mono text-[9px]">ResearchGate</span>
									<a href="<?php echo esc_url($researchgate); ?>" target="_blank" rel="noopener" class="text-[#0066cc] font-bold hover:underline">View Profile &rarr;</a>
								</div>
							<?php endif; ?>
							<?php if ($scopus) : ?>
								<div class="flex justify-between items-center">
									<span class="text-gray-400 font-mono text-[9px]">Scopus</span>
									<a href="<?php echo esc_url($scopus); ?>" target="_blank" rel="noopener" class="text-[#0066cc] font-bold hover:underline">View ID &rarr;</a>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<!-- Social links -->
					<?php if ($linkedin || $twitter) : ?>
						<div class="pt-2 border-t border-[#DCDCDC] space-y-2">
							<span class="font-bold text-gray-500 block mb-1">Professional Networks</span>
							<?php if ($linkedin) : ?>
								<div class="flex justify-between items-center">
									<span class="text-gray-400 font-mono text-[9px]">LinkedIn</span>
									<a href="<?php echo esc_url($linkedin); ?>" target="_blank" rel="noopener" class="text-[#0066cc] font-bold hover:underline">Connect &rarr;</a>
								</div>
							<?php endif; ?>
							<?php if ($twitter) : ?>
								<div class="flex justify-between items-center">
									<span class="text-gray-400 font-mono text-[9px]">Twitter / X</span>
									<a href="<?php echo esc_url($twitter); ?>" target="_blank" rel="noopener" class="text-[#0066cc] font-bold hover:underline">Follow &rarr;</a>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>

				</div>

				<!-- Contact / Lab Inquiry (if enabled) -->
				<?php if ($enable_contact === 'yes' && $contact_email) : ?>
					<div class="pt-4 border-t border-[#DCDCDC] text-center">
						<a href="mailto:<?php echo esc_attr($contact_email); ?>?subject=Inquiry%20via%20Healthedia%20Profile" class="w-full bg-black text-white py-2.5 rounded font-mono text-[10px] uppercase tracking-widest font-bold hover:bg-gray-800 transition-colors flex items-center justify-center gap-2">
							<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
							Contact Affiliated Lab
						</a>
					</div>
				<?php endif; ?>

			</div>
		</div>

	</div>
</div>
<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-footer.php'; ?>
