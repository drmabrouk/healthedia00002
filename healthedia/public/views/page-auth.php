<?php
// Load custom header instead of get_header()
include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-header.php';

$maintenance_mode = get_option('healthedia_auth_maintenance', 'no');
$registration_enabled = get_option('healthedia_enable_registration', 'yes');

if ($maintenance_mode === 'yes') {
	echo '<div class="h-[calc(100vh-64px)] flex items-center justify-center bg-gray-50 text-[#111111] px-4">';
	echo '  <div class="text-center">';
	echo '    <h1 class="text-3xl font-sans font-bold uppercase tracking-tight mb-2">System Maintenance</h1>';
	echo '    <p class="font-mono text-sm text-gray-500 mb-6">Authentication services are currently offline for development.</p>';
	echo '    <p class="font-mono text-[10px] text-gray-400 uppercase tracking-widest">Redirecting to homepage...</p>';
	echo '  </div>';
	echo '  <script>setTimeout(function(){ window.location.href = "'.esc_url(home_url('/')).'"; }, 3000);</script>';
	echo '</div>';
	include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-footer.php';
	return;
}
?>

<div class="healthedia-auth flex-grow flex items-center justify-center bg-white text-[#111111] py-4 px-4 relative">
	<div class="bg-white border border-[#E0E0E0] rounded-2xl p-6 max-w-lg w-full shadow-sm relative overflow-hidden">

		<div id="auth-alerts" class="hidden mb-4 p-4 rounded font-mono text-xs text-center border"></div>

		<?php if ($registration_enabled === 'yes'): ?>
		<!-- Tabs -->
		<div class="flex bg-white rounded-xl p-1 mb-6 border border-[#E0E0E0]">
			<button id="tab-login" class="flex-1 py-2 rounded-lg font-mono text-[10px] uppercase tracking-widest text-black bg-white border border-[#E0E0E0] shadow-sm font-bold transition-all">Login</button>
			<button id="tab-register" class="flex-1 py-2 rounded-lg font-mono text-[10px] uppercase tracking-widest text-gray-400 border border-transparent hover:text-black transition-all">Create Account</button>
		</div>
		<?php else: ?>
		<div class="mb-10 text-center font-mono text-xs text-red-500 uppercase tracking-widest bg-red-50 py-2 rounded border border-red-200">
			New Registrations Disabled
		</div>
		<?php endif; ?>

		<!-- Login Form Container -->
		<div id="auth-login-container">
			<div class="text-center mb-8">
				<h2 class="text-2xl font-sans font-bold uppercase tracking-tight mb-2">Login To Archive</h2>
				<p class="font-mono text-[10px] text-gray-500 uppercase tracking-widest leading-relaxed">Access global health, physiology, and sports biomechanics indices.</p>
			</div>

			<form id="auth-form-login" class="space-y-6">
				<div>
					<input type="text" name="login" id="login-input" required placeholder="Email Address or Username" class="w-full border border-[#E0E0E0] rounded-xl px-4 py-3 font-sans text-sm outline-none focus:border-black transition-colors bg-white">
				</div>
				<div class="relative">
					<input type="password" name="password" id="login-password" required maxlength="100" placeholder="Password" class="w-full border border-[#E0E0E0] rounded-xl px-4 py-3 font-sans text-sm outline-none focus:border-black transition-colors bg-white pr-10">
					<button type="button" class="toggle-password absolute right-3 top-3 text-gray-400 hover:text-black" data-target="login-password">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
					</button>
				</div>
				<div class="flex justify-end">
					<button type="button" id="btn-forgot-password" class="font-mono text-[10px] text-gray-500 hover:text-black uppercase tracking-widest transition-colors">Forgot Password?</button>
				</div>
				<div>
					<button type="submit" id="btn-login-submit" class="w-full bg-black text-white px-6 py-4 rounded-xl font-mono uppercase text-[10px] font-bold tracking-widest hover:bg-gray-800 transition-colors flex justify-center items-center gap-2">
						<span>Sign In To Archive</span>
					</button>
				</div>
			</form>
		</div>

		<?php if ($registration_enabled === 'yes'): ?>
		<!-- Registration Form Container (Multi-step) -->
		<div id="auth-register-container" class="hidden">
			<div class="text-center mb-8">
				<h2 class="text-2xl font-sans font-bold uppercase tracking-tight mb-2">Create Investigator Account</h2>
				<p class="font-mono text-[10px] text-gray-500 uppercase tracking-widest leading-relaxed">Join our verified directory of active researchers and clinicians.</p>
			</div>

			<form id="auth-form-register" class="space-y-4">

				<!-- Step 1: Basic Info -->
				<div id="reg-step-1" class="space-y-4 block">
					<div class="flex flex-col md:flex-row gap-2">
						<select name="title" class="w-full md:w-24 border border-[#E0E0E0] rounded-xl px-2 py-3 font-sans text-sm outline-none focus:border-black transition-colors bg-white">
							<option value="Dr.">Dr.</option>
							<option value="Prof.">Prof.</option>
							<option value="Mr.">Mr.</option>
							<option value="Ms.">Ms.</option>
						</select>
						<input type="text" name="first_name" id="reg-first" required placeholder="First Name" class="w-full md:flex-1 border border-[#E0E0E0] rounded-xl px-4 py-3 font-sans text-sm outline-none focus:border-black transition-colors bg-white">
						<input type="text" name="last_name" id="reg-last" required placeholder="Last Name" class="w-full md:flex-1 border border-[#E0E0E0] rounded-xl px-4 py-3 font-sans text-sm outline-none focus:border-black transition-colors bg-white">
					</div>

					<div>
						<input type="email" name="email" id="register-email" required placeholder="Institutional Email Address" class="w-full border border-[#E0E0E0] rounded-xl px-4 py-3 font-sans text-sm outline-none focus:border-black transition-colors bg-white">
					</div>

					<div class="flex flex-col md:flex-row gap-4">
						<div class="relative w-full md:w-1/2">
							<input type="password" name="password" id="register-password" required maxlength="100" placeholder="Secure Password" class="w-full border border-[#E0E0E0] rounded-xl px-4 py-3 font-sans text-sm outline-none focus:border-black transition-colors bg-white pr-10">
							<button type="button" class="toggle-password absolute right-3 top-3 text-gray-400 hover:text-black" data-target="register-password">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
							</button>
						</div>
						<div class="relative w-full md:w-1/2">
							<input type="password" name="password_confirm" id="register-password-confirm" required maxlength="100" placeholder="Confirm Password" class="w-full border border-[#E0E0E0] rounded-xl px-4 py-3 font-sans text-sm outline-none focus:border-black transition-colors bg-white pr-10">
							<button type="button" class="toggle-password absolute right-3 top-3 text-gray-400 hover:text-black" data-target="register-password-confirm">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
							</button>
						</div>
					</div>

					<div class="pt-4">
						<button type="button" id="btn-reg-next-1" class="w-full bg-black text-white px-6 py-4 rounded-xl font-mono uppercase text-[10px] font-bold tracking-widest hover:bg-gray-800 transition-colors">
							Next: Professional Details →
						</button>
					</div>
				</div>

				<!-- Step 2: Academic Info -->
				<div id="reg-step-2" class="space-y-4 hidden">
					<div>
						<input type="text" name="specialty" id="reg-specialty" required placeholder="Primary Specialty (e.g. Physiology)" class="w-full border border-[#E0E0E0] rounded-xl px-4 py-3 font-sans text-sm outline-none focus:border-black transition-colors bg-white">
					</div>

					<div class="flex flex-col md:flex-row gap-4">
						<input type="text" name="institution" id="reg-institution" required placeholder="Institutional Affiliation" class="w-full md:w-1/2 border border-[#E0E0E0] rounded-xl px-4 py-3 font-sans text-sm outline-none focus:border-black transition-colors bg-white">
						<input type="text" name="country" id="reg-country" required placeholder="Country of Origin" class="w-full md:w-1/2 border border-[#E0E0E0] rounded-xl px-4 py-3 font-sans text-sm outline-none focus:border-black transition-colors bg-white">
					</div>

					<div>
						<input type="text" name="orcid" placeholder="ORCID Identifier iD (e.g. 0000-xxxx-xxxx-xxxx)" class="w-full border border-[#E0E0E0] rounded-xl px-4 py-3 font-mono text-xs outline-none focus:border-black transition-colors bg-white">
					</div>

					<div class="pt-4 flex gap-4">
						<button type="button" id="btn-reg-back-2" class="w-1/3 bg-gray-100 text-gray-600 px-6 py-4 rounded-xl font-mono uppercase text-[10px] font-bold tracking-widest hover:bg-gray-200 transition-colors">
							← Back
						</button>
						<button type="button" id="btn-reg-next-2" class="w-2/3 bg-black text-white px-6 py-4 rounded-xl font-mono uppercase text-[10px] font-bold tracking-widest hover:bg-gray-800 transition-colors">
							Next: Terms & Policy →
						</button>
					</div>
				</div>

				<!-- Step 3: Legal & Submit -->
				<div id="reg-step-3" class="space-y-4 hidden">
					<div class="bg-gray-50 border border-[#E0E0E0] rounded-xl p-4 font-mono text-[10px] text-gray-600 leading-relaxed uppercase tracking-widest">
						By submitting this application, you verify that you hold an active clinical practice, university research post, or laboratory affiliation.
					</div>

					<div class="pt-2 pb-2">
						<label class="flex items-start gap-3 cursor-pointer">
							<input type="checkbox" id="reg-terms-check" required class="mt-0.5 w-4 h-4 text-black border-gray-300 rounded focus:ring-black">
							<span class="font-mono text-[9px] text-gray-500 uppercase tracking-widest leading-relaxed">
								I accept the <a href="<?php echo esc_url(get_option('healthedia_terms_url', '#')); ?>" target="_blank" class="text-black hover:underline">Terms and Conditions</a> and <a href="<?php echo esc_url(get_option('healthedia_privacy_policy_url', '#')); ?>" target="_blank" class="text-black hover:underline">Privacy Policy</a>.
							</span>
						</label>
					</div>

					<input type="hidden" name="name" id="reg-full-name" value="">

					<div class="pt-4 flex gap-4">
						<button type="button" id="btn-reg-back-3" class="w-1/3 bg-gray-100 text-gray-600 px-6 py-4 rounded-xl font-mono uppercase text-[10px] font-bold tracking-widest hover:bg-gray-200 transition-colors">
							← Back
						</button>
						<button type="submit" id="btn-register-submit" class="w-2/3 bg-black text-white px-6 py-4 rounded-xl font-mono uppercase text-[10px] font-bold tracking-widest hover:bg-gray-800 transition-colors">
							Verify Email & Register
						</button>
					</div>
				</div>
			</form>
		</div>
		<?php endif; ?>

		<!-- Forgot Password Initial Request Container -->
		<div id="auth-forgot-container" class="hidden">
			<div class="text-center mb-8">
				<h2 class="text-2xl font-sans font-bold uppercase tracking-tight mb-2">Account Recovery</h2>
				<p class="font-mono text-[10px] text-gray-500 uppercase tracking-widest leading-relaxed">Enter your email address to receive a secure OTP verification code.</p>
			</div>

			<form id="auth-form-forgot" class="space-y-6">
				<input type="hidden" name="is_forgot" value="true">
				<div>
					<input type="email" name="email" id="forgot-email" required placeholder="Institutional Email Address" class="w-full border border-[#E0E0E0] rounded-xl px-4 py-3 font-sans text-sm outline-none focus:border-black transition-colors bg-white">
				</div>
				<div>
					<button type="submit" id="btn-forgot-submit" class="w-full bg-black text-white px-6 py-4 rounded-xl font-mono uppercase text-[10px] font-bold tracking-widest hover:bg-gray-800 transition-colors flex justify-center items-center gap-2">
						<span>Request OTP</span>
					</button>
				</div>
				<div class="text-center">
					<button type="button" id="btn-back-to-login" class="font-mono text-[10px] text-gray-500 hover:text-black uppercase tracking-widest transition-colors">← Back to Login</button>
				</div>
			</form>
		</div>

		<!-- Password Reset Container -->
		<div id="auth-reset-container" class="hidden">
			<div class="text-center mb-8">
				<h2 class="text-2xl font-sans font-bold uppercase tracking-tight mb-2">Secure Reset</h2>
				<p class="font-mono text-[10px] text-gray-500 uppercase tracking-widest leading-relaxed">Identity verified. Please enter a new password below.</p>
			</div>

			<form id="auth-form-reset" class="space-y-6">
				<input type="hidden" name="email" id="reset-email" value="">
				<input type="hidden" name="otp" id="reset-otp" value="">

				<div class="relative">
					<input type="password" name="password" id="reset-password" required maxlength="100" placeholder="New Password" class="w-full border border-[#E0E0E0] rounded-xl px-4 py-3 font-sans text-sm outline-none focus:border-black transition-colors bg-white pr-10">
					<button type="button" class="toggle-password absolute right-3 top-3 text-gray-400 hover:text-black" data-target="reset-password">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
					</button>
				</div>
				<div class="relative">
					<input type="password" name="password_confirm" id="reset-password-confirm" required maxlength="100" placeholder="Confirm New Password" class="w-full border border-[#E0E0E0] rounded-xl px-4 py-3 font-sans text-sm outline-none focus:border-black transition-colors bg-white pr-10">
					<button type="button" class="toggle-password absolute right-3 top-3 text-gray-400 hover:text-black" data-target="reset-password-confirm">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
					</button>
				</div>
				<div>
					<button type="submit" id="btn-reset-submit" class="w-full bg-black text-white px-6 py-4 rounded-xl font-mono uppercase text-[10px] font-bold tracking-widest hover:bg-gray-800 transition-colors flex justify-center items-center gap-2">
						<span>Reset & Login</span>
					</button>
				</div>
			</form>
		</div>

		<!-- OTP Verification Overlay Container -->
		<div id="auth-otp-container" class="absolute inset-0 bg-white z-20 flex flex-col items-center justify-center p-8 hidden transform transition-transform duration-300 translate-y-full">
			<div class="text-center w-full max-w-sm">
				<h2 class="text-2xl font-sans font-bold uppercase tracking-tight mb-2">Verify Identity</h2>
				<p class="font-mono text-[10px] text-green-700 bg-green-50 uppercase tracking-widest p-3 rounded-lg border border-green-200 mb-8">6-digit OTP sent to your inbox. Valid for 15 minutes.</p>

				<form id="auth-form-otp-verify" class="space-y-6">
					<input type="hidden" name="email" id="verify-email-input" value="">
					<input type="hidden" name="is_register" id="verify-is-register" value="false">
					<div>
						<input type="text" name="otp" id="auth-otp-page" required placeholder="000000" maxlength="6" pattern="\d{6}" class="w-full border border-black rounded-xl px-4 py-4 font-mono text-center tracking-[1em] text-2xl outline-none shadow-sm focus:ring-2 focus:ring-black">
					</div>
					<button type="submit" id="btn-verify-submit" class="w-full bg-black text-white px-6 py-4 rounded-xl font-mono uppercase text-[10px] font-bold tracking-widest hover:bg-gray-800 transition-colors">
						Confirm & Access
					</button>
				</form>
				<button type="button" id="btn-cancel-otp" class="mt-8 font-mono text-[10px] uppercase tracking-widest text-gray-400 hover:text-black hover:underline">
					← Cancel & Go Back
				</button>
			</div>
		</div>

	</div>
</div>
<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-footer.php'; ?>
