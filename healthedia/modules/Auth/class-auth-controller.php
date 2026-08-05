<?php
class Healthedia_Auth_Controller extends Healthedia_Base_Controller {
	public function init( $loader ) {
		require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Auth/class-auth-endpoints.php';
		require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Auth/class-auth-otp.php';
		require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Auth/class-auth-mailer.php';

		$endpoints = new Healthedia_Auth_Endpoints();
		$loader->add_action( 'rest_api_init', $endpoints, 'register_routes' );
	}
}
