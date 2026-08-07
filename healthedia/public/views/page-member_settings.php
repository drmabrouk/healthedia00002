<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-header.php'; ?>
<?php
$user_id = get_current_user_id();
$user = wp_get_current_user();
$username = get_user_meta($user_id, '_healthedia_username', true);
$first_name = get_user_meta($user_id, 'first_name', true);
$last_name = get_user_meta($user_id, 'last_name', true);
$specialty = get_user_meta($user_id, '_healthedia_specialty', true);
$institution = get_user_meta($user_id, '_healthedia_institution', true);
$country = get_user_meta($user_id, '_healthedia_country', true);
$orcid = get_user_meta($user_id, '_healthedia_orcid', true);
$privacy_mode = get_user_meta($user_id, '_healthedia_privacy_mode', true) ?: 'public';
$description = get_user_meta($user_id, 'description', true);

$public_url = $username ? home_url('/' . $username) : home_url('/profile/' . $user_id);
?>
<div class="max-w-7xl mx-auto flex flex-col md:flex-row gap-8 py-12 px-4 md:px-8 bg-white text-[#111111]">
	<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-member-sidebar.php'; ?>

	<main class="flex-grow w-full md:w-3/4">
		<h1 class="text-3xl font-sans font-bold uppercase tracking-tight mb-8 border-b border-[#E0E0E0] pb-4">Account Settings</h1>

		<div id="settings-message" class="hidden mb-6 p-4 rounded-xl font-mono text-sm border"></div>

		<form id="healthedia-settings-form" class="space-y-8" enctype="multipart/form-data">
			<!-- Privacy Controls -->
			<div class="bg-gray-50 border border-[#E0E0E0] rounded-2xl p-8">
				<h2 class="font-sans font-bold uppercase tracking-wider text-sm mb-6 flex items-center gap-2">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
					Profile Privacy & Visibility
				</h2>

				<div class="space-y-4 font-sans text-sm">
					<label class="flex items-start gap-3 cursor-pointer">
						<div class="pt-0.5">
							<input type="radio" name="_healthedia_privacy_mode" value="public" <?php checked($privacy_mode, 'public'); ?> class="w-4 h-4 text-black border-gray-300 focus:ring-black">
						</div>
						<div>
							<span class="block font-bold">Public (Visible to everyone)</span>
							<span class="block font-mono text-xs text-gray-500 mt-1">Your profile is visible in the Global Directory and via external search engines.</span>
						</div>
					</label>
					<label class="flex items-start gap-3 cursor-pointer">
						<div class="pt-0.5">
							<input type="radio" name="_healthedia_privacy_mode" value="hidden" <?php checked($privacy_mode, 'hidden'); ?> class="w-4 h-4 text-black border-gray-300 focus:ring-black">
						</div>
						<div>
							<span class="block font-bold">Hidden</span>
							<span class="block font-mono text-xs text-gray-500 mt-1">Your profile is temporarily hidden from the Global Directory and external visitors.</span>
						</div>
					</label>
					<label class="flex items-start gap-3 cursor-pointer">
						<div class="pt-0.5">
							<input type="radio" name="_healthedia_privacy_mode" value="private" <?php checked($privacy_mode, 'private'); ?> class="w-4 h-4 text-black border-gray-300 focus:ring-black">
						</div>
						<div>
							<span class="block font-bold">Private</span>
							<span class="block font-mono text-xs text-gray-500 mt-1">Your profile and academic portfolio are strictly visible only to you.</span>
						</div>
					</label>
				</div>
			</div>

			<!-- Security & Account Credentials -->
			<div class="bg-white border border-[#E0E0E0] rounded-2xl p-8">
				<h2 class="font-sans font-bold uppercase tracking-wider text-sm mb-6">Security & Account Credentials</h2>

				<div class="space-y-6">
					<div>
						<label class="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2">Email Address</label>
						<input type="email" name="user_email" value="<?php echo esc_attr($user->user_email); ?>" required class="w-full border border-[#E0E0E0] rounded-xl px-4 py-2.5 font-sans outline-none focus:border-black bg-white" title="Changing your email will require OTP verification on next login.">
						<p class="font-mono text-[10px] text-gray-400 mt-1 uppercase tracking-widest">Changing this will update your primary login credential.</p>
					</div>

					<div class="border-t border-[#E0E0E0] pt-6">
						<h3 class="font-sans font-bold uppercase tracking-wider text-xs mb-4">Change Password</h3>
						<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
							<div class="relative">
								<input type="password" name="new_password" id="setting-new-password" placeholder="New Password" class="w-full border border-[#E0E0E0] rounded-xl px-4 py-2.5 font-sans outline-none focus:border-black bg-white pr-10">
								<button type="button" class="toggle-password absolute right-3 top-3 text-gray-400 hover:text-black" data-target="setting-new-password">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
								</button>
							</div>
							<div class="relative">
								<input type="password" name="confirm_password" id="setting-confirm-password" placeholder="Confirm New Password" class="w-full border border-[#E0E0E0] rounded-xl px-4 py-2.5 font-sans outline-none focus:border-black bg-white pr-10">
								<button type="button" class="toggle-password absolute right-3 top-3 text-gray-400 hover:text-black" data-target="setting-confirm-password">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
								</button>
							</div>
						</div>
						<p class="font-mono text-[10px] text-gray-400 mt-2 uppercase tracking-widest">Leave blank to keep your current password.</p>
					</div>
				</div>
			</div>

			<!-- Public URL -->
			<div class="bg-white border border-[#E0E0E0] rounded-2xl p-8">
				<h2 class="font-sans font-bold uppercase tracking-wider text-sm mb-6">Custom Profile URL</h2>
				<label class="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2">Username Handle</label>
				<div class="flex items-stretch">
					<span class="flex items-center px-4 bg-gray-100 border border-r-0 border-[#E0E0E0] rounded-l-xl font-mono text-sm text-gray-500">healthedia.org/u/</span>
					<input type="text" name="_healthedia_username" value="<?php echo esc_attr($username ?: $user->user_login); ?>" class="flex-grow border border-[#E0E0E0] rounded-r-xl px-4 py-2.5 font-sans outline-none focus:border-black bg-white" pattern="[a-zA-Z0-9_-]+" title="Only letters, numbers, underscores, and hyphens are allowed.">
				</div>
				<p class="font-mono text-[10px] text-gray-400 mt-2 uppercase tracking-widest">Usernames can only be changed once every 7 days to maintain index integrity.</p>
				<p class="font-mono text-xs text-gray-500 mt-3">
					Current Public URL: <a href="<?php echo esc_url($public_url); ?>" class="text-black font-bold hover:underline" target="_blank"><?php echo esc_url($public_url); ?></a>
				</p>
			</div>

			<!-- Profile Information -->
			<?php if (current_user_can('request_verification')) : ?>
			<div class="bg-white border border-[#E0E0E0] rounded-2xl p-8">
				<h2 class="font-sans font-bold uppercase tracking-wider text-sm mb-6">Academic Information</h2>

				<div class="mb-8 pb-8 border-b border-[#E0E0E0]">
					<label class="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2">Profile Portrait</label>
					<div class="flex items-center gap-6">
						<div class="w-24 h-24 rounded-full bg-gray-100 border border-[#E0E0E0] overflow-hidden flex-shrink-0">
							<?php
							$photo_url = get_user_meta($user_id, '_healthedia_profile_photo', true);
							if ($photo_url) {
								echo '<img src="'.esc_url($photo_url).'" class="w-full h-full object-cover" alt="Profile">';
							} else {
								echo get_avatar($user_id, 96, '', '', array('class' => 'w-full h-full object-cover'));
							}
							?>
						</div>
						<div class="flex-grow">
							<input type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-mono file:uppercase file:tracking-widest file:bg-gray-50 file:text-black hover:file:bg-gray-100 transition-colors cursor-pointer outline-none">
							<p class="font-mono text-[10px] text-gray-500 mt-2 uppercase tracking-widest">Recommendation: A professional portrait with a plain background is preferred.</p>
						</div>
					</div>
				</div>

				<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
					<div>
						<label class="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2">First Name</label>
						<input type="text" name="first_name" value="<?php echo esc_attr($first_name); ?>" required class="w-full border border-[#E0E0E0] rounded-xl px-4 py-2.5 font-sans outline-none focus:border-black bg-white">
					</div>
					<div>
						<label class="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2">Last Name</label>
						<input type="text" name="last_name" value="<?php echo esc_attr($last_name); ?>" required class="w-full border border-[#E0E0E0] rounded-xl px-4 py-2.5 font-sans outline-none focus:border-black bg-white">
					</div>
					<div>
						<label class="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2">Primary Specialty</label>
						<input type="text" name="_healthedia_specialty" value="<?php echo esc_attr($specialty); ?>" placeholder="e.g. Sports Science & Physiology" class="w-full border border-[#E0E0E0] rounded-xl px-4 py-2.5 font-sans outline-none focus:border-black bg-white">
					</div>
					<div>
						<label class="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2">Institution / Affiliated Lab</label>
						<input type="text" name="_healthedia_institution" value="<?php echo esc_attr($institution); ?>" placeholder="e.g. Sydney University" class="w-full border border-[#E0E0E0] rounded-xl px-4 py-2.5 font-sans outline-none focus:border-black bg-white">
					</div>
					<div>
						<label class="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2">Country of Origin</label>
						<input type="text" name="_healthedia_country" value="<?php echo esc_attr($country); ?>" placeholder="e.g. Australia" class="w-full border border-[#E0E0E0] rounded-xl px-4 py-2.5 font-sans outline-none focus:border-black bg-white">
					</div>
					<div>
						<label class="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2">ORCID ID</label>
						<input type="text" name="_healthedia_orcid" value="<?php echo esc_attr($orcid); ?>" placeholder="0000-0000-0000-0000" class="w-full border border-[#E0E0E0] rounded-xl px-4 py-2.5 font-mono text-sm outline-none focus:border-black bg-white">
					</div>
				</div>

				<div>
					<label class="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2">Professional Biography (Abstract)</label>
					<textarea name="description" rows="5" class="w-full border border-[#E0E0E0] rounded-xl px-4 py-2.5 font-sans outline-none focus:border-black bg-white resize-y"><?php echo esc_textarea($description); ?></textarea>
				</div>
			</div>

			<div class="flex justify-end mt-8">
				<button type="submit" class="bg-black text-white px-8 py-3 rounded-full font-sans uppercase text-sm tracking-wide hover:bg-gray-800 transition-colors flex items-center gap-2">
					<svg class="w-4 h-4 hidden" id="settings-spinner" class="animate-spin" fill="none" viewBox="0 0 24 24">
						<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
						<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
					</svg>
					Save Changes
				</button>
			</div>
			<?php endif; ?>
			<?php if (!current_user_can('request_verification')) : ?>
			<div class="flex justify-end mt-8">
				<button type="submit" class="bg-black text-white px-8 py-3 rounded-full font-sans uppercase text-sm tracking-wide hover:bg-gray-800 transition-colors flex items-center gap-2">
					<svg class="w-4 h-4 hidden" id="settings-spinner" class="animate-spin" fill="none" viewBox="0 0 24 24">
						<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
						<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
					</svg>
					Save Changes
				</button>
			</div>
			<?php endif; ?>
		</form>

		<!-- Verification Request Workflow -->
		<?php if (current_user_can('request_verification')) : ?>
		<div class="mt-12 bg-gray-50 border border-[#E0E0E0] rounded-2xl p-8">
			<h2 class="font-sans font-bold uppercase tracking-wider text-sm mb-2 flex items-center gap-2">
				<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
				Account Verification Request
			</h2>

			<?php if (get_user_meta($user_id, '_healthedia_verified', true) === '1'): ?>
				<p class="font-mono text-sm text-green-600 uppercase tracking-widest mt-4">Your account is fully verified.</p>
			<?php elseif (get_user_meta($user_id, '_healthedia_verification_status', true) === 'pending'): ?>
				<p class="font-mono text-sm text-yellow-600 uppercase tracking-widest mt-4">Your verification request is currently under review by the Editorial Board.</p>
			<?php else: ?>
				<p class="font-sans text-sm text-gray-600 mb-6 leading-relaxed">
					To receive the Verified Badge (✔) on your public profile, you must undergo a secure identity verification process.
					Please ensure the following before submitting:
				</p>
				<ul class="list-disc list-inside font-mono text-xs text-gray-500 mb-6 space-y-2 ml-4">
					<li>Your profile information above is fully completed.</li>
					<li>Your email address belongs to an official institutional or academic domain.</li>
					<li>All academic and professional information is accurate.</li>
					<li>You provide a valid National ID or Passport below.</li>
				</ul>

				<div id="verification-message" class="hidden mb-6 p-4 rounded-xl font-mono text-sm border"></div>

				<form id="healthedia-verification-form" class="space-y-6" enctype="multipart/form-data">
					<div class="border border-[#E0E0E0] rounded-xl p-6 bg-white">
						<label class="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2">Upload Identity Document (National ID / Passport)</label>
						<input type="file" name="identity_document" required accept=".pdf,image/jpeg,image/png,image/webp" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-mono file:uppercase file:tracking-widest file:bg-gray-50 file:text-black hover:file:bg-gray-100 transition-colors cursor-pointer outline-none">
					</div>
					<div class="flex justify-start">
						<button type="submit" class="bg-black text-white px-8 py-3 rounded-full font-sans uppercase text-sm tracking-wide hover:bg-gray-800 transition-colors flex items-center gap-2">
							<svg class="w-4 h-4 hidden" id="verification-spinner" class="animate-spin" fill="none" viewBox="0 0 24 24">
								<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
								<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
							</svg>
							Submit Request
						</button>
					</div>
				</form>
			<?php endif; ?>
		</div>
			<?php endif; ?>
	</main>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
	const form = document.getElementById('healthedia-settings-form');
	const msgBox = document.getElementById('settings-message');
	const spinner = document.getElementById('settings-spinner');

	if (form) {
		form.addEventListener('submit', async (e) => {
			e.preventDefault();
			const formData = new FormData(form);

			msgBox.classList.add('hidden');
			spinner.classList.remove('hidden');

			try {
				const res = await fetch('/wp-json/healthedia/v1/profile', {
					method: 'POST',
					headers: {
						'X-WP-Nonce': '<?php echo wp_create_nonce("wp_rest"); ?>'
					},
					body: formData
				});

				const result = await res.json();

				if (res.ok) {
					msgBox.textContent = result.message || 'Settings saved successfully.';
					msgBox.className = 'mb-6 p-4 rounded-xl font-mono text-sm border border-black bg-gray-50 text-black';
				} else {
					msgBox.textContent = result.message || 'An error occurred.';
					msgBox.className = 'mb-6 p-4 rounded-xl font-mono text-sm border border-red-500 bg-red-50 text-red-700';
				}
			} catch (err) {
				msgBox.textContent = 'Network error. Please try again.';
				msgBox.className = 'mb-6 p-4 rounded-xl font-mono text-sm border border-red-500 bg-red-50 text-red-700';
			} finally {
				msgBox.classList.remove('hidden');
				spinner.classList.add('hidden');
				window.scrollTo({top: 0, behavior: 'smooth'});
			}
		});
	}

	const vForm = document.getElementById('healthedia-verification-form');
	const vMsgBox = document.getElementById('verification-message');
	const vSpinner = document.getElementById('verification-spinner');

	if (vForm) {
		vForm.addEventListener('submit', async (e) => {
			e.preventDefault();
			const formData = new FormData(vForm);

			vMsgBox.classList.add('hidden');
			vSpinner.classList.remove('hidden');

			try {
				const res = await fetch('/wp-json/healthedia/v1/profile/verify-request', {
					method: 'POST',
					headers: {
						'X-WP-Nonce': '<?php echo wp_create_nonce("wp_rest"); ?>'
					},
					body: formData
				});

				const result = await res.json();

				if (res.ok) {
					vForm.innerHTML = '<p class="font-mono text-sm text-yellow-600 uppercase tracking-widest mt-4">Your verification request has been successfully submitted and is under review.</p>';
				} else {
					vMsgBox.textContent = result.message || 'An error occurred.';
					vMsgBox.className = 'mb-6 p-4 rounded-xl font-mono text-sm border border-red-500 bg-red-50 text-red-700';
					vMsgBox.classList.remove('hidden');
				}
			} catch (err) {
				vMsgBox.textContent = 'Network error. Please try again.';
				vMsgBox.className = 'mb-6 p-4 rounded-xl font-mono text-sm border border-red-500 bg-red-50 text-red-700';
				vMsgBox.classList.remove('hidden');
			} finally {
				vSpinner.classList.add('hidden');
			}
		});
	}
});
</script>
<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-footer.php'; ?>
