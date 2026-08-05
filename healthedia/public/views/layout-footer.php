	</div> <!-- End Main Content Wrapper -->

	<!-- Global Footer -->
	<footer class="bg-white py-6">
		<div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center gap-4">
			<div class="font-mono text-[10px] text-gray-400 uppercase tracking-widest">
				&copy; <?php echo date('Y'); ?> Healthedia Archive. All Rights Reserved.
			</div>
			<nav class="flex flex-wrap justify-center gap-6 font-mono text-[10px] text-gray-500 uppercase tracking-widest mt-4 md:mt-0">
				<a href="<?php echo esc_url(get_option('healthedia_privacy_policy_url', home_url('/privacy-policy'))); ?>" class="hover:text-black transition-colors">Privacy Policy</a>
				<a href="<?php echo esc_url(get_option('healthedia_terms_url', home_url('/terms-of-service'))); ?>" class="hover:text-black transition-colors">Terms of Service</a>
				<a href="<?php echo home_url('/legal'); ?>" class="hover:text-black transition-colors">Data Integrity</a>
			</div>
		</div>
	</footer>

	<?php wp_footer(); ?>
</body>
</html>
