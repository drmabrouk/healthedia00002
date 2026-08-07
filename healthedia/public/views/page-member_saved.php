<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-header.php'; ?>
<?php
$user_id = get_current_user_id();
?>
<div class="max-w-7xl mx-auto flex flex-col md:flex-row gap-8 py-12 px-4 md:px-8 bg-white text-[#111111]">
	<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-member-sidebar.php'; ?>

	<main class="flex-grow w-full md:w-3/4">
		<h1 class="text-3xl font-sans font-bold uppercase tracking-tight mb-8 border-b border-[#E0E0E0] pb-4">My Publications & Authored Research</h1>

		<div class="space-y-4">
			<?php
			$args = array(
				'post_type' => array('healthedia_article', 'healthedia_post', 'healthedia_ext_res', 'healthedia_journal'),
				'author' => $user_id,
				'posts_per_page' => -1,
				'post_status' => array('publish', 'pending')
			);
			$query = new WP_Query($args);

			if ($query->have_posts()): while ($query->have_posts()): $query->the_post();
				$status = get_post_status();
				$doi = get_post_meta(get_the_ID(), '_healthedia_doi', true) ?: 'Pending DOI Assignment';
			?>
			<div class="p-6 border border-[#E0E0E0] rounded-2xl hover:border-black transition-colors flex flex-col md:flex-row justify-between md:items-center gap-4 group bg-white shadow-sm hover:shadow-md">
				<div>
					<div class="flex gap-2 mb-2 font-mono text-[10px] uppercase tracking-widest text-gray-500">
						<?php if ($status === 'publish'): ?>
							<span class="bg-black text-white px-2 py-0.5 rounded text-[10px]">Published</span>
						<?php else: ?>
							<span class="bg-yellow-100 text-yellow-800 border border-yellow-200 px-2 py-0.5 rounded text-[10px]">Pending Editorial Review</span>
						<?php endif; ?>
						<span class="border border-[#E0E0E0] px-2 py-0.5 rounded-full"><?php the_date(); ?></span>
					</div>
					<a href="<?php echo $status === 'publish' ? get_permalink() : '#'; ?>" class="font-sans font-bold text-lg block <?php echo $status === 'publish' ? 'group-hover:underline' : 'cursor-default text-gray-700'; ?>"><?php the_title(); ?></a>
					<div class="font-mono text-xs text-gray-500 mt-1">DOI: <?php echo esc_html($doi); ?></div>
				</div>
				<?php if ($status === 'publish'): ?>
				<a href="<?php the_permalink(); ?>" class="flex-shrink-0 border border-[#E0E0E0] px-4 py-2 rounded-full font-mono text-xs uppercase hover:bg-gray-50 hover:border-black transition-colors">View Research Page</a>
				<?php endif; ?>
			</div>
			<?php endwhile; wp_reset_postdata(); else: ?>
			<div class="text-center py-12 text-gray-400 font-mono text-sm uppercase tracking-widest border border-dashed border-[#E0E0E0] rounded-2xl">
				No authored publications found.
			</div>
			<?php endif; ?>
		</div>
	</main>
</div>
<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-footer.php'; ?>
