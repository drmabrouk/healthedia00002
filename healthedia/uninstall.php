<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package    Healthedia
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die();
}

global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}healthedia_search_index" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}healthedia_metrics" );

// Delete auto-provisioned pages
$pages = array(
	'gateway', 'auth', 'directory', 'academies', 'journal', 'dashboard',
	'account-settings', 'saved-research', 'my-requests', 'submit-manuscript',
	'privacy-policy', 'terms-of-service', 'publication-policies', 'certificate-verification', 'support'
);

foreach ( $pages as $slug ) {
	$page = get_page_by_path( $slug );
	if ( $page ) {
		wp_delete_post( $page->ID, true );
	}
}

// Clean transients
global $wpdb;
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_healthedia_otp_%'" );
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_healthedia_otp_%'" );
