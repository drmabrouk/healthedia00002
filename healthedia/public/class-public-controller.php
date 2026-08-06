<?php
class Healthedia_Public_Controller {
	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name . '-core', HEALTHEDIA_PLUGIN_URL . 'assets/css/healthedia-core.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-fonts', HEALTHEDIA_PLUGIN_URL . 'assets/css/fonts.css', array(), $this->version, 'all' );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name . '-public', HEALTHEDIA_PLUGIN_URL . 'assets/js/healthedia-public.js', array( 'jquery' ), $this->version, true );
		wp_localize_script( $this->plugin_name . '-public', 'healthediaPublicSettings', array(
			'nonce' => wp_create_nonce( 'wp_rest' )
		) );

		$page = get_query_var('healthedia_page');
		if ( $page === 'member_settings' ) {
			wp_enqueue_script( $this->plugin_name . '-member-settings', HEALTHEDIA_PLUGIN_URL . 'assets/js/member-settings.js', array( $this->plugin_name . '-public' ), $this->version, true );
		} elseif ( $page === 'archive_search' ) {
			wp_enqueue_script( $this->plugin_name . '-archive-search', HEALTHEDIA_PLUGIN_URL . 'assets/js/archive-search.js', array( $this->plugin_name . '-public' ), $this->version, true );
		} elseif ( $page === 'member_requests' ) {
			wp_enqueue_script( $this->plugin_name . '-member-requests', HEALTHEDIA_PLUGIN_URL . 'assets/js/member-requests.js', array( $this->plugin_name . '-public' ), $this->version, true );
		}
	}
}
