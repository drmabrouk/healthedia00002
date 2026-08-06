<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-header.php'; ?>
<div class="healthedia-journal max-w-7xl mx-auto flex flex-col md:flex-row gap-8 py-12 px-4 bg-white text-[#111111]">
	<aside class="w-full md:w-1/4 border-r border-[#E0E0E0] pr-6">
		<div class="sticky top-8">
			<h3 class="font-sans font-bold uppercase tracking-wider mb-4 text-sm border-b border-[#E0E0E0] pb-2">Journal Sections</h3>
			<ul class="font-mono text-sm space-y-2">
				<li><a href="#" class="block px-4 py-2 bg-black text-white rounded-full">Current Issue</a></li>
				<li><a href="#" class="block px-4 py-2 text-gray-500 hover:text-black hover:bg-gray-50 rounded-full transition-colors">Archived Issues</a></li>
				<li><a href="#" class="block px-4 py-2 text-gray-500 hover:text-black hover:bg-gray-50 rounded-full transition-colors">Editorial Board</a></li>
				<?php if (current_user_can('submit_journal')) : ?>
				<li><a href="/submit-journal" class="block px-4 py-2 mt-4 border border-[#E0E0E0] text-gray-500 hover:text-black hover:border-black rounded-full transition-colors text-center">Submit Research</a></li>
				<?php endif; ?>
			</ul>
		</div>
	</aside>
	<main class="w-full md:w-3/4">
		<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 border-b border-[#E0E0E0] pb-4 gap-4">
			<div>
				<h1 class="text-4xl md:text-5xl font-sans font-bold leading-tight tracking-tighter mb-2">Scientific Journal Archive</h1>
				<span class="bg-black text-white px-3 py-1 rounded-full font-mono text-[10px] uppercase tracking-widest">Official Scientific Journal</span>
			</div>
			<div class="text-right font-mono text-xs text-gray-500 uppercase tracking-widest space-y-1">
				<div>ISSN: <span class="text-black font-bold">2831-932X</span></div>
				<div>IF: <span class="text-black font-bold">4.2</span></div>
				<div>h5-Index: <span class="text-black font-bold">18</span></div>
			</div>
		</div>

		<!-- Feed Filter -->
		<div class="mb-6 flex gap-2">
			<input type="text" placeholder="Search archive..." class="border border-[#E0E0E0] rounded-full px-6 py-2 text-sm font-sans outline-none focus:border-black w-full max-w-sm">
			<select class="border border-[#E0E0E0] rounded-full px-6 py-2 text-sm font-sans outline-none focus:border-black cursor-pointer bg-white">
				<option value="">All Specialties</option>
				<option value="cardiology">Cardiology</option>
				<option value="neurology">Neurology</option>
			</select>
		</div>

		<div class="space-y-6">
			<?php
			$args = array('post_type' => array('healthedia_article', 'healthedia_post', 'healthedia_ext_res', 'healthedia_journal'), 'posts_per_page' => 10);
			$query = new WP_Query($args);
			if ($query->have_posts()): while ($query->have_posts()): $query->the_post();
				$doi = get_post_meta(get_the_ID(), '_healthedia_doi', true);
			?>
			<div class="pb-6 border-b border-[#E0E0E0] hover:border-black transition-colors group">
				<div class="flex gap-2 mb-3 font-mono text-[10px] uppercase tracking-widest">
					<span class="border border-black px-2 py-0.5 rounded-full">Clinical Trial</span>
					<span class="text-gray-500 border border-[#E0E0E0] px-2 py-0.5 rounded-full">Open Access</span>
					<?php if($doi): ?><span class="text-gray-500 border border-[#E0E0E0] px-2 py-0.5 rounded-full">DOI: <?php echo esc_html($doi); ?></span><?php endif; ?>
				</div>
				<a href="<?php the_permalink(); ?>" class="font-sans font-bold text-2xl block mb-2 group-hover:underline"><?php the_title(); ?></a>
				<div class="font-mono text-xs text-gray-500 mb-3">By <?php the_author(); ?> • <?php the_date(); ?></div>
				<div class="font-sans text-gray-700 line-clamp-2"><?php the_excerpt(); ?></div>
			</div>
			<?php endwhile; wp_reset_postdata(); else: ?>
			<p class="font-mono text-sm text-gray-500">No published articles available in this archive.</p>
			<?php endif; ?>
		</div>
	</main>
</div>
<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-footer.php'; ?>
