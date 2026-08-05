document.addEventListener('DOMContentLoaded', async () => {
	const queryInput = document.getElementById('archive-search-input');
	const btnUpdate = document.getElementById('btn-update-search');
	const container = document.getElementById('results-list');
	const spinner = document.getElementById('loading-spinner');
	const meta = document.getElementById('search-meta');

	const performSearch = async (query) => {
		if (!query) return;

		if(spinner) spinner.classList.remove('hidden');
		if(container) container.classList.add('hidden');

		try {
			const res = await fetch(`/wp-json/healthedia/v1/search?q=${encodeURIComponent(query)}`);
			const data = await res.json();

			if(spinner) spinner.classList.add('hidden');
			if(container) container.classList.remove('hidden');

			if (!data || data.length === 0) {
				container.innerHTML = '<div class="text-center py-12 text-gray-400 font-mono text-sm uppercase tracking-widest">No results found for your query.</div>';
				if (meta) meta.innerHTML = `Showing 0 matches for "${escapeHTML(query)}"`;
				return;
			}

			if (meta) meta.innerHTML = `Showing ${data.length} matches for "${escapeHTML(query)}"`;
			let html = '';

			data.forEach(item => {
				if (item.type === 'healthedia_article' || item.type === 'healthedia_post' || item.type === 'healthedia_ext_res' || item.type === 'healthedia_journal') {
					let badge = 'Article';
					if (item.type === 'healthedia_journal') badge = 'Journal Paper';
					if (item.type === 'healthedia_ext_res') badge = 'Ext. Research';

					html += `
					<div class="border border-[#E0E0E0] rounded-2xl p-8 hover:border-black transition-colors shadow-sm bg-white">
						<div class="flex items-center gap-4 font-mono text-[10px] text-gray-500 uppercase tracking-widest mb-4">
							<span class="bg-gray-100 px-3 py-1 rounded-full border border-[#E0E0E0] font-bold text-black text-[9px]">${badge}</span>
							<span class="flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg> Healthedia Global Journal</span>
						</div>
						<a href="${item.url}" class="font-sans font-bold text-2xl block hover:underline mb-4 text-[#111111] leading-tight">${escapeHTML(item.title)}</a>

						<div class="flex items-center gap-6 font-mono text-xs text-gray-500 mb-6 border-b border-[#E0E0E0] pb-6">
							<div class="flex items-center gap-2">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
								${escapeHTML(item.meta.doi || 'N/A')}
							</div>
						</div>

						<p class="font-sans text-gray-600 text-sm leading-relaxed mb-6 line-clamp-3">
							High-intensity interval training (HIIT) has emerged as an efficient strategy... (Abstract preview hidden for mock display).
						</p>

						<div class="flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-[#E0E0E0]">
							<div class="flex items-center gap-2 font-mono text-[10px] text-gray-500 uppercase tracking-widest">
								DOI: <span class="bg-white border border-[#E0E0E0] px-3 py-1.5 rounded">${escapeHTML(item.meta.doi || 'N/A')}</span>
							</div>
							<div class="flex gap-4 items-center">
								<a href="${item.url}" class="font-mono text-[10px] uppercase font-bold text-black hover:underline tracking-widest">View Scholarly Page</a>
								<a href="${item.url}" class="bg-black text-white px-6 py-2.5 rounded-full font-mono text-[10px] uppercase font-bold tracking-widest hover:bg-gray-800 transition-colors flex items-center gap-2">
									<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
									Open PDF (Access)
								</a>
							</div>
						</div>
					</div>`;
				} else if (item.type === 'user') {
					html += `
					<div class="border border-[#E0E0E0] rounded-2xl p-6 hover:border-black transition-colors shadow-sm bg-white flex items-center gap-6">
						<div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center font-sans font-bold text-2xl text-gray-400 border border-[#E0E0E0] flex-shrink-0">${escapeHTML(item.title).charAt(0)}</div>
						<div class="flex-grow">
							<div class="flex items-center gap-3 mb-1">
								<a href="${item.url}" class="font-sans font-bold text-xl block hover:underline text-[#111111]">${escapeHTML(item.title)}</a>
								${item.meta.verified ? '<svg class="w-4 h-4 text-black flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>' : ''}
							</div>
							<div class="font-mono text-[10px] text-gray-500 uppercase tracking-widest mt-1">${escapeHTML(item.meta.specialty || 'Independent Researcher')}</div>
						</div>
						<a href="${item.url}" class="border border-[#E0E0E0] px-4 py-2 rounded-full font-mono text-[10px] uppercase font-bold tracking-widest hover:border-black transition-colors whitespace-nowrap">View Profile</a>
					</div>`;
				} else if (item.type === 'healthedia_inst') {
					html += `
					<div class="border border-[#E0E0E0] rounded-2xl p-6 hover:border-black transition-colors shadow-sm bg-white flex items-center gap-6">
						<div class="w-16 h-16 bg-gray-50 rounded-xl flex items-center justify-center font-sans font-bold text-2xl text-gray-400 border border-[#E0E0E0] flex-shrink-0">
							<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
						</div>
						<div class="flex-grow">
							<a href="${item.url}" class="font-sans font-bold text-xl block hover:underline text-[#111111] mb-1">${escapeHTML(item.title)}</a>
							<div class="font-mono text-[10px] text-gray-500 uppercase tracking-widest mt-1">Verified Institution</div>
						</div>
					</div>`;
				}
			});

			container.innerHTML = html;

		} catch (e) {
			if(spinner) spinner.classList.add('hidden');
			if(container) {
				container.classList.remove('hidden');
				container.innerHTML = '<div class="text-center py-12 text-red-500 font-mono text-sm uppercase tracking-widest">Error fetching results.</div>';
			}
		}
	};

	const escapeHTML = (str) => {
		if (typeof str !== 'string') return str;
		return str.replace(/[&<>'"]/g,
			tag => ({
				'&': '&amp;',
				'<': '&lt;',
				'>': '&gt;',
				"'": '&#39;',
				'"': '&quot;'
			}[tag] || tag)
		);
	};

	if (queryInput && btnUpdate) {
		btnUpdate.addEventListener('click', () => {
			const newQuery = queryInput.value.trim();
			const url = new URL(window.location);
			url.searchParams.set('q', newQuery);
			window.history.pushState({}, '', url);
			performSearch(newQuery);
		});

		queryInput.addEventListener('keypress', (e) => {
			if (e.key === 'Enter') {
				e.preventDefault();
				btnUpdate.click();
			}
		});

		// Initial fetch
		const urlParams = new URLSearchParams(window.location.search);
		if (urlParams.has('q')) {
			performSearch(urlParams.get('q'));
		}
	}
});
