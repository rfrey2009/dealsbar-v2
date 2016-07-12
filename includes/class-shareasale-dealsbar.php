<?php
 
class ShareASale_Dealsbar {

    /**
   * @var ShareASale_Dealsbar_Loader $loader Loader object that coordinates actions and filters between core plugin and admin classes
   * @var string $plugin_slug WordPress Slug for this plugin
   * @var float $version Plugin version
   */
	protected $loader, $plugin_slug, $version;
 
    public function __construct() {
 
        $this->plugin_slug = 'shareasale-dealsbar-slug';
        $this->version     = '1.2';
 
        $this->load_dependencies();
        $this->define_admin_hooks();
 
    }
 
    /**
    * Loads the plugin's dependencies
    */
    private function load_dependencies() {
        /** 
        * This WordPress option will store the Affiliate ID
        */
        add_option( 'dealsbar_options', '' );

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-shareasale-dealsbar-admin.php';
        require_once plugin_dir_path( __FILE__ ) . 'class-shareasale-dealsbar-api.php';
        require_once plugin_dir_path( __FILE__ ) . 'class-shareasale-dealsbar-loader.php';
        $this->loader = new ShareASale_Dealsbar_Loader();
    }

    /**
    * Setup the actions/methods to run on the ShareASale_Dealsbar_Admin object when certain WordPress hooks happen
    * No filters used yet in v0.1.0
    */
    private function define_admin_hooks() {
 
        $admin = new ShareASale_Dealsbar_Admin( $this->get_version() );
        $this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_scripts' );
        $this->loader->add_action( 'admin_init',            $admin, 'admin_init' );
        $this->loader->add_action( 'admin_menu',            $admin, 'admin_menu' );
 
    }

    /**
    * Wrapper for the loader object to execute now that dependencies and hooks were setup in the constructor
    */
    public function run() {
        $this->loader->run();
    }

    /**
    * Simply returns the plugin version
    * Useful for cache-busting on the frontend
    */
    public function get_version() {
        return $this->version;
    }
 
}