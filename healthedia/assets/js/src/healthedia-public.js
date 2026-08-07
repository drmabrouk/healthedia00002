document.addEventListener('DOMContentLoaded', () => {

	// Voice Search Logic for Gateway
	const btnVoiceSearch = document.getElementById('btn-voice-search');
	const gatewaySearchInput = document.getElementById('gateway-search-input');

	if (btnVoiceSearch && gatewaySearchInput) {
		const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
		if (typeof SpeechRecognition !== 'undefined') {
			const recognition = new SpeechRecognition();
			recognition.lang = 'en-US';
			recognition.interimResults = false;
			recognition.maxAlternatives = 1;

			btnVoiceSearch.addEventListener('click', () => {
				btnVoiceSearch.classList.replace('text-gray-400', 'text-red-500');
				btnVoiceSearch.classList.add('animate-pulse');
				recognition.start();
			});

			recognition.addEventListener('result', (e) => {
				const transcript = e.results[0][0].transcript;
				gatewaySearchInput.value = transcript;
				gatewaySearchInput.closest('form').submit();
			});

			recognition.addEventListener('end', () => {
				btnVoiceSearch.classList.replace('text-red-500', 'text-gray-400');
				btnVoiceSearch.classList.remove('animate-pulse');
			});

			recognition.addEventListener('error', (e) => {
				btnVoiceSearch.classList.replace('text-red-500', 'text-gray-400');
				btnVoiceSearch.classList.remove('animate-pulse');
				console.error('Voice search error:', e.error);
			});
		} else {
			btnVoiceSearch.classList.add('hidden'); // Hide if not supported
		}
	}

	// Search History & Autocomplete Logic
	const searchHistoryKey = 'healthedia_search_history';
	const archiveSearchInput = document.getElementById('archive-search-input') || gatewaySearchInput;
	const searchHistoryDropdown = document.getElementById('search-history-dropdown') || document.getElementById('search-history-container');
	const searchHistoryList = document.getElementById('search-history-list');
	const btnClearHistory = document.getElementById('btn-clear-history');

	const getSearchHistory = () => {
		try { return JSON.parse(localStorage.getItem(searchHistoryKey)) || []; }
		catch(e) { return []; }
	};

	const saveSearchHistory = (query) => {
		if (!query.trim()) return;
		let history = getSearchHistory();
		history = history.filter(q => q.toLowerCase() !== query.toLowerCase()); // Remove duplicates
		history.unshift(query.trim()); // Add to top
		if (history.length > 3) history = history.slice(0, 3); // Keep last 3 searches only
		localStorage.setItem(searchHistoryKey, JSON.stringify(history));
	};

	const removeSearchHistory = (query) => {
		let history = getSearchHistory();
		history = history.filter(q => q.toLowerCase() !== query.toLowerCase());
		localStorage.setItem(searchHistoryKey, JSON.stringify(history));
		renderSearchHistory();
	};

	const renderSearchHistory = () => {
		if (!searchHistoryList) return;
		const history = getSearchHistory();
		if (history.length === 0) {
			searchHistoryList.innerHTML = '<li class="px-6 py-4 font-mono text-[10px] text-gray-400 uppercase tracking-widest text-center">No recent searches</li>';
			return;
		}

		searchHistoryList.innerHTML = history.map(q => `
			<li class="history-item px-6 py-3 font-sans text-sm hover:bg-gray-50 flex items-center justify-between transition-colors border-b border-[#E0E0E0] last:border-0 group" data-query="${escapeHTML(q)}">
				<div class="flex items-center gap-3 cursor-pointer flex-grow history-text">
					<svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
					${escapeHTML(q)}
				</div>
				<button type="button" class="history-remove text-gray-300 hover:text-red-500 transition-colors p-1" data-query="${escapeHTML(q)}">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
				</button>
			</li>
		`).join('');

		document.querySelectorAll('.history-text').forEach(item => {
			item.addEventListener('click', function(e) {
				archiveSearchInput.value = this.closest('.history-item').getAttribute('data-query');
				if (searchHistoryDropdown) searchHistoryDropdown.classList.add('hidden');
				if (archiveSearchInput.closest('form')) {
					archiveSearchInput.closest('form').submit();
				} else {
					const btnUpdate = document.getElementById('btn-update-search');
					if (btnUpdate) btnUpdate.click();
				}
			});
		});

		document.querySelectorAll('.history-remove').forEach(btn => {
			btn.addEventListener('click', function(e) {
				e.stopPropagation();
				removeSearchHistory(this.getAttribute('data-query'));
			});
		});
	};

	if (archiveSearchInput) {
		archiveSearchInput.addEventListener('focus', () => {
			if (searchHistoryDropdown) {
				renderSearchHistory();
				searchHistoryDropdown.classList.remove('hidden');
			}
		});

		// Track form submissions
		const form = archiveSearchInput.closest('form');
		if (form) {
			form.addEventListener('submit', () => saveSearchHistory(archiveSearchInput.value));
		}

		const btnUpdate = document.getElementById('btn-update-search');
		if (btnUpdate) {
			btnUpdate.addEventListener('click', () => saveSearchHistory(archiveSearchInput.value));
		}

		// Autocomplete suggestions (mock implementation based on typing)
		const autocompleteContainer = document.getElementById('autocomplete-suggestions');
		if (autocompleteContainer) {
			archiveSearchInput.addEventListener('input', (e) => {
				const val = e.target.value.trim().toLowerCase();
				if (val.length > 2) {
					// Dummy suggestions
					const words = ['oncology', 'biomechanics', 'trials', 'verified', 'research', 'cardiology'];
					const matches = words.filter(w => w.includes(val));

					if (matches.length > 0) {
						if (searchHistoryList) searchHistoryList.classList.add('hidden');
						autocompleteContainer.classList.remove('hidden');

						autocompleteContainer.innerHTML = matches.map(m => `
							<div class="px-6 py-3 font-sans text-sm hover:bg-gray-50 cursor-pointer flex items-center gap-3 transition-colors autocomplete-item border-b border-[#E0E0E0] last:border-0" data-query="${escapeHTML(m)}">
								<svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
								${escapeHTML(m)}
							</div>
						`).join('');

						autocompleteContainer.querySelectorAll('.autocomplete-item').forEach(item => {
							item.addEventListener('click', function(ev) {
								archiveSearchInput.value = this.getAttribute('data-query');
								saveSearchHistory(archiveSearchInput.value);
								if (searchHistoryDropdown) searchHistoryDropdown.classList.add('hidden');
								if (archiveSearchInput.closest('form')) archiveSearchInput.closest('form').submit();
							});
						});
					} else {
						autocompleteContainer.classList.add('hidden');
						if (searchHistoryList) searchHistoryList.classList.remove('hidden');
					}
				} else {
					autocompleteContainer.classList.add('hidden');
					if (searchHistoryList) searchHistoryList.classList.remove('hidden');
				}
			});
		}

		// Close dropdown when clicking outside
		document.addEventListener('click', (e) => {
			if (searchHistoryDropdown && !archiveSearchInput.contains(e.target) && !searchHistoryDropdown.contains(e.target)) {
				searchHistoryDropdown.classList.add('hidden');
			}
		});
	}

	if (btnClearHistory) {
		btnClearHistory.addEventListener('click', () => {
			localStorage.removeItem(searchHistoryKey);
			renderSearchHistory();
		});
	}

	// Search Suggestion Tags
	document.querySelectorAll('.search-tag').forEach(tag => {
		tag.addEventListener('click', function() {
			if (archiveSearchInput) {
				const query = this.innerText.trim();
				archiveSearchInput.value = query;
				saveSearchHistory(query);
				if (archiveSearchInput.closest('form')) {
					archiveSearchInput.closest('form').submit();
				} else {
					const btnUpdate = document.getElementById('btn-update-search');
					if (btnUpdate) btnUpdate.click();
				}
			}
		});
	});

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

	// Universal Submission Logic (Articles, Research, Journal)
	const submitForms = ['form-submit-article', 'form-submit-research', 'form-submit-journal'];
	submitForms.forEach(formId => {
		const form = document.getElementById(formId);
		if (form) {
			const fileInput = document.getElementById('ms-file');
			const fileNameDisplay = document.getElementById('ms-file-name');

			if (fileInput && fileNameDisplay) {
				fileInput.addEventListener('change', (e) => {
					if (e.target.files.length > 0) {
						fileNameDisplay.innerText = 'Selected: ' + e.target.files[0].name;
						fileNameDisplay.classList.add('text-black', 'font-bold');
					}
				});
			}

			form.addEventListener('submit', (e) => {
				e.preventDefault();
				const btn = document.getElementById('btn-submit-ms');
				const status = document.getElementById('ms-status');
				const originalBtnText = btn.innerText;

				btn.innerText = 'Uploading...';
				btn.disabled = true;
				status.classList.add('hidden');

				const formData = new FormData();
				const typeInput = document.getElementById('ms-type');
				formData.append('type', typeInput ? typeInput.value : 'healthedia_post');
				formData.append('title', document.getElementById('ms-title').value);

				const abstractInput = document.getElementById('ms-abstract');
				if (abstractInput) formData.append('abstract', abstractInput.value);

				const specialtyInput = document.getElementById('ms-specialty');
				if (specialtyInput) formData.append('specialty', specialtyInput.value);

				const nctInput = document.getElementById('ms-nct');
				if (nctInput) formData.append('nct', nctInput.value);

				if (fileInput && fileInput.files.length > 0) {
					formData.append('manuscript', fileInput.files[0]);
				}

				const nonce = window.healthediaPublicSettings?.nonce || '';

				fetch('/wp-json/healthedia/v1/manuscript/submit', {
					method: 'POST',
					headers: { 'X-WP-Nonce': nonce },
					body: formData
				})
				.then(res => res.json())
				.then(data => {
					btn.innerText = originalBtnText;
					btn.disabled = false;
					status.classList.remove('hidden', 'bg-red-50', 'text-red-600');

					if (data.success) {
						status.classList.add('bg-green-50', 'text-green-700', 'border', 'border-green-200');
						status.innerText = data.message;
						form.reset();
						if (fileNameDisplay) {
							fileNameDisplay.innerText = 'Upload successful.';
							fileNameDisplay.classList.remove('text-black', 'font-bold');
						}
					} else {
						status.classList.add('bg-red-50', 'text-red-600', 'border', 'border-red-200');
						status.innerText = data.message || 'Submission failed.';
					}
				})
				.catch(() => {
					btn.innerText = originalBtnText;
					btn.disabled = false;
					status.classList.remove('hidden');
					status.classList.add('bg-red-50', 'text-red-600', 'border', 'border-red-200');
					status.innerText = 'Network error during upload.';
				});
			});
		}
	});

	// Mobile Off-Canvas Menu
	const mobileMenuBtn = document.getElementById('mobile-menu-btn');
	const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
	let menuOpen = false;

	if (mobileMenuBtn && mobileMenuOverlay) {
		mobileMenuBtn.addEventListener('click', () => {
			menuOpen = !menuOpen;
			if (menuOpen) {
				mobileMenuOverlay.classList.remove('translate-x-full');
				document.body.style.overflow = 'hidden'; // Prevent background scrolling
				mobileMenuBtn.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
			} else {
				mobileMenuOverlay.classList.add('translate-x-full');
				document.body.style.overflow = '';
				mobileMenuBtn.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>';
			}
		});
	}


	// Accessibility Typography (Zoom)
	const articleContent = document.querySelector('.article-content');
	if (articleContent) {
		const btnIn = document.getElementById('btn-zoom-in');
		const btnOut = document.getElementById('btn-zoom-out');
		let currentZoom = 1;

		btnIn.addEventListener('click', () => {
			if (currentZoom < 1.5) { currentZoom += 0.1; articleContent.style.transform = `scale(${currentZoom})`; articleContent.style.transformOrigin = 'top left'; }
		});
		btnOut.addEventListener('click', () => {
			if (currentZoom > 0.8) { currentZoom -= 0.1; articleContent.style.transform = `scale(${currentZoom})`; articleContent.style.transformOrigin = 'top left'; }
		});
	}

	// Directory Grid Fetching
	const dirGrid = document.getElementById('directory-grid');
	const dirCount = document.getElementById('dir-count');
	if (dirGrid) {
		fetch('/wp-json/healthedia/v1/directories/researchers')
			.then(res => res.json())
			.then(response => {
				dirGrid.innerHTML = '';
				if (dirCount) dirCount.innerText = response.total || 0;
				if (!response.data || response.data.length === 0) {
					dirGrid.innerHTML = '<div class="col-span-full text-center py-12 font-mono text-sm text-gray-500">No researchers found.</div>';
					return;
				}

				response.data.forEach(user => {
					const card = document.createElement('div');
					card.className = 'border border-[#E0E0E0] rounded-2xl p-8 hover:border-black transition-colors bg-white shadow-sm flex flex-col relative h-full';
					card.innerHTML = `
						${user.verified ? '<div class="absolute top-6 right-6" title="Verified Researcher"><svg class="w-5 h-5 text-black" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg></div>' : ''}

						<div class="flex items-center gap-5 mb-6">
							<div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 font-sans font-bold text-2xl border border-[#E0E0E0] shrink-0">
								${escapeHTML(user.name).charAt(0)}
							</div>
							<div>
								<a href="${user.url}" class="font-sans font-bold text-lg hover:underline line-clamp-1 block text-black">${escapeHTML(user.name)}</a>
								<div class="font-mono text-[10px] text-gray-500 uppercase tracking-widest mt-1 line-clamp-1">${escapeHTML(user.specialty || 'Independent')}</div>
							</div>
						</div>

						<div class="grid grid-cols-2 gap-3 mb-6 font-mono text-[10px] uppercase tracking-widest border-t border-b border-[#E0E0E0] py-4 mt-auto">
							<div>
								<div class="text-gray-400 mb-1">Publications</div>
								<div class="text-black font-bold text-base font-sans leading-none">0</div>
							</div>
							<div>
								<div class="text-gray-400 mb-1">Total Views</div>
								<div class="text-black font-bold text-base font-sans leading-none">${user.views}</div>
							</div>
						</div>

						<a href="${user.url}" class="w-full block text-center border border-[#E0E0E0] rounded-full py-2.5 font-mono text-[10px] uppercase tracking-widest hover:border-black hover:bg-gray-50 transition-colors text-black font-bold">
							View Academic Profile
						</a>
					`;
					dirGrid.appendChild(card);
				});
			})
			.catch(() => {
				dirGrid.innerHTML = '<div class="col-span-full text-center py-12 text-red-500 font-mono text-sm">Error loading directory.</div>';
			});
	}

	// Dedicated Auth Page Logic (Redesigned Unified Layout)
	const loginContainer = document.getElementById('auth-login-container');
	const registerContainer = document.getElementById('auth-register-container');
	const otpContainer = document.getElementById('auth-otp-container');
	const alerts = document.getElementById('auth-alerts');

	if (loginContainer) {
		const tabLogin = document.getElementById('tab-login');
		const tabRegister = document.getElementById('tab-register');
		const verifyEmailInput = document.getElementById('verify-email-input');
		const verifyIsRegister = document.getElementById('verify-is-register');
		const btnCancelOtp = document.getElementById('btn-cancel-otp');

		const formLogin = document.getElementById('auth-form-login');
		const formRegister = document.getElementById('auth-form-register');
		const formOtpVerify = document.getElementById('auth-form-otp-verify');

		const forgotContainer = document.getElementById('auth-forgot-container');
		const resetContainer = document.getElementById('auth-reset-container');
		const formForgot = document.getElementById('auth-form-forgot');
		const formReset = document.getElementById('auth-form-reset');
		const btnForgotPassword = document.getElementById('btn-forgot-password');
		const btnBackToLogin = document.getElementById('btn-back-to-login');

		const showAlert = (msg, isError = true) => {
			alerts.classList.remove('hidden');
			if (isError) {
				alerts.className = 'mb-6 p-4 rounded font-mono text-xs text-center border border-red-200 bg-red-50 text-red-600';
			} else {
				alerts.className = 'mb-6 p-4 rounded font-mono text-xs text-center border border-green-200 bg-green-50 text-green-700';
			}
			alerts.innerText = msg;
		};

		if (tabLogin && tabRegister) {
			tabLogin.addEventListener('click', () => {
				tabLogin.classList.replace('text-gray-400', 'text-black');
				tabLogin.classList.replace('border-transparent', 'border-[#E0E0E0]');
				tabLogin.classList.add('bg-white', 'shadow-sm', 'font-bold');

				tabRegister.classList.replace('text-black', 'text-gray-400');
				tabRegister.classList.replace('border-[#E0E0E0]', 'border-transparent');
				tabRegister.classList.remove('bg-white', 'shadow-sm', 'font-bold');

				loginContainer.classList.remove('hidden');
				registerContainer.classList.add('hidden');
				alerts.classList.add('hidden');
			});

			tabRegister.addEventListener('click', () => {
				tabRegister.classList.replace('text-gray-400', 'text-black');
				tabRegister.classList.replace('border-transparent', 'border-[#E0E0E0]');
				tabRegister.classList.add('bg-white', 'shadow-sm', 'font-bold');

				tabLogin.classList.replace('text-black', 'text-gray-400');
				tabLogin.classList.replace('border-[#E0E0E0]', 'border-transparent');
				tabLogin.classList.remove('bg-white', 'shadow-sm', 'font-bold');

				registerContainer.classList.remove('hidden');
				loginContainer.classList.add('hidden');
				alerts.classList.add('hidden');
			});
		}

		const requestOtp = (form, isReg, isForgot = false) => {
			let btnId = 'btn-login-submit';
			if (isReg) btnId = 'btn-register-submit';
			if (isForgot) btnId = 'btn-forgot-submit';

			const btn = document.getElementById(btnId);
			const originalText = btn.innerHTML;
			btn.innerHTML = '<span class="animate-pulse">Processing...</span>';
			btn.disabled = true;
			alerts.classList.add('hidden');

			const formData = new FormData(form);
			const data = Object.fromEntries(formData.entries());
			data.is_register = isReg;

			fetch('/wp-json/healthedia/v1/auth/request-otp', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify(data)
			}).then(res => res.json()).then(resData => {
				btn.innerHTML = originalText;
				btn.disabled = false;
				if (resData.success) {
					verifyEmailInput.value = data.email;
					verifyIsRegister.value = isReg;
					if (isForgot) verifyIsRegister.value = 'forgot';

					otpContainer.classList.remove('hidden', 'translate-y-full');
					setTimeout(() => document.getElementById('auth-otp-page').focus(), 300);
				} else {
					showAlert(resData.message || 'Error requesting OTP.');
				}
			}).catch(() => {
				btn.innerHTML = originalText;
				btn.disabled = false;
				showAlert('Network error communicating with authentication server.');
			});
		};

		if (btnForgotPassword && btnBackToLogin) {
			btnForgotPassword.addEventListener('click', () => {
				loginContainer.classList.add('hidden');
				forgotContainer.classList.remove('hidden');
				alerts.classList.add('hidden');
			});
			btnBackToLogin.addEventListener('click', () => {
				forgotContainer.classList.add('hidden');
				loginContainer.classList.remove('hidden');
				alerts.classList.add('hidden');
			});
		}

		if (formLogin) {
			formLogin.addEventListener('submit', (e) => {
				e.preventDefault();
				const btn = document.getElementById('btn-login-submit');
				const originalText = btn.innerHTML;
				btn.innerHTML = '<span class="animate-pulse">Authenticating...</span>';
				btn.disabled = true;
				alerts.classList.add('hidden');

				const formData = new FormData(formLogin);
				const data = Object.fromEntries(formData.entries());

				fetch('/wp-json/healthedia/v1/auth/login-standard', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify(data)
				}).then(res => res.json()).then(resData => {
					if (resData.success) {
						window.location.href = '/';
					} else {
						btn.innerHTML = originalText;
						btn.disabled = false;
						showAlert(resData.message || 'Invalid credentials.');
					}
				}).catch(() => {
					btn.innerHTML = originalText;
					btn.disabled = false;
					showAlert('Network error communicating with authentication server.');
				});
			});
		}

		// Registration Multi-step UI Logic
		const btnRegNext1 = document.getElementById('btn-reg-next-1');
		const btnRegNext2 = document.getElementById('btn-reg-next-2');
		const btnRegBack2 = document.getElementById('btn-reg-back-2');
		const btnRegBack3 = document.getElementById('btn-reg-back-3');
		const regStep1 = document.getElementById('reg-step-1');
		const regStep2 = document.getElementById('reg-step-2');
		const regStep3 = document.getElementById('reg-step-3');

		if (btnRegNext1 && regStep1 && regStep2 && regStep3) {
			btnRegNext1.addEventListener('click', () => {
				const pw1 = document.getElementById('register-password').value;
				const pw2 = document.getElementById('register-password-confirm').value;
				if (!document.getElementById('reg-first').value || !document.getElementById('reg-last').value || !document.getElementById('register-email').value || !pw1 || !pw2) {
					showAlert('Please complete all fields.');
					return;
				}
				if (pw1 !== pw2) {
					showAlert('Passwords do not match.');
					return;
				}
				alerts.classList.add('hidden');
				regStep1.classList.add('hidden');
				regStep1.classList.remove('block');
				regStep2.classList.remove('hidden');
				regStep2.classList.add('block');
			});

			btnRegNext2.addEventListener('click', () => {
				if (!document.getElementById('reg-specialty').value || !document.getElementById('reg-institution').value || !document.getElementById('reg-country').value) {
					showAlert('Please complete all professional details.');
					return;
				}
				alerts.classList.add('hidden');
				regStep2.classList.add('hidden');
				regStep2.classList.remove('block');
				regStep3.classList.remove('hidden');
				regStep3.classList.add('block');

				// Set hidden full name for payload
				const title = document.querySelector('select[name="title"]').value;
				const first = document.getElementById('reg-first').value;
				const last = document.getElementById('reg-last').value;
				document.getElementById('reg-full-name').value = `${title} ${first} ${last}`;
			});

			btnRegBack2.addEventListener('click', () => {
				regStep2.classList.add('hidden');
				regStep2.classList.remove('block');
				regStep1.classList.remove('hidden');
				regStep1.classList.add('block');
				alerts.classList.add('hidden');
			});

			btnRegBack3.addEventListener('click', () => {
				regStep3.classList.add('hidden');
				regStep3.classList.remove('block');
				regStep2.classList.remove('hidden');
				regStep2.classList.add('block');
				alerts.classList.add('hidden');
			});
		}

		if (formRegister) {
			formRegister.addEventListener('submit', (e) => {
				e.preventDefault();
				if (!document.getElementById('reg-terms-check').checked) {
					showAlert('You must accept the Terms and Privacy Policy.');
					return;
				}
				requestOtp(formRegister, true, false);
			});
		}

		if (formForgot) {
			formForgot.addEventListener('submit', (e) => {
				e.preventDefault();
				requestOtp(formForgot, false, true);
			});
		}

		if (formOtpVerify) {
			formOtpVerify.addEventListener('submit', (e) => {
				e.preventDefault();
				const btn = document.getElementById('btn-verify-submit');
				const originalText = btn.innerHTML;
				btn.innerHTML = '<span class="animate-pulse">Verifying...</span>';
				btn.disabled = true;
				alerts.classList.add('hidden');

				const formData = new FormData(formOtpVerify);
				const data = Object.fromEntries(formData.entries());

				fetch('/wp-json/healthedia/v1/auth/verify-otp', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify(data)
				}).then(res => res.json()).then(resData => {
					btn.innerHTML = originalText;
					btn.disabled = false;

					if (resData.success) {
						if (data.is_register === 'forgot') {
							// Transition to reset password UI
							otpContainer.classList.add('hidden');
							forgotContainer.classList.add('hidden');
							resetContainer.classList.remove('hidden');
							document.getElementById('reset-email').value = data.email;
							document.getElementById('reset-otp').value = data.otp;
							document.getElementById('auth-otp-page').value = '';
							showAlert('Identity verified. Please set your new password.', false);
						} else {
							window.location.href = '/';
						}
					} else {
						document.getElementById('auth-otp-page').value = '';
						showAlert(resData.message || 'Invalid OTP.');
						otpContainer.classList.add('translate-y-full');
						setTimeout(() => otpContainer.classList.add('hidden'), 300);
					}
				}).catch(() => {
					btn.innerHTML = originalText;
					btn.disabled = false;
					showAlert('Network error verifying OTP.');
					otpContainer.classList.add('translate-y-full');
					setTimeout(() => otpContainer.classList.add('hidden'), 300);
				});
			});
		}

		if (formReset) {
			formReset.addEventListener('submit', (e) => {
				e.preventDefault();
				const btn = document.getElementById('btn-reset-submit');
				const originalText = btn.innerHTML;
				btn.innerHTML = '<span class="animate-pulse">Saving...</span>';
				btn.disabled = true;
				alerts.classList.add('hidden');

				const formData = new FormData(formReset);
				const data = Object.fromEntries(formData.entries());

				if (data.password !== data.password_confirm) {
					showAlert('Passwords do not match.');
					btn.innerHTML = originalText;
					btn.disabled = false;
					return;
				}

				fetch('/wp-json/healthedia/v1/auth/reset-password', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify(data)
				}).then(res => res.json()).then(resData => {
					if (resData.success) {
						window.location.href = '/';
					} else {
						btn.innerHTML = originalText;
						btn.disabled = false;
						showAlert(resData.message || 'Error resetting password.');
					}
				}).catch(() => {
					btn.innerHTML = originalText;
					btn.disabled = false;
					showAlert('Network error.');
				});
			});
		}

		if (btnCancelOtp) {
			btnCancelOtp.addEventListener('click', () => {
				otpContainer.classList.add('translate-y-full');
				setTimeout(() => {
					otpContainer.classList.add('hidden');
					document.getElementById('auth-otp-page').value = '';
				}, 300);
			});
		}
	}

	// Password Toggle Logic
	document.querySelectorAll('.toggle-password').forEach(btn => {
		btn.addEventListener('click', function() {
			const targetId = this.getAttribute('data-target');
			const input = document.getElementById(targetId);
			if (input) {
				if (input.type === 'password') {
					input.type = 'text';
					this.innerHTML = '<svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m0 0a10.05 10.05 0 015.71-1.58c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>';
				} else {
					input.type = 'password';
					this.innerHTML = '<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>';
				}
			}
		});
	});

	// Notifications Dropdown Logic
	const btnNotifications = document.getElementById('btn-notifications');
	const notificationsDropdown = document.getElementById('notifications-dropdown');
	const notificationsList = document.getElementById('notifications-list');
	const notificationBadge = document.getElementById('notification-badge');
	const btnMarkAllRead = document.getElementById('btn-mark-all-read');

	if (btnNotifications && notificationsDropdown) {
		let notificationsOpen = false;

		const fetchNotifications = async () => {
			if (!window.healthediaPublicSettings || !window.healthediaPublicSettings.nonce) return;
			try {
				const res = await fetch('/wp-json/healthedia/v1/notifications', {
					headers: { 'X-WP-Nonce': window.healthediaPublicSettings.nonce }
				});
				if (!res.ok) return;
				const data = await res.json();

				const unreadCount = data.filter(n => !n.is_read).length;
				if (unreadCount > 0) {
					notificationBadge.classList.remove('hidden');
				} else {
					notificationBadge.classList.add('hidden');
				}

				if (data.length === 0) {
					notificationsList.innerHTML = '<div class="p-4 text-center text-gray-500 font-mono text-xs">No notifications.</div>';
					return;
				}

				notificationsList.innerHTML = data.map(n => `
					<div class="p-4 flex flex-col gap-1 ${n.is_read ? 'opacity-60' : 'bg-blue-50/30'}">
						<div class="flex justify-between items-start gap-2">
							<a href="${n.link || '#'}" class="font-sans text-sm ${n.is_read ? 'text-gray-700' : 'text-black font-bold hover:underline'}">${escapeHTML(n.message)}</a>
							${!n.is_read ? '<span class="w-2 h-2 bg-blue-500 rounded-full shrink-0 mt-1.5"></span>' : ''}
						</div>
						<div class="font-mono text-[10px] text-gray-400 uppercase tracking-widest">${new Date(n.date).toLocaleString()}</div>
					</div>
				`).join('');

			} catch (e) {
				console.error(e);
			}
		};

		// Initial Fetch
		fetchNotifications();

		btnNotifications.addEventListener('click', (e) => {
			e.stopPropagation();
			notificationsOpen = !notificationsOpen;
			if (notificationsOpen) {
				notificationsDropdown.classList.remove('hidden');
				fetchNotifications(); // Refresh on open
			} else {
				notificationsDropdown.classList.add('hidden');
			}
		});

		document.addEventListener('click', (e) => {
			if (notificationsOpen && !notificationsDropdown.contains(e.target)) {
				notificationsDropdown.classList.add('hidden');
				notificationsOpen = false;
			}
		});

		if (btnMarkAllRead) {
			btnMarkAllRead.addEventListener('click', async (e) => {
				e.stopPropagation();
				e.preventDefault();
				btnMarkAllRead.innerText = 'Marking...';
				try {
					await fetch('/wp-json/healthedia/v1/notifications/mark-read', {
						method: 'POST',
						headers: {
							'X-WP-Nonce': window.healthediaPublicSettings.nonce,
							'Content-Type': 'application/json'
						}
					});
					btnMarkAllRead.innerText = 'Mark All Read';
					fetchNotifications();
				} catch (e) {
					btnMarkAllRead.innerText = 'Mark All Read';
				}
			});
		}
	}
});
