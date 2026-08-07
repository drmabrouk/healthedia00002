<?php
// Since this plugin replaces the frontend heavily, this is an optional layout wrapper.
if ( ! headers_sent() ) {
	header( 'Cache-Control: no-cache, no-store, must-revalidate, max-age=0' );
	header( 'Pragma: no-cache' );
	header( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT' );
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php wp_title('|', true, 'right'); ?></title>
	<?php wp_head(); ?>
	<?php if(is_user_logged_in()): ?>
	<script>window.healthediaPublicSettings = { nonce: "<?php echo wp_create_nonce("wp_rest"); ?>" };</script>
	<?php endif; ?>
</head>
<body <?php body_class('bg-white text-[#111111] antialiased min-h-screen flex flex-col pt-16 pb-14'); ?>>

	<!-- Global Header (Archival Minimalist) -->
	<header class="fixed top-0 left-0 right-0 bg-white/90 backdrop-blur-sm z-40 border-b border-[#E0E0E0] h-16">
		<div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">

			<div class="flex items-center gap-6 z-50">
				<div>
					<a href="<?php echo home_url(); ?>" class="font-sans font-bold text-xl tracking-tighter uppercase block leading-none">Healthedia</a>
					<div class="font-mono text-[8px] uppercase tracking-widest text-gray-500 mt-1">Global Health Archive & Research Network</div>
				</div>

				<!-- Desktop Nav -->
				<nav class="hidden md:flex items-center gap-6 font-mono text-sm uppercase tracking-wider pl-6 border-l border-[#E0E0E0]">
					<a href="<?php echo home_url(); ?>" class="text-gray-500 hover:text-black transition-colors">Home</a>
					<a href="<?php echo home_url('/journal'); ?>" class="text-gray-500 hover:text-black transition-colors">Journal</a>
					<a href="<?php echo home_url('/directory'); ?>" class="text-gray-500 hover:text-black transition-colors">Directory</a>
				</nav>
			</div>

			<!-- Desktop Actions -->
			<div class="hidden md:flex items-center gap-4">
				<?php if ( is_user_logged_in() ) : ?>
					<?php if ( current_user_can( 'manage_options' ) ) : ?>
						<a href="<?php echo home_url('/healthedia-admin'); ?>" class="font-mono text-xs uppercase tracking-wider bg-[#800020] text-white px-3 py-1.5 rounded-full hover:bg-[#600018] transition-colors flex items-center gap-1">
							<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V10"></path><path d="M18 20V4"></path><path d="M6 20v-4"></path></svg>
							Dashboard
						</a>
					<?php endif; ?>
					<div class="flex items-center gap-2 relative">
						<a href="<?php echo home_url('/account-settings'); ?>" title="Profile Settings" class="w-10 h-10 bg-gray-50 border border-[#E0E0E0] rounded-lg text-gray-500 hover:text-black hover:border-black transition-colors flex items-center justify-center shadow-sm">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
						</a>
						<button id="btn-notifications" title="Notifications" class="w-10 h-10 bg-gray-50 border border-[#E0E0E0] rounded-lg text-gray-500 hover:text-black hover:border-black transition-colors flex items-center justify-center relative shadow-sm">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
							<span id="notification-badge" class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full hidden"></span>
						</button>
						<a href="<?php echo wp_logout_url(home_url()); ?>" title="Secure Logout" class="w-10 h-10 bg-gray-50 border border-[#E0E0E0] rounded-lg text-gray-500 hover:text-red-500 hover:bg-red-50 hover:border-red-200 transition-colors flex items-center justify-center shadow-sm">
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
						</a>

						<!-- Notifications Dropdown -->
						<div id="notifications-dropdown" class="hidden absolute top-full right-0 mt-2 w-80 bg-white border border-[#E0E0E0] rounded-xl shadow-lg z-50 overflow-hidden">
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
					<a href="<?php echo home_url('/login'); ?>" class="font-mono text-xs uppercase tracking-wider bg-black text-white px-5 py-2 rounded-full hover:bg-gray-800 transition-colors flex items-center justify-center">Login / Register</a>
				<?php endif; ?>
			</div>

			<!-- Mobile Menu Button -->
			<button id="mobile-menu-btn" class="md:hidden flex items-center justify-center w-11 h-11 text-black z-50 focus:outline-none" aria-label="Toggle Menu">
				<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
			</button>
		</div>
	</header>

	<!-- Mobile Off-Canvas Menu -->
	<div id="mobile-menu-overlay" class="fixed inset-0 bg-white z-40 transform translate-x-full transition-transform duration-300 md:hidden flex flex-col pt-24 px-6 pb-8 overflow-y-auto">
		<nav class="flex flex-col gap-6 font-mono text-xl uppercase tracking-wider mb-8 border-b border-[#E0E0E0] pb-8">
			<a href="<?php echo home_url('/journal'); ?>" class="text-black hover:text-gray-500 transition-colors block">Scientific Journal</a>
			<a href="<?php echo home_url('/directory'); ?>" class="text-black hover:text-gray-500 transition-colors block">Global Directory</a>
			<a href="<?php echo home_url('/academies'); ?>" class="text-black hover:text-gray-500 transition-colors block">Academies</a>
		</nav>

		<div class="flex flex-col gap-4 mt-auto">
			<?php if ( is_user_logged_in() ) : ?>
				<?php if ( current_user_can( 'manage_options' ) ) : ?>
					<a href="<?php echo home_url('/healthedia-admin'); ?>" class="font-mono text-sm uppercase tracking-wider bg-[#800020] text-white w-full py-4 rounded-xl flex items-center justify-center gap-2">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V10"></path><path d="M18 20V4"></path><path d="M6 20v-4"></path></svg>
						System Dashboard
					</a>
				<?php endif; ?>
				<a href="<?php echo home_url('/account-settings'); ?>" class="font-mono text-sm uppercase tracking-wider border border-[#E0E0E0] w-full py-4 rounded-xl text-center">Member Portal</a>
				<a href="<?php echo wp_logout_url(home_url()); ?>" class="font-mono text-sm uppercase tracking-wider bg-gray-100 text-gray-500 w-full py-4 rounded-xl text-center mt-2">Logout</a>
			<?php else: ?>
				<a href="<?php echo home_url('/login'); ?>" class="font-mono text-sm uppercase tracking-wider bg-black text-white w-full py-4 rounded-xl flex items-center justify-center">Login / Register</a>
			<?php endif; ?>
		</div>
	</div>

	<!-- Main Content Wrapper -->
	<div class="flex-grow flex flex-col">
