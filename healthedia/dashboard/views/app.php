<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Healthedia Dashboard</title>
	<?php wp_head(); ?>
	<link rel="stylesheet" href="<?php echo HEALTHEDIA_PLUGIN_URL; ?>assets/css/healthedia-dashboard.css">
	<script>
		window.healthediaDashboardSettings = {
			nonce: '<?php echo wp_create_nonce("wp_rest"); ?>'
		};
	</script>
</head>
<body>
	<div id="healthedia-dashboard-root"></div>
	<?php wp_footer(); ?>
	<script src="<?php echo HEALTHEDIA_PLUGIN_URL; ?>assets/js/healthedia-dashboard.js"></script>
</body>
</html>
