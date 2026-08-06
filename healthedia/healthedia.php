<?php
/**
 * Plugin Name:       Healthedia
 * Plugin URI:        https://healthedia.com
 * Description:       A massive, standalone, and enterprise-grade WordPress plugin for a highly sophisticated, academic, and scientific networking platform.
 * Version:           3.0.0
 * Author:            Healthedia
 * Text Domain:       healthedia
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'HEALTHEDIA_VERSION', '3.0.0' );
define( 'HEALTHEDIA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'HEALTHEDIA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function activate_healthedia() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-healthedia-activator.php';
	Healthedia_Activator::activate();
	update_option('healthedia_needs_rewrite_flush', true);
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_healthedia() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-healthedia-deactivator.php';
	Healthedia_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_healthedia' );
register_deactivation_hook( __FILE__, 'deactivate_healthedia' );

/**
 * The core plugin class.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-healthedia.php';

/**
 * Begins execution of the plugin.
 */
function run_healthedia() {
	$plugin = new Healthedia();
	$plugin->run();
}
run_healthedia();
