<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-header.php'; ?>
<div class="healthedia-gateway flex flex-col items-center justify-center bg-white text-[#111111] pt-24 pb-12">
	<h1 class="text-6xl md:text-7xl font-sans font-bold tracking-tighter uppercase mb-2 text-center px-4">HEALTHEDIA</h1>
	<p class="font-mono text-xs md:text-sm text-gray-500 mb-8 uppercase tracking-widest text-center px-4">Global Health Archive & Network</p>
	<div class="w-full max-w-3xl relative px-4 md:px-0">
		<form action="<?php echo home_url('/archive-search'); ?>" method="GET" class="w-full flex flex-col relative" id="gateway-search-form">
			<div class="relative flex items-center">
				<input type="text" name="q" id="gateway-search-input" autocomplete="off" class="w-full border border-[#E0E0E0] rounded-lg py-4 pl-4 pr-32 md:pr-40 text-base md:text-lg font-sans outline-none focus:border-black transition-colors shadow-sm" placeholder="Search across journals, authors...">

				<div class="absolute right-2 top-2 bottom-2 flex items-center gap-1 md:gap-2">
					<button type="button" id="btn-voice-search" title="Voice Search (English)" class="w-10 h-10 bg-gray-50 text-black border border-[#E0E0E0] rounded-lg flex items-center justify-center hover:bg-gray-100 transition-colors hidden sm:flex">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3-3z"></path></svg>
					</button>
					<button type="submit" class="bg-black text-white px-4 md:px-6 h-10 rounded-lg font-sans uppercase text-sm tracking-wide hover:bg-gray-800 transition-colors flex items-center justify-center gap-2">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
						<span class="hidden md:inline">Search</span>
					</button>
				</div>
			</div>

			<div id="search-history-container" class="hidden absolute top-full left-0 right-0 mt-2 bg-white border border-[#E0E0E0] rounded-lg shadow-lg z-50 overflow-hidden font-sans text-sm">
				<div class="p-3 border-b border-[#E0E0E0] font-mono text-[10px] uppercase tracking-widest text-gray-400">Recent Searches</div>
				<ul id="search-history-list">
					<!-- JS injected -->
				</ul>
				<div id="autocomplete-suggestions" class="border-t border-gray-100 hidden">
					<!-- JS injected -->
				</div>
			</div>
		</form>
	</div>

	<!-- Search Suggestions -->
	<div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3 font-mono text-[9px] md:text-[10px] uppercase tracking-widest max-w-3xl w-full px-4 md:px-0">
		<span class="text-gray-400 flex items-center whitespace-nowrap">Trending:</span>
		<div class="flex flex-row gap-2 overflow-x-auto scrollbar-hide items-center justify-start sm:justify-center flex-nowrap w-full pb-2 sm:pb-0">
			<span class="search-tag px-3 py-1.5 bg-gray-50 border border-[#E0E0E0] rounded-md text-gray-600 hover:bg-gray-100 hover:text-black hover:border-gray-300 cursor-pointer transition-all whitespace-nowrap">Oncology DOIs</span>
			<span class="search-tag px-3 py-1.5 bg-gray-50 border border-[#E0E0E0] rounded-md text-gray-600 hover:bg-gray-100 hover:text-black hover:border-gray-300 cursor-pointer transition-all whitespace-nowrap">Verified Researchers</span>
			<span class="search-tag px-3 py-1.5 bg-gray-50 border border-[#E0E0E0] rounded-md text-gray-600 hover:bg-gray-100 hover:text-black hover:border-gray-300 cursor-pointer transition-all whitespace-nowrap">Clinical Trials</span>
			<span class="search-tag px-3 py-1.5 bg-gray-50 border border-[#E0E0E0] rounded-md text-gray-600 hover:bg-gray-100 hover:text-black hover:border-gray-300 cursor-pointer transition-all whitespace-nowrap">Global Health Data</span>
		</div>
	</div>
</div>
<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-footer.php'; ?>
