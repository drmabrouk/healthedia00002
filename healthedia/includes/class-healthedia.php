<?php
class Healthedia {
	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct() {
		$this->plugin_name = 'healthedia';
		$this->version = HEALTHEDIA_VERSION;
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->init_modules();
	}

	private function load_dependencies() {
		require_once HEALTHEDIA_PLUGIN_DIR . 'includes/class-healthedia-loader.php';
		require_once HEALTHEDIA_PLUGIN_DIR . 'includes/class-healthedia-i18n.php';
		require_once HEALTHEDIA_PLUGIN_DIR . 'includes/class-healthedia-router.php';
		require_once HEALTHEDIA_PLUGIN_DIR . 'includes/class-healthedia-seo.php';
		require_once HEALTHEDIA_PLUGIN_DIR . 'includes/class-healthedia-sitemap.php';
		require_once HEALTHEDIA_PLUGIN_DIR . 'includes/Base/interface-module.php';
		require_once HEALTHEDIA_PLUGIN_DIR . 'includes/Base/class-base-controller.php';

		// Load module controllers
		require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Auth/class-auth-controller.php';
		require_once HEALTHEDIA_PLUGIN_DIR . 'modules/SearchEngine/class-search-controller.php';
		require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Profiles/class-profile-controller.php';
		require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Articles/class-article-controller.php';
		require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Directories/class-directory-controller.php';
		require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Notifications/class-notification-controller.php';

		require_once HEALTHEDIA_PLUGIN_DIR . 'dashboard/class-dashboard-controller.php';
		require_once HEALTHEDIA_PLUGIN_DIR . 'public/class-public-controller.php';

		$this->loader = new Healthedia_Loader();
	}

	private function set_locale() {
		$plugin_i18n = new Healthedia_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	private function define_admin_hooks() {
		$dashboard = new Healthedia_Dashboard_Controller( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'init', $dashboard, 'register_routes' );
	}

	private function define_public_hooks() {
		$public = new Healthedia_Public_Controller( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $public, 'enqueue_scripts' );

		$router = new Healthedia_Router();
		$this->loader->add_action( 'init', $router, 'add_rewrite_rules' );
		$this->loader->add_action( 'login_init', $router, 'redirect_wp_login' );
		$this->loader->add_action( 'wp_logout', $router, 'redirect_after_logout' );
		$this->loader->add_action( 'template_include', $router, 'load_templates' );

		// Register Institution Custom Post Type
		$this->loader->add_action('init', $this, 'register_institution_cpt');
		$this->loader->add_action('init', $this, 'register_certificate_cpt');
		$this->loader->add_action('init', $this, 'register_notification_cpt');
		$this->loader->add_action('init', $this, 'register_publication_cpts');

		$seo = new Healthedia_SEO();
		$this->loader->add_action( 'wp_head', $seo, 'inject_metadata', 5 );

		$sitemap = new Healthedia_Sitemap();
		$sitemap->init();
	}

	public function register_institution_cpt() {
		register_post_type('healthedia_inst', array(
			'labels' => array(
				'name' => 'Institutions',
				'singular_name' => 'Institution'
			),
			'public' => true,
			'has_archive' => false,
			'show_in_rest' => true,
			'supports' => array('title', 'editor')
		));
	}

	public function register_certificate_cpt() {
		register_post_type('healthedia_cert', array(
			'labels' => array(
				'name' => 'Certificates',
				'singular_name' => 'Certificate'
			),
			'public' => false,
			'has_archive' => false,
			'show_in_rest' => true,
			'supports' => array('title')
		));
	}

	public function register_notification_cpt() {
		register_post_type('healthedia_notif', array(
			'labels' => array(
				'name' => 'Notifications',
				'singular_name' => 'Notification'
			),
			'public' => false,
			'has_archive' => false,
			'show_in_rest' => false,
			'supports' => array('title', 'editor', 'author')
		));
	}

	public function register_publication_cpts() {
		register_post_type('healthedia_ext_res', array(
			'labels' => array(
				'name' => 'External Research',
				'singular_name' => 'External Research'
			),
			'public' => true,
			'has_archive' => true,
			'show_in_rest' => true,
			'supports' => array('title', 'editor', 'author')
		));

		register_post_type('healthedia_journal', array(
			'labels' => array(
				'name' => 'Scientific Journal',
				'singular_name' => 'Journal Paper'
			),
			'public' => true,
			'has_archive' => true,
			'show_in_rest' => true,
			'supports' => array('title', 'editor', 'author', 'thumbnail'),
			'rewrite' => array('slug' => 'journal')
		));
	}

	private function init_modules() {
		$modules = [
			new Healthedia_Auth_Controller(),
			new Healthedia_Search_Controller(),
			new Healthedia_Profile_Controller(),
			new Healthedia_Article_Controller(),
			new Healthedia_Directory_Controller(),
			new Healthedia_Notification_Controller()
		];

		foreach ($modules as $module) {
			$module->init($this->loader);
		}
	}

	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}
}
