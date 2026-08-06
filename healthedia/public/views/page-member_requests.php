<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-header.php'; ?>
<?php
$user_id = get_current_user_id();
?>
<div class="max-w-7xl mx-auto flex flex-col md:flex-row gap-12 py-12 px-4 bg-white text-[#111111]">
	<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-member-sidebar.php'; ?>

	<main class="flex-grow">
		<h1 class="text-3xl font-sans font-bold uppercase tracking-tight mb-8 border-b border-[#E0E0E0] pb-4">My Requests & Tracking</h1>

		<div id="requests-message" class="hidden mb-6 p-4 rounded-xl font-mono text-sm border"></div>

		<div class="bg-white border border-[#E0E0E0] rounded-2xl shadow-sm overflow-x-auto">
			<table class="w-full text-left border-collapse min-w-[700px]">
				<thead>
					<tr class="bg-gray-50 border-b border-[#E0E0E0] font-mono text-[10px] uppercase tracking-widest text-gray-500">
						<th class="py-4 px-6 font-normal">Tracking ID</th>
						<th class="py-4 px-6 font-normal">Request Type</th>
						<th class="py-4 px-6 font-normal">Date Submitted</th>
						<th class="py-4 px-6 font-normal">Status</th>
						<th class="py-4 px-6 font-normal text-right">Actions</th>
					</tr>
				</thead>
				<tbody class="font-sans text-sm divide-y divide-[#E0E0E0]" id="requests-table-body">
					<?php
					$args = array(
						'post_type' => array('healthedia_article', 'healthedia_post', 'healthedia_ext_res', 'healthedia_journal'),
						'author' => $user_id,
						'posts_per_page' => -1,
						'post_status' => array('pending')
					);
					$query = new WP_Query($args);

					if ($query->have_posts()): while ($query->have_posts()): $query->the_post();
						$post_id = get_the_ID();
					?>
					<tr class="hover:bg-gray-50 transition-colors" id="request-row-<?php echo $post_id; ?>">
						<td class="py-4 px-6 font-mono text-xs text-gray-500">REQ-MS-<?php echo $post_id; ?></td>
						<td class="py-4 px-6 font-bold">Manuscript Submission<br><span class="font-normal text-xs text-gray-500 truncate block max-w-[200px]"><?php the_title(); ?></span></td>
						<td class="py-4 px-6 font-mono text-xs text-gray-500"><?php the_date(); ?></td>
						<td class="py-4 px-6">
							<span class="bg-yellow-100 text-yellow-800 border border-yellow-200 px-2 py-0.5 rounded-full font-mono text-[10px] uppercase tracking-widest">Pending Review</span>
						</td>
						<td class="py-4 px-6 text-right">
							<button onclick="withdrawRequest(<?php echo $post_id; ?>)" class="border border-[#E0E0E0] px-3 py-1.5 rounded font-mono text-[10px] uppercase hover:bg-gray-100 transition-colors text-gray-600">Withdraw</button>
						</td>
					</tr>
					<?php endwhile; wp_reset_postdata(); else: ?>
					<tr id="empty-row">
						<td colspan="5" class="py-12 text-center text-gray-400 font-mono text-sm uppercase tracking-widest">
							No active requests found.
						</td>
					</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</main>
</div>
<script>
async function withdrawRequest(postId) {
	if (!window.confirm("Are you sure you want to withdraw this manuscript submission?")) {
		return;
	}

	const msgBox = document.getElementById('requests-message');
	msgBox.classList.add('hidden');

	try {
		const res = await fetch(`/wp-json/healthedia/v1/manuscript/${postId}`, {
			method: 'DELETE',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': window.healthediaPublicSettings?.nonce
			}
		});

		const result = await res.json();

		if (res.ok) {
			document.getElementById('request-row-' + postId).remove();
			msgBox.textContent = result.message || 'Request successfully withdrawn.';
			msgBox.className = 'mb-6 p-4 rounded-xl font-mono text-sm border border-black bg-gray-50 text-black';

			// Check if table is empty now
			if (document.querySelectorAll('#requests-table-body tr').length === 0) {
				document.getElementById('requests-table-body').innerHTML = '<tr id="empty-row"><td colspan="5" class="py-12 text-center text-gray-400 font-mono text-sm uppercase tracking-widest">No active requests found.</td></tr>';
			}
		} else {
			msgBox.textContent = result.message || 'An error occurred.';
			msgBox.className = 'mb-6 p-4 rounded-xl font-mono text-sm border border-red-500 bg-red-50 text-red-700';
		}
	} catch (err) {
		msgBox.textContent = 'Network error. Please try again.';
		msgBox.className = 'mb-6 p-4 rounded-xl font-mono text-sm border border-red-500 bg-red-50 text-red-700';
	} finally {
		msgBox.classList.remove('hidden');
	}
}
</script>
<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-footer.php'; ?>
