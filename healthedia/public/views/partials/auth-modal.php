<div id="healthedia-auth-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
	<div class="bg-white border border-[#E0E0E0] rounded-2xl p-8 max-w-sm w-full shadow-xl relative">
		<button id="auth-close" class="absolute top-4 right-4 text-gray-400 hover:text-black transition-colors">
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
		</button>

		<h2 class="text-2xl font-sans font-bold uppercase tracking-tight mb-2 text-center">Authenticate</h2>
		<p class="font-mono text-xs text-gray-500 text-center mb-6">Enter your academic or professional email.</p>

		<form id="auth-form-email" class="space-y-4">
			<input type="email" id="auth-email" required placeholder="name@university.edu" class="w-full border border-[#E0E0E0] rounded px-4 py-3 font-sans outline-none focus:border-black transition-colors">
			<button type="submit" class="w-full bg-black text-white px-6 py-3 rounded-full font-sans uppercase text-sm tracking-wide hover:bg-gray-800 transition-colors">Request Access Link</button>
		</form>

		<form id="auth-form-otp" class="space-y-4 hidden mt-4">
			<p class="font-mono text-xs text-gray-500 text-center mb-2">Check your inbox for the OTP.</p>
			<input type="text" id="auth-otp" required placeholder="000000" class="w-full border border-[#E0E0E0] rounded px-4 py-3 font-mono text-center tracking-widest text-lg outline-none focus:border-black transition-colors">
			<button type="submit" class="w-full bg-black text-white px-6 py-3 rounded-full font-sans uppercase text-sm tracking-wide hover:bg-gray-800 transition-colors">Verify & Login</button>
		</form>
	</div>
</div>
