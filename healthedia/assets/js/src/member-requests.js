window.withdrawRequest = async function withdrawRequest(postId) {
	if (!window.confirm("Are you sure you want to withdraw this manuscript submission?")) {
		return;
	}

	const msgBox = document.getElementById('requests-message');
	if (msgBox) msgBox.classList.add('hidden');

	try {
		const res = await fetch(`/wp-json/healthedia/v1/manuscript/${postId}`, {
			method: 'DELETE',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': window.healthediaPublicSettings?.nonce || ''
			}
		});

		const result = await res.json();

		if (res.ok) {
			const row = document.getElementById('request-row-' + postId);
			if (row) row.remove();
			if (msgBox) {
				msgBox.textContent = result.message || 'Request successfully withdrawn.';
				msgBox.className = 'mb-6 p-4 rounded-xl font-mono text-sm border border-black bg-gray-50 text-black';
				msgBox.classList.remove('hidden');
			}

			// Check if table is empty now
			const tbody = document.getElementById('requests-table-body');
			if (tbody && tbody.querySelectorAll('tr:not(#empty-row)').length === 0) {
				tbody.innerHTML = '<tr id="empty-row"><td colspan="5" class="py-12 text-center text-gray-400 font-mono text-sm uppercase tracking-widest">No active requests found.</td></tr>';
			}
		} else {
			if (msgBox) {
				msgBox.textContent = result.message || 'An error occurred.';
				msgBox.className = 'mb-6 p-4 rounded-xl font-mono text-sm border border-red-500 bg-red-50 text-red-700';
				msgBox.classList.remove('hidden');
			}
		}
	} catch (err) {
		if (msgBox) {
			msgBox.textContent = 'Network error. Please try again.';
			msgBox.className = 'mb-6 p-4 rounded-xl font-mono text-sm border border-red-500 bg-red-50 text-red-700';
			msgBox.classList.remove('hidden');
		}
	}
}
