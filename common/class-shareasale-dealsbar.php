<?php

class ShareASale_Dealsbar {

	/**
	* @var ShareASale_Dealsbar_Toolbar $toolbar object that controls the state, type (admin or frontend), and rendering of dealsbar toolbar itself
	* @var ShareASale_Dealsbar_Loader $loader Loader object that coordinates actions and filters between core plugin and classes
	* @var string $plugin_slug WordPress Slug for this plugin
	* @var float $version Plugin version
	*/
	protected $toolbar, $loader, $plugin_slug, $version;

	public function __construct() {
		$this->plugin_slug = 'shareasale-dealsbar-slug';
		$this->version     = '1.2';

		$this->load_dependencies();

		$this->define_frontend_hooks();
		$this->define_admin_hooks();
		$this->define_installer_hooks();
		$this->define_uninstaller_hooks();
		$this->define_updater_hooks();
	}

	private function load_dependencies() {
		require_once plugin_dir_path( __FILE__ ) . 'class-shareasale-dealsbar-loader.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-shareasale-dealsbar-toolbar.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-shareasale-dealsbar-admin.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-shareasale-dealsbar-installer.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-shareasale-dealsbar-uninstaller.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-shareasale-dealsbar-updater.php';

		$this->loader  = new ShareASale_Dealsbar_Loader();
		$this->toolbar = new ShareASale_Dealsbar_Toolbar( $this->get_version() );
	}

	private function define_frontend_hooks() {
		//frontend facing toolbar actions
		$this->loader->add_action( 'wp_enqueue_scripts', $this->toolbar, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $this->toolbar, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_enqueue_scripts', $this->toolbar, 'render_custom_css' );
		$this->loader->add_action( 'wp_footer',          $this->toolbar, 'render_toolbar' );
	}

	private function define_admin_hooks() {
		$admin   = new ShareASale_Dealsbar_Admin( $this->get_version() );
		//general admin actions
		$this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_init',            $admin, 'admin_init' );
		$this->loader->add_action( 'admin_menu',            $admin, 'admin_menu' );
		//admin facing toolbar actions
		$this->loader->add_action( 'admin_enqueue_scripts',               $this->toolbar, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts',               $this->toolbar, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_enqueue_scripts',               $this->toolbar, 'render_custom_css' );
		$this->loader->add_action( 'admin_footer-toplevel_page_dealsbar', $this->toolbar, 'render_toolbar' );
		//admin filters
		$this->loader->add_filter( 'plugin_action_links_' . SHAREASALE_DEALSBAR_PLUGIN_FILENAME, $admin, 'render_settings_shortcut' );
	}

	private function define_installer_hooks() {
	    $installer = new ShareASale_Dealsbar_Installer( $this->get_version() );
	    register_activation_hook( __FILE__, array( $installer, 'install' ) );
	}

	private function define_uninstaller_hooks() {
	    return;
	}

	private function define_updater_hooks() {
		return;
	}

	public function run() {
		$this->loader->run();
	}

	public function get_version() {
		return $this->version;
	}
}
