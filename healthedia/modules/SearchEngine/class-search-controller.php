<?php
class Healthedia_Search_Controller extends Healthedia_Base_Controller {
	public function init( $loader ) {
		require_once HEALTHEDIA_PLUGIN_DIR . 'modules/SearchEngine/class-search-indexer.php';
		require_once HEALTHEDIA_PLUGIN_DIR . 'modules/SearchEngine/class-search-endpoints.php';

		$indexer = new Healthedia_Search_Indexer();
		$loader->add_action( 'save_post', $indexer, 'index_post', 10, 3 );
		$loader->add_action( 'profile_update', $indexer, 'index_user', 10, 2 );
		$loader->add_action( 'user_register', $indexer, 'index_user', 10, 1 );

		$endpoints = new Healthedia_Search_Endpoints();
		$loader->add_action( 'rest_api_init', $endpoints, 'register_routes' );
	}
}
