<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-header.php'; ?>
<?php
$query = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';
?>
<div class="max-w-7xl mx-auto py-12 px-4 bg-white text-[#111111] min-h-[60vh]">
	<?php
	require_once HEALTHEDIA_PLUGIN_DIR . 'public/views/partials/breadcrumbs.php';
	healthedia_breadcrumbs();
	?>
	<div class="flex flex-col md:flex-row gap-8">

	<!-- Sidebar Filters -->
	<aside class="w-full md:w-64 flex-shrink-0">
		<div class="border-r border-[#E0E0E0] pr-6 pb-6">
			<div class="flex items-center gap-2 mb-6 font-mono text-[10px] font-bold uppercase tracking-widest border-b border-[#E0E0E0] pb-2">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
				Filters
			</div>

			<div class="mb-8">
				<h3 class="font-mono text-[10px] font-bold uppercase tracking-widest mb-4">Research Specialty</h3>
				<div class="space-y-3 font-sans text-sm text-gray-700">
					<label class="flex items-center gap-3"><input type="checkbox" class="border-gray-300 rounded focus:ring-black"> Cardiology & Sports Medicine</label>
					<label class="flex items-center gap-3"><input type="checkbox" class="border-gray-300 rounded focus:ring-black"> Physical Therapy & Rehabilitation</label>
					<label class="flex items-center gap-3"><input type="checkbox" class="border-gray-300 rounded focus:ring-black"> Sports Nutrition & Endocrinology</label>
					<label class="flex items-center gap-3"><input type="checkbox" class="border-gray-300 rounded focus:ring-black"> Sports Science & Physiology</label>
				</div>
			</div>

			<div class="mb-8 border-t border-[#E0E0E0] pt-6">
				<h3 class="font-mono text-[10px] font-bold uppercase tracking-widest mb-4">Publication Year</h3>
				<div class="flex gap-2">
					<span class="border border-[#E0E0E0] rounded-full px-4 py-1 text-xs font-mono text-gray-500 cursor-pointer hover:border-black transition-colors">2025</span>
					<span class="border border-[#E0E0E0] rounded-full px-4 py-1 text-xs font-mono text-gray-500 cursor-pointer hover:border-black transition-colors">2024</span>
				</div>
			</div>

			<div class="mb-8 border-t border-[#E0E0E0] pt-6">
				<h3 class="font-mono text-[10px] font-bold uppercase tracking-widest mb-4">Research Type</h3>
				<div class="space-y-3 font-sans text-sm text-gray-700">
					<label class="flex items-center gap-3"><input type="checkbox" class="border-gray-300 rounded focus:ring-black"> Laboratory Study</label>
					<label class="flex items-center gap-3"><input type="checkbox" class="border-gray-300 rounded focus:ring-black"> Randomized Controlled Trial</label>
				</div>
			</div>

			<div class="border-t border-[#E0E0E0] pt-6">
				<h3 class="font-mono text-[10px] font-bold uppercase tracking-widest mb-4">Affiliation</h3>
				<div class="space-y-3 font-sans text-sm text-gray-700">
					<label class="flex items-center gap-3"><input type="checkbox" class="border-gray-300 rounded focus:ring-black truncate"> Harvard Medical School</label>
					<label class="flex items-center gap-3"><input type="checkbox" class="border-gray-300 rounded focus:ring-black truncate"> Institute of Human Performance...</label>
				</div>
			</div>
		</div>
	</aside>

	<!-- Main Content Area -->
	<div class="flex-grow">
		<!-- Search Header -->
		<div class="relative">
			<header class="mb-10 w-full relative border border-[#E0E0E0] rounded-full flex items-center p-2 shadow-sm bg-white hover:border-black transition-colors z-30">
				<svg class="w-5 h-5 ml-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
				<input type="text" id="archive-search-input" autocomplete="off" value="<?php echo esc_attr($query); ?>" placeholder="Search archive..." class="flex-grow px-4 py-2 font-sans text-lg outline-none bg-transparent">
				<button id="btn-update-search" class="bg-black text-white px-8 py-3 rounded-full font-sans uppercase text-[10px] font-bold tracking-widest hover:bg-gray-800 transition-colors flex items-center gap-2">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
					Update
				</button>
			</header>

			<!-- Search History Dropdown -->
			<div id="search-history-dropdown" class="hidden absolute top-14 left-0 w-full bg-white border border-[#E0E0E0] rounded-2xl shadow-lg z-20 overflow-hidden pt-4 pb-2">
				<div class="px-6 py-3 border-b border-[#E0E0E0] flex justify-between items-center bg-gray-50">
					<span class="font-sans font-bold text-[10px] uppercase tracking-widest text-gray-500">Recent Searches</span>
					<button id="btn-clear-history" class="font-mono text-[10px] text-red-500 hover:text-red-700 uppercase tracking-widest transition-colors">Clear History</button>
				</div>
				<ul id="search-history-list" class="max-h-64 overflow-y-auto">
					<!-- History items populated via JS -->
				</ul>
			</div>
		</div>

		<div class="flex justify-between items-center mb-6 font-mono text-[10px] text-gray-500 uppercase tracking-widest">
			<div id="search-meta">Showing matches for "<?php echo esc_html($query); ?>"</div>
			<div class="flex items-center gap-2">
				Sort:
				<select class="border border-[#E0E0E0] rounded px-2 py-1 outline-none text-black bg-white cursor-pointer focus:border-black">
					<option>Relevance Score</option>
					<option>Date Published</option>
					<option>Citations (Highest)</option>
				</select>
			</div>
		</div>

		<div id="search-results-container" class="space-y-6 border-t border-[#E0E0E0] pt-8">
			<?php if (empty($query)): ?>
				<div class="text-center py-12 text-gray-400 font-mono text-sm uppercase tracking-widest">Enter a query to search the global archive.</div>
			<?php else: ?>
				<div id="loading-spinner" class="text-center py-12">
					<svg class="w-8 h-8 animate-spin mx-auto text-black" fill="none" viewBox="0 0 24 24">
						<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
						<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
					</svg>
				</div>
				<div id="results-list" class="space-y-6 hidden"></div>
			<?php endif; ?>
		</div>
	</div>

</div>
<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-footer.php'; ?>
