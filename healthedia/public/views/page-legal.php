<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-header.php'; ?>
<div class="max-w-7xl mx-auto py-12 px-4 md:px-8 bg-white text-[#111111]">
	<?php if (have_posts()) : while(have_posts()) : the_post(); ?>
	<h1 class="text-4xl md:text-5xl font-sans font-bold uppercase tracking-tight mb-8 border-b border-[#E0E0E0] pb-6 text-center"><?php the_title(); ?></h1>
	<div class="font-mono text-xs text-gray-500 uppercase tracking-widest text-center mb-12">Last Updated: <?php the_modified_date(); ?></div>
	<div class="prose max-w-none font-sans text-gray-800 leading-relaxed space-y-6">
		<?php the_content(); ?>
	</div>
	<?php endwhile; endif; ?>
</div>
<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-footer.php'; ?>
