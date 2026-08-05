<?php
/**
 * Layout Header Wrapper for Healthedia Plugin
 * Call active WordPress theme header and open Astra-compatible container structures.
 */
get_header();

$primary_class = function_exists('astra_primary_class') ? 'class="' . esc_attr(astra_primary_class(false)) . '"' : 'class="content-area primary"';
?>
<div id="primary" <?php echo $primary_class; ?>>
	<main id="main" class="site-main" role="main">
		<div class="ast-container">

			<!-- Premium Secondary Utility Bar (Sleek Academic Integration) -->
			<div class="healthedia-secondary-utility border border-[#E0E0E0] rounded-2xl bg-[#FAFAFA] py-3 px-6 mb-8 mt-4 flex flex-col md:flex-row items-center justify-between gap-4 font-sans text-xs">
				<div class="flex items-center gap-6">
					<a href="<?php echo home_url(); ?>" class="font-bold text-black uppercase tracking-wider hover:text-red-700 transition-colors">Healthedia Archive</a>
					<nav class="flex items-center gap-4 font-mono text-[10px] uppercase tracking-widest text-gray-500">
						<a href="<?php echo home_url('/directory'); ?>" class="hover:text-black transition-colors">Directory</a>
						<a href="<?php echo home_url('/journal'); ?>" class="hover:text-black transition-colors">Journal</a>
						<a href="<?php echo home_url('/academies'); ?>" class="hover:text-black transition-colors">Academies</a>
					</nav>
				</div>
				<div class="flex items-center gap-4">
					<?php if ( is_user_logged_in() ) : ?>
						<?php if ( current_user_can( 'manage_options' ) ) : ?>
							<a href="<?php echo home_url('/healthedia-admin'); ?>" class="font-mono text-[9px] uppercase tracking-widest bg-[#800020] text-white px-3 py-1.5 rounded-full hover:bg-black transition-colors flex items-center gap-1">
								Dashboard
							</a>
						<?php endif; ?>
						<div class="flex items-center gap-3 relative">
							<a href="<?php echo home_url('/account-settings'); ?>" title="Profile Settings" class="text-gray-500 hover:text-black transition-colors font-mono text-[10px] uppercase tracking-widest font-bold">
								Settings
							</a>
							<button id="btn-notifications" title="Notifications" class="text-gray-500 hover:text-black transition-colors relative flex items-center gap-1">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
								<span id="notification-badge" class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full hidden"></span>
							</button>
							<a href="<?php echo wp_logout_url(home_url()); ?>" title="Secure Logout" class="text-gray-400 hover:text-red-600 transition-colors font-mono text-[10px] uppercase tracking-widest">
								Logout
							</a>

							<!-- Notifications Dropdown -->
							<div id="notifications-dropdown" class="hidden absolute top-full right-0 mt-2 w-80 bg-white border border-[#E0E0E0] rounded-xl shadow-lg z-50 overflow-hidden normal-case">
								<div class="p-3 border-b border-[#E0E0E0] flex justify-between items-center bg-gray-50">
									<span class="font-sans font-bold text-xs uppercase tracking-widest text-gray-700">Notifications</span>
									<button id="btn-mark-all-read" class="font-mono text-[10px] text-gray-500 hover:text-black uppercase tracking-widest transition-colors">Mark All Read</button>
								</div>
								<div id="notifications-list" class="max-h-80 overflow-y-auto font-sans text-sm divide-y divide-[#E0E0E0]">
									<div class="p-4 text-center text-gray-500 font-mono text-xs">Loading...</div>
								</div>
							</div>
						</div>
					<?php else: ?>
						<a href="<?php echo home_url('/login'); ?>" class="font-mono text-[10px] uppercase tracking-widest bg-black text-white px-4 py-2 rounded-full hover:bg-gray-800 transition-colors">Login / Register</a>
					<?php endif; ?>
				</div>
			</div>
