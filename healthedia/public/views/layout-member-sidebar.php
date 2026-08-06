<aside class="w-full md:w-64 flex-shrink-0 border-r border-[#E0E0E0] pr-6">
	<div class="sticky top-8">
		<h3 class="font-sans font-bold uppercase tracking-wider mb-6 text-sm border-b border-[#E0E0E0] pb-2">Member Portal</h3>
		<nav class="font-mono text-xs uppercase tracking-widest space-y-2">
			<?php
			$current = get_query_var('healthedia_page');
			$links = [
				'member_settings' => ['url' => '/account-settings', 'label' => 'Account Settings'],
				'member_saved' => ['url' => '/saved-research', 'label' => 'Saved Research'],
				'member_requests' => ['url' => '/my-requests', 'label' => 'My Requests']
			];
			if (current_user_can('submit_articles')) {
				$links['submit_article'] = ['url' => '/submit-article', 'label' => 'Submit Article'];
			}
			if (current_user_can('submit_ext_res')) {
				$links['submit_research'] = ['url' => '/submit-research', 'label' => 'Add Published Research'];
			}
			if (current_user_can('submit_journal')) {
				$links['submit_journal'] = ['url' => '/submit-journal', 'label' => 'Submit to Journal'];
			}
			foreach ($links as $slug => $data) {
				$activeClass = ($current === $slug) ? 'bg-black text-white' : 'text-gray-500 hover:text-black hover:bg-gray-50';
				echo '<a href="'.home_url($data['url']).'" class="block px-4 py-2.5 rounded-full transition-colors '.$activeClass.'">'.$data['label'].'</a>';
			}
			?>
			<a href="<?php echo wp_logout_url(home_url()); ?>" class="block px-4 py-2.5 mt-8 border border-[#E0E0E0] text-gray-500 hover:text-black hover:border-black rounded-full transition-colors text-center">Secure Logout</a>
		</nav>
	</div>
</aside>
