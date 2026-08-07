<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-header.php'; ?>
<?php
$search_query = isset($_GET['cert']) ? sanitize_text_field($_GET['cert']) : '';
$result = null;

if (!empty($search_query)) {
	$args = array(
		'post_type' => 'healthedia_cert',
		'post_status' => 'publish',
		'meta_query' => array(
			array(
				'key' => '_healthedia_cert_number',
				'value' => $search_query,
				'compare' => '='
			)
		),
		'posts_per_page' => 1
	);
	$query = new WP_Query($args);
	if ($query->have_posts()) {
		$query->the_post();
		$result = array(
			'title' => get_the_title(),
			'cert_number' => get_post_meta(get_the_ID(), '_healthedia_cert_number', true),
			'holder_name' => get_post_meta(get_the_ID(), '_healthedia_cert_holder', true),
			'issue_date' => get_post_meta(get_the_ID(), '_healthedia_cert_issue', true),
		);
	}
	wp_reset_postdata();
}
?>
<div class="max-w-7xl mx-auto py-12 px-4 md:px-8 bg-white text-[#111111] text-center min-h-[calc(100vh-200px)] flex flex-col justify-center">

	<h1 class="text-4xl md:text-5xl font-sans font-bold uppercase tracking-tight mb-4">Certificate Verification</h1>
	<p class="font-mono text-sm text-gray-500 uppercase mb-12">Verify the authenticity of Healthedia credentials</p>

	<form class="max-w-7xl mx-auto w-full relative" method="GET" action="">
		<input type="text" name="cert" value="<?php echo esc_attr($search_query); ?>" placeholder="Enter Certificate ID (e.g. HTH-00192-X)" required class="w-full border-2 border-[#E0E0E0] rounded-full py-4 px-8 text-lg font-mono text-center tracking-widest outline-none focus:border-black transition-colors shadow-sm uppercase">
		<button type="submit" class="mt-8 bg-black text-white px-10 py-4 rounded-full font-sans uppercase text-sm tracking-wide font-bold hover:bg-gray-800 transition-colors w-full md:w-auto">Verify Authenticity</button>
	</form>

	<?php if (!empty($search_query)): ?>
		<div class="mt-12 max-w-7xl mx-auto text-left">
			<?php if ($result): ?>
				<div class="border border-green-200 bg-green-50 p-6 md:p-8 rounded-2xl relative overflow-hidden">
					<div class="absolute top-0 right-0 p-4 text-green-600">
						<svg class="w-12 h-12 opacity-20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
					</div>
					<h3 class="font-mono text-xs uppercase tracking-widest text-green-700 font-bold mb-4 flex items-center gap-2">
						<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
						Verified Authentic Record
					</h3>
					<div class="space-y-4 font-sans">
						<div>
							<div class="font-mono text-[10px] text-gray-500 uppercase tracking-widest">Document Title</div>
							<div class="font-bold text-lg"><?php echo esc_html($result['title']); ?></div>
						</div>
						<div>
							<div class="font-mono text-[10px] text-gray-500 uppercase tracking-widest">Issued To</div>
							<div class="text-base"><?php echo esc_html($result['holder_name']); ?></div>
						</div>
						<div class="grid grid-cols-2 gap-4 pt-4 border-t border-green-200/50">
							<div>
								<div class="font-mono text-[10px] text-gray-500 uppercase tracking-widest">Certificate No.</div>
								<div class="font-mono text-sm"><?php echo esc_html($result['cert_number']); ?></div>
							</div>
							<div>
								<div class="font-mono text-[10px] text-gray-500 uppercase tracking-widest">Issue Date</div>
								<div class="font-mono text-sm"><?php echo esc_html($result['issue_date']); ?></div>
							</div>
						</div>
					</div>
				</div>
			<?php else: ?>
				<div class="border border-red-200 bg-red-50 p-6 md:p-8 rounded-2xl text-center">
					<svg class="w-12 h-12 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
					<h3 class="font-sans font-bold text-xl text-red-700 mb-2">Record Not Found</h3>
					<p class="font-mono text-xs text-red-600 uppercase tracking-widest">The requested certificate ID could not be verified against the secure ledger. Please check the ID and try again.</p>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="mt-12 pt-8 border-t border-[#E0E0E0] font-mono text-[10px] uppercase tracking-widest text-gray-400">
		All cryptographic queries are securely logged.
	</div>
</div>
<?php include HEALTHEDIA_PLUGIN_DIR . 'public/views/layout-footer.php'; ?>
