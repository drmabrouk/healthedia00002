<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-header.php'; ?>
<div class="max-w-7xl mx-auto py-12 px-4 md:px-8 bg-white text-[#111111]">

	<h1 class="text-4xl md:text-5xl font-sans font-bold uppercase tracking-tight mb-4 text-center">Global Support</h1>
	<p class="font-mono text-sm text-gray-500 uppercase mb-12 text-center">Technical & Editorial Assistance</p>

	<form class="space-y-6 bg-gray-50 border border-[#E0E0E0] rounded-2xl p-8 md:p-12 shadow-sm">
		<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
			<div>
				<label class="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2">Your Name</label>
				<input type="text" class="w-full border border-[#E0E0E0] rounded-xl px-4 py-3 font-sans outline-none focus:border-black transition-colors bg-white">
			</div>
			<div>
				<label class="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2">Email Address</label>
				<input type="email" class="w-full border border-[#E0E0E0] rounded-xl px-4 py-3 font-sans outline-none focus:border-black transition-colors bg-white">
			</div>
		</div>
		<div>
			<label class="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2">Inquiry Type</label>
			<select class="w-full border border-[#E0E0E0] rounded-xl px-4 py-3 font-sans outline-none focus:border-black transition-colors bg-white">
				<option>Technical Issue</option>
				<option>Manuscript Status</option>
				<option>Account Verification</option>
				<option>General Inquiry</option>
			</select>
		</div>
		<div>
			<label class="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2">Message</label>
			<textarea rows="6" class="w-full border border-[#E0E0E0] rounded-xl px-4 py-3 font-sans outline-none focus:border-black transition-colors bg-white"></textarea>
		</div>

		<button type="button" class="w-full bg-black text-white px-8 py-4 rounded-xl font-sans font-bold uppercase text-sm tracking-wide hover:bg-gray-800 transition-colors">Submit Request</button>
	</form>
</div>
<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-footer.php'; ?>
