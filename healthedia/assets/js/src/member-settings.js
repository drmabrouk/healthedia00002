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
						'X-WP-Nonce': window.healthediaPublicSettings?.nonce || ''
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
						'X-WP-Nonce': window.healthediaPublicSettings?.nonce || ''
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
