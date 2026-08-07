<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-header.php'; ?>
<div class="healthedia-directory max-w-7xl mx-auto py-12 px-4 bg-white text-[#111111]">

	<div class="flex flex-col md:flex-row justify-between items-end border-b border-[#E0E0E0] pb-6 mb-8 gap-4">
		<div>
			<h1 class="text-4xl font-sans font-bold uppercase tracking-tight mb-2">Global Directory of Researchers</h1>
			<p class="font-mono text-sm text-gray-500 uppercase">Verified academic and clinical professionals</p>
		</div>
		<div>
			<a href="<?php echo home_url('/login'); ?>" class="inline-block bg-black text-white px-8 py-3.5 rounded-xl font-sans font-bold uppercase text-sm tracking-wider hover:bg-[#800020] transition-colors shadow-sm text-center">Become a Researcher</a>
		</div>
	</div>

	<!-- Filter Toolbar -->
	<div class="flex flex-col md:flex-row gap-4 mb-8 items-center bg-gray-50 p-4 rounded-2xl border border-[#E0E0E0]">
		<input type="text" placeholder="Search by name..." class="flex-grow border border-[#E0E0E0] rounded-full px-6 py-2.5 text-sm font-sans outline-none focus:border-black bg-white">
		<select class="border border-[#E0E0E0] rounded-full px-6 py-2.5 text-sm font-sans outline-none focus:border-black cursor-pointer bg-white">
			<option value="">All Specialties</option>
			<option value="cardiology">Cardiology</option>
			<option value="neurology">Neurology</option>
			<option value="biomechanics">Biomechanics</option>
		</select>
		<select class="border border-[#E0E0E0] rounded-full px-6 py-2.5 text-sm font-sans outline-none focus:border-black cursor-pointer bg-white">
			<option value="">All Countries</option>
			<option value="us">United States</option>
			<option value="uk">United Kingdom</option>
		</select>
		<label class="flex items-center gap-2 font-mono text-xs uppercase cursor-pointer">
			<input type="checkbox" checked class="accent-black w-4 h-4 cursor-pointer">
			<span>Verified Only</span>
		</label>
		<div class="font-mono text-xs uppercase bg-white border border-[#E0E0E0] px-4 py-2.5 rounded-full whitespace-nowrap ml-auto">
			Showing <span id="dir-count">0</span> Results
		</div>
	</div>

	<!-- Directory Grid -->
	<div id="directory-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
		<div class="col-span-full text-center py-12 font-mono text-sm text-gray-500 animate-pulse">Loading directory data...</div>
	</div>

	<!-- Pagination -->
	<div class="mt-12 flex justify-center gap-2">
		<button class="px-6 py-2 border border-[#E0E0E0] rounded-full font-mono text-sm uppercase hover:border-black transition-colors disabled:opacity-50">Previous</button>
		<button class="px-6 py-2 border border-[#E0E0E0] rounded-full font-mono text-sm uppercase hover:border-black transition-colors disabled:opacity-50">Next Page</button>
	</div>

</div>
<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-footer.php'; ?>
