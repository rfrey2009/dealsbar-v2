<?php
class ShareASale_Dealsbar_Toolbar {
    /**
    * @var Wpdb $wpdb WordPress global database connection singleton
    * @var float $version Plugin version, used for cache-busting
    * @var array $settings user's configuration choices for dealsbar toolbar design and Merchants
    * @var array $deals array containing chosen Merchant's deals for the dealsbar toolbar
    * @var ShareASale_Dealsbar_Loader $loader Loader dependency injected object that coordinates actions and filters between core plugin and classes
   */
	private $wpdb, $version, $settings, $deals, $loader;
 
    public function __construct( $version, $loader ) {        
        $this->version = $version;
        $this->loader  = $loader;
        $this->load_dependencies();

        $this->define_toolbar_hooks();
    }

    private function load_dependencies() {
        //should setup everything the methods called in __construct need to work
        global $wpdb;

        $this->wpdb      = &$wpdb;
        $this->settings  = get_option( 'dealsbar_options' );
        $this->deals     = $this->get_deals( $this->settings['toolbar-merchants'] );
    }

    private function define_toolbar_hooks() {
        //admin
        $this->loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_scripts' );
        $this->loader->add_action( 'admin_enqueue_scripts', $this, 'render_custom_css' );
        //frontend
        $this->loader->add_action( 'wp_enqueue_scripts',    $this, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts',    $this, 'enqueue_scripts' );
        $this->loader->add_action( 'wp_enqueue_scripts',    $this, 'render_custom_css' );
    }

    private function get_deals( $merchants ) {
        return;
    }

    public function render_custom_css(){
        $custom_inline_styles = 
            "div#dealsbar-deals-toolbar{
                background-color: {$this->settings['toolbar-bg-color']};
                color: {$this->settings['toolbar-text-color']};
                height: {$this->settings['toolbar-pixels']}px;      
                font-size: " . ( wp_is_mobile() ? '2vmax;' : (int) $this->settings['toolbar-pixels'] / 2 . 'px;' ) . "
                {$this->settings['toolbar-position']}: 0;
                " . wp_strip_all_tags( $this->settings['toolbar-custom-css'] ) . "
            }";
        $custom_inline_styles .= 
            "#dealsbar-toolbar-ad{
                display: " . ( !is_admin() ? 'block' : 'none' ) . "
            }";
        $custom_inline_styles .= 
            "#dealsbar-toolbar-warning{
                display: " . ( is_admin() ? 'block' : 'none' ) . "
            }";

        wp_add_inline_style( 'dealsbar-standard-styles', $custom_inline_styles );
    }

    public function render_toolbar() {
        if ( !$this->settings['toolbar-setting'] )
            return;

    	$template = file_get_contents( plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-toolbar.php' );
        echo $template;
    }

    public function enqueue_styles( $hook ) {
        if ( !$this->settings['toolbar-setting'] || ( is_admin() && $hook != 'toplevel_page_dealsbar' ) )
            return;

            wp_enqueue_style( 
                'font-awesome', 
                plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css',
                array(),
                '4.6.3'
            );

            wp_enqueue_style( 
                'dealsbar-standard-styles', 
                plugin_dir_url( __FILE__ ) . 'css/shareasale-dealsbar.css',
                array(),
                $this->version
            );
    }

    public function enqueue_scripts( $hook ) {
        if ( !$this->settings['toolbar-setting'] || ( is_admin() && $hook != 'toplevel_page_dealsbar' ) )
            return;

            wp_enqueue_script(
                'dealsbar-deals-toolbar',
                plugin_dir_url( __FILE__ ) . 'js/shareasale-dealsbar-toolbar.js',
                array('jquery'),
                $this->version
            );

            wp_localize_script(
                'dealsbar-deals-toolbar',
                'dealsbarToolbarSettings',
                array(
                    'start_index' => '', //$random_deal_index, 
                    'deals' =>  '', //$results, 
                    'is_backend' => '' //$is_backend
                )
            );
    }
}    