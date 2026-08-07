<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-header.php'; ?>
<div class="max-w-7xl mx-auto py-12 px-4 bg-white text-[#111111]">

	<div class="text-center border-b border-[#E0E0E0] pb-8 mb-10">
		<h1 class="text-4xl font-sans font-bold uppercase tracking-tight mb-2">Add Published Research</h1>
		<p class="font-mono text-xs text-gray-500 uppercase tracking-widest">Index existing peer-reviewed publications</p>
	</div>

	<!-- Wizard Progress -->
	<div class="flex justify-between items-center mb-12 font-mono text-[10px] uppercase tracking-widest relative">
		<div class="absolute left-0 top-1/2 w-full h-px bg-[#E0E0E0] -z-10"></div>
		<div class="bg-black text-white px-4 py-1.5 rounded-full z-10 border-2 border-white">1. Bibliographic Info</div>
		<div class="bg-gray-100 text-gray-400 px-4 py-1.5 rounded-full z-10 border-2 border-white">2. Verification</div>
		<div class="bg-gray-100 text-gray-400 px-4 py-1.5 rounded-full z-10 border-2 border-white">3. Indexed</div>
	</div>

	<form id="form-submit-research" class="space-y-8 bg-white border border-[#E0E0E0] rounded-2xl p-8 shadow-sm">
		<input type="hidden" id="ms-type" value="healthedia_ext_res">
		<div>
			<label class="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2">Research Title</label>
			<input type="text" id="ms-title" required class="w-full border border-[#E0E0E0] rounded-xl px-4 py-3 font-sans outline-none focus:border-black transition-colors">
		</div>

		<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
			<div>
				<label class="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2">Primary Specialty</label>
				<select id="ms-specialty" class="w-full border border-[#E0E0E0] rounded-xl px-4 py-3 font-sans outline-none focus:border-black transition-colors bg-white">
					<option value="">Select Specialty...</option>
					<option value="Biomechanics">Biomechanics</option>
					<option value="Cardiology">Cardiology</option>
					<option value="Neurology">Neurology</option>
				</select>
			</div>
			<div>
				<label class="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2">Trial Registration (Optional)</label>
				<input type="text" id="ms-nct" placeholder="NCT Number" class="w-full border border-[#E0E0E0] rounded-xl px-4 py-3 font-sans outline-none focus:border-black transition-colors font-mono text-sm">
			</div>
		</div>

		<div>
			<label class="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2">Interactive Abstract</label>
			<div class="border border-[#E0E0E0] rounded-xl overflow-hidden focus-within:border-black transition-colors">
				<div class="bg-gray-50 border-b border-[#E0E0E0] px-4 py-2 flex gap-2">
					<button type="button" class="font-sans font-bold text-sm hover:text-gray-600">B</button>
					<button type="button" class="font-sans italic text-sm hover:text-gray-600">I</button>
				</div>
				<textarea id="ms-abstract" required rows="6" class="w-full px-4 py-3 font-sans outline-none resize-y"></textarea>
			</div>
		</div>

		<div class="border-t border-dashed border-[#E0E0E0] pt-8">
			<label class="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2">Upload PDF Document</label>
			<div class="border-2 border-dashed border-[#E0E0E0] rounded-xl p-8 text-center hover:border-black transition-colors bg-gray-50 relative">
				<div class="font-sans font-bold text-lg mb-1">Click or Drag & Drop file here</div>
				<div class="font-mono text-[10px] uppercase tracking-widest text-gray-500" id="ms-file-name">Accepted formats: .PDF (Max 20MB)</div>
				<input type="file" id="ms-file" required accept=".pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
			</div>
		</div>

		<div class="pt-2 pb-2">
			<label class="flex items-start gap-3 cursor-pointer">
				<input type="checkbox" required class="mt-0.5 w-4 h-4 text-black border-gray-300 rounded focus:ring-black">
				<span class="font-mono text-[9px] text-gray-500 uppercase tracking-widest leading-relaxed">I explicitly grant permission to display this publication publicly on the platform for indexing and dissemination. This paper will not enter a new peer-review process.</span>
			</label>
		</div>

		<div id="ms-status" class="hidden font-mono text-[10px] uppercase tracking-widest p-4 rounded-xl text-center"></div>

		<div class="flex justify-end gap-4 pt-6">
			<button type="submit" id="btn-submit-ms" class="bg-black text-white px-8 py-3 rounded-full font-sans uppercase text-sm tracking-wide hover:bg-gray-800 transition-colors">Submit for Indexing</button>
		</div>
	</form>
</div>
<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-footer.php'; ?>
