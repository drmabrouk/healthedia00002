<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-header.php'; ?>
<div class="max-w-7xl mx-auto py-24 px-4 text-center">
	<h1 class="text-7xl font-sans font-bold tracking-tighter uppercase mb-4">404</h1>
	<h2 class="text-2xl font-mono uppercase tracking-widest text-gray-500 mb-8">Resource Not Found</h2>

	<p class="font-sans text-lg text-gray-600 mb-12">The academic resource, profile, or publication you are looking for has been moved, removed, or does not exist.</p>

	<div class="max-w-7xl mx-auto mb-12 relative">
		<form action="<?php echo home_url('/archive-search'); ?>" method="GET" class="w-full flex relative items-center">
			<input type="text" name="q" autocomplete="off" class="w-full border border-[#E0E0E0] rounded-xl py-4 pl-4 pr-32 text-lg font-sans outline-none focus:border-black transition-colors shadow-sm" placeholder="Search the archive...">

			<div class="absolute right-2 top-2 bottom-2 flex items-center">
				<button type="submit" class="bg-black text-white px-6 h-10 rounded-lg font-sans uppercase text-sm tracking-wide hover:bg-gray-800 transition-colors flex items-center justify-center gap-2">
					Search
				</button>
			</div>
		</form>
	</div>

	<a href="<?php echo home_url(); ?>" class="font-mono text-sm uppercase tracking-wider text-black hover:underline">← Return to Gateway</a>
</div>
<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-footer.php'; ?>
