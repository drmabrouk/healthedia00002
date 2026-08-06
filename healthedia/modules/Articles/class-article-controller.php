<?php
class Healthedia_Article_Controller extends Healthedia_Base_Controller {
	public function init( $loader ) {
		require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Articles/class-article-doi.php';
		require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Articles/class-article-endpoints.php';

		$doi = new Healthedia_Article_DOI();
		$loader->add_action( 'save_post_healthedia_journal', $doi, 'generate_doi_on_publish', 10, 3 );

		$endpoints = new Healthedia_Article_Endpoints();
		$loader->add_action( 'rest_api_init', $endpoints, 'register_routes' );
	}
}
