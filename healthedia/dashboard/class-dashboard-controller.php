<?php
class Healthedia_Dashboard_Controller {
	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	public function register_routes() {
		require_once HEALTHEDIA_PLUGIN_DIR . 'dashboard/class-dashboard-api.php';
		$api = new Healthedia_Dashboard_API();
		add_action('rest_api_init', array($api, 'register_routes'));
	}
}
