<?php
class Healthedia_Profile_Controller extends Healthedia_Base_Controller {
	public function init( $loader ) {
		require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Profiles/class-profile-model.php';
		require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Profiles/class-profile-verification.php';

		$model = new Healthedia_Profile_Model();
		$loader->add_action( 'template_redirect', $model, 'track_views' );

		$verification = new Healthedia_Profile_Verification();
		$loader->add_action( 'rest_api_init', $verification, 'register_routes' );

		require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Profiles/class-profile-endpoints.php';
		$endpoints = new Healthedia_Profile_Endpoints();
		$loader->add_action( 'rest_api_init', $endpoints, 'register_routes' );
	}
}
