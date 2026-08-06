<?php
class Healthedia_Directory_Controller extends Healthedia_Base_Controller {
	public function init( $loader ) {
		require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Directories/class-directory-endpoints.php';

		$endpoints = new Healthedia_Directory_Endpoints();
		$loader->add_action( 'rest_api_init', $endpoints, 'register_routes' );
	}
}
