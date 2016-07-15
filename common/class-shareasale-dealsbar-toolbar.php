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
        $this->loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_scripts' ); 
        $this->loader->add_action( 'wp_enqueue_scripts',    $this, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts',    $this, 'enqueue_scripts' );
    }

    private function get_deals( $merchants ){
        return;
    }

    public function render_toolbar() {
        if( $this->settings['toolbar-setting'] != 1 )
            return;
        
    	$template      = file_get_contents( plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-toolbar.php' );
        $template_data = $this->settings;
        $template_data['plugin-dir-url'] = plugin_dir_url( __FILE__ );
    	$template_data['font-size']      = wp_is_mobile() ? '2vmax' : (int)$template_data['toolbar-pixels'] / 2 . 'px';
        //$template_data['toolbar-custom-css'] = htmlspecialchars( @$template_data['toolbar-custom-css'], ENT_QUOTES, 'UTF-8' );
        //$template_data['display']            = array( 'front' => !is_admin(), 'admin' => is_admin() );
        
        foreach ( $template_data as $macro => $value ) {
        	if( gettype( $value ) == 'string' )
        	   $template = str_replace( "!!$macro!!", $value, $template );
        }
        echo $template;
    }

    public function enqueue_styles( $hook ) {
        if( $this->settings['toolbar-setting'] == 1 ){
            wp_register_style( 
                'font-awesome', 
                plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css',
                array(),
                '4.6.3'
            );

            wp_register_style( 
                'dealsbar_styles', 
                plugin_dir_url( __FILE__ ) . 'css/shareasale-dealsbar.css',
                array(),
                $this->version
            );
            wp_enqueue_style( 'font-awesome' );
            wp_enqueue_style( 'dealsbar_styles' );
        }
    }

    public function enqueue_scripts( $hook ) {
        if( $this->settings['toolbar-setting'] == 1 ){
            wp_register_script(
                    'dealsbar_deals_toolbar',
                    plugin_dir_url( __FILE__ ) . 'js/shareasale-dealsbar-toolbar.js',
                    array('jquery'),
                    $this->version
            );
            wp_enqueue_script( 'dealsbar_deals_toolbar' );

            wp_localize_script(
                'dealsbar_deals_toolbar',
                'dealsbarToolbarSettings',
                array(
                    'start_index' => '', //$random_deal_index, 
                    'deals' =>  '', //$results, 
                    'is_backend' => '' //$is_backend
                )
            );
        }
    }
}    