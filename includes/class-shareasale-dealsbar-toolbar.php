<?php
class ShareASale_Dealsbar_Toolbar {
    /**
   * @var float $version Plugin version, used for cache-busting
   * @var Wpdb $wpdb WordPress global database connection singleton
   */
	private $wpdb, $version;
 
    public function __construct( $version ) {
        global $wpdb;

        $this->wpdb    = &$wpdb;
        $this->version = $version;
    }

    public function render_toolbar() {
    	$template      = file_get_contents( plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-toolbar.php' );
    	$template_data = get_option( 'dealsbar_options' );
    	$template_data['font-size']      = $template_data['toolbar-pixels'] / 2; //($is_mobile ? '2vmax;' : (@$options['Toolbar Pixels'] / 2) . 'px;')
    	$template_data['plugin-dir-url'] = plugin_dir_url( __FILE__ );

    	$deals = $this->get_deals( $template_data['merchant-name'] );
        
        foreach ( $template_data as $macro => $value ) {
        	if( gettype( $value ) == 'array' ) 
        		continue;

        	$template = str_replace( "!!$macro!!", $value, $template );
        }
        echo $template;
    }

    private function get_deals( $merchants ){

    	return;

    }
}