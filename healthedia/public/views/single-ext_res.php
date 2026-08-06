<?php
// Load custom header instead of get_header()
include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-header.php';
?>
<?php while ( have_posts() ) : the_post();
	$doi = get_post_meta( get_the_ID(), '_healthedia_doi', true );
?>
<div class="healthedia-journal max-w-7xl mx-auto flex flex-col md:flex-row gap-8 py-12 px-4 md:px-8 bg-white text-[#111111]">

	<!-- Sidebar -->
	<aside class="w-full md:w-1/4 border-r border-[#E0E0E0] pr-6">
		<div class="sticky top-8">
			<h3 class="font-sans font-bold uppercase tracking-wider mb-4 text-sm border-b border-[#E0E0E0] pb-2">Publication Index</h3>
			<p class="font-mono text-xs text-gray-500 leading-relaxed mb-6">This research was published externally and has been indexed by the author for public dissemination.</p>

			<?php
			$file_url = get_post_meta(get_the_ID(), '_healthedia_file_url', true);
			if ($file_url):
				if (current_user_can('submit_articles') || current_user_can('submit_ext_res') || current_user_can('submit_journal') || current_user_can('manage_options')):
			?>
			<a href="<?php echo esc_url($file_url); ?>" target="_blank" class="block w-full text-center bg-black text-white px-4 py-3 rounded-xl font-mono text-xs uppercase tracking-widest hover:bg-gray-800 transition-colors flex items-center justify-center gap-2">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
				Download PDF
			</a>
			<?php else: ?>
			<div class="bg-gray-50 border border-[#E0E0E0] p-4 rounded-xl text-center">
				<svg class="w-6 h-6 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
				<p class="font-mono text-[10px] text-gray-500 uppercase tracking-widest">Login with a Researcher account to download full text.</p>
			</div>
			<?php
				endif;
			endif;
			?>

			<div class="mt-12">
				<h3 class="font-sans font-bold uppercase tracking-wider mb-4 text-sm border-b border-[#E0E0E0] pb-2">Tools</h3>
				<div class="flex gap-2">
					<button id="btn-zoom-in" title="Increase Text Size" class="w-8 h-8 inline-flex items-center justify-center bg-white border border-[#E0E0E0] rounded-lg text-gray-400 hover:text-black hover:border-black transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"></path></svg></button>
					<button id="btn-zoom-out" title="Decrease Text Size" class="w-8 h-8 inline-flex items-center justify-center bg-white border border-[#E0E0E0] rounded-lg text-gray-400 hover:text-black hover:border-black transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM7 10h6"></path></svg></button>
				</div>
			</div>
		</div>
	</aside>

	<!-- Main Content -->
	<main class="flex-grow max-w-4xl w-full article-content transition-all duration-300">
		<?php
		require_once HEALTHEDIA_PLUGIN_DIR . 'public/views/partials/breadcrumbs.php';
		healthedia_breadcrumbs();
		?>

		<div class="flex gap-2 mb-4 font-mono text-xs">
			<span class="px-2 py-1 border border-black text-black rounded-full uppercase tracking-wider">External Research</span>
			<span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full uppercase tracking-wider">Indexed Record</span>
		</div>

		<h1 class="text-4xl md:text-5xl font-sans font-bold leading-tight tracking-tighter mb-6"><?php the_title(); ?></h1>

		<div class="flex flex-col gap-2 mb-10 border-y border-[#E0E0E0] py-4">
			<div class="font-sans text-sm">
				<strong>Authors:</strong> <a href="#" class="underline hover:no-underline"><?php the_author(); ?></a>
			</div>
			<?php if ( $doi ) : ?>
			<div class="font-mono text-xs text-gray-500">
				<strong>DOI:</strong> <?php echo esc_html( $doi ); ?>
			</div>
			<?php endif; ?>
			<div class="font-mono text-xs text-gray-500">
				<strong>Published:</strong> <?php the_date(); ?>
			</div>
		</div>

		<div class="prose max-w-none font-sans text-lg leading-relaxed text-gray-800">
			<?php the_content(); ?>
		</div>
	</main>
</div>
<?php endwhile; ?>
<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-footer.php'; ?>
