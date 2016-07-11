<?php
error_reporting(E_ALL);
class ShareASale_Dealsbar_Admin {
     /**
   * @var float $version Plugin version, used for cache-busting
   */
	private $wpdb, $version;
 
    public function __construct( $version ) {
        global $wpdb;

        $this->wpdb    = &$wpdb;
        $this->version = $version;
    }

    /**
    * Method to wrap the WordPress wp_enqueue_style() function
    */ 
    public function enqueue_styles() { 
        wp_enqueue_style(
            'shareasale-dealsbar-admin-css',
            plugin_dir_url( __FILE__ ) . 'css/shareasale-dealsbar-admin.css',
            array(),
            $this->version,
            FALSE
        );
 
    }

    /**
    * Method to wrap the WordPress wp_enqueue_script() function
    */
    public function enqueue_scripts( $hook ) {
        $options = get_option( 'dealsbar_options' );

        if( $hook == 'post.php' && @$options['affiliate-id'] ) {

            wp_register_script(
                'shareasale-dealsbar-admin-js',
                plugin_dir_url( __FILE__ ) . 'js/shareasale-dealsbar-admin.js',
                array('jquery'),
                $this->version,
                FALSE
            );

            wp_localize_script( 'shareasale-dealsbar-admin-js', 'shareasale_dealsbar_data', $options );
            wp_enqueue_script( 'shareasale-dealsbar-admin-js' );

            wp_register_script(
                'clipboard',
                plugin_dir_url( __FILE__ ) . 'js/clipboard.min.js',
                array(),
                $this->version,
                FALSE
            );
            wp_enqueue_script( 'clipboard' );
        }
    }

    public function admin_init() {
        $options = get_option( 'dealsbar_options' );
        register_setting( 'dealsbar_options', 'dealsbar_options' /*, array( $this, 'sanitize_settings' ) */ );

        //API settings... so much boilerplate WordPress code.
        //HTML name attributes have hyphens for spaces, therefore PHP array indexes do too (instead of usual _underscore or camelCase) for HTML value attributes
        add_settings_section( 'dealsbar_api', 'API Settings', array( $this, 'render_settings_api_section_text' ), 'shareasale_dealsbar' );
        add_settings_field( 'affiliate-id', 'Affiliate ID', array( $this, 'render_settings_input' ), 'shareasale_dealsbar', 'dealsbar_api', array(
            'label_for'   => 'affiliate-id',
            'id'          => 'affiliate-id',
            'name'        => 'affiliate-id',
            'value'       => !empty( $options['affiliate-id'] ) ? $options['affiliate-id'] : '' ,
            'status'      => '',
            'size'        => 18,
            'type'        => 'text',
            'placeholder' => 'Enter your Affiliate ID',
            'class'       => 'dealsbar-option',
            'extra'       => ''
        ));  
        add_settings_field( 'api-token', 'API Token', array( $this, 'render_settings_input' ), 'shareasale_dealsbar', 'dealsbar_api', array(
            'label_for'   => 'api-token',
            'id'          => 'api-token',
            'name'        => 'api-token',
            'value'       => !empty( $options['api-token'] ) ? $options['api-token'] : '',
            'status'      => '',
            'size'        => 18,
            'type'        => 'text',
            'placeholder' => 'Enter your API Token',
            'class'       => 'dealsbar-option',
            'extra'       => ''
        ));
        add_settings_field( 'api-key', 'API Key', array( $this, 'render_settings_input' ), 'shareasale_dealsbar', 'dealsbar_api', array(
            'label_for'   => 'api-key',
            'id'          => 'api-key',
            'name'        => 'api-key',
            'value'       => !empty( $options['api-key'] ) ? $options['api-key'] : '',
            'status'      => '',
            'size'        => 34,
            'type'        => 'text',
            'placeholder' => 'Enter your API Key',
            'class'       => 'dealsbar-option',
            'extra'       => ''
        ));
          
        //dealsbar settings
        add_settings_section( 'dealsbar_toolbar', 'Dealsbar', array( $this, 'render_settings_toolbar_section_text' ), 'shareasale_dealsbar' );
        add_settings_field( 'toolbar-setting', 'Dealsbar Enabled', array( $this, 'render_settings_input' ), 'shareasale_dealsbar', 'dealsbar_toolbar', array(
            'label_for'   => 'toolbar-setting',
            'id'          => 'toolbar-setting',
            'name'        => 'toolbar-setting',
            'value'       => 1,
            'status'      => checked( @$options['toolbar-setting'], 1, false ),
            'size'        => 1,
            'type'        => 'checkbox',
            'placeholder' => '',
            'class'       => 'dealsbar-option',
            'extra'       => ''
        ));
        add_settings_field( 'toolbar-text', 'Dealsbar Text', array( $this, 'render_settings_input' ), 'shareasale_dealsbar', 'dealsbar_toolbar', array(
            'label_for'   => 'toolbar-text',
            'id'          => 'toolbar-text',
            'name'        => 'toolbar-text',
            'value'       => !empty( $options['toolbar-text'] ) ? $options['toolbar-text'] : '',
            'status'      => disabled( @$options['toolbar-setting'], '', false ),
            'size'        => '34',
            'type'        => 'text',
            'placeholder' => 'Enter your Toolbar Text',
            'class'       => 'dealsbar-option',
            'extra'       => ''
        ));
        add_settings_field( 'toolbar-position-top', 'Dealsbar Position', array( $this, 'render_settings_input' ), 'shareasale_dealsbar', 'dealsbar_toolbar', array(
            'label_for'   => 'toolbar-position-top',
            'id'          => 'toolbar-position-top',
            'name'        => 'toolbar-position',
            'value'       => 'top',
            'status'      => disabled( @$options['toolbar-setting'], '', false ) . checked( @$options['toolbar-position'], 'top', false ),
            'size'        => 1,
            'type'        => 'radio',
            'placeholder' => '',
            'class'       => 'dealsbar-option',
            'extra'       => 'Top'
        ));
        add_settings_field( 'toolbar-position-bottom', '', array( $this, 'render_settings_input' ), 'shareasale_dealsbar', 'dealsbar_toolbar', array(
            'id'          => 'toolbar-position-bottom',
            'name'        => 'toolbar-position',
            'value'       => 'bottom',
            'status'      => disabled( @$options['toolbar-setting'], '', false ) . checked( @$options['toolbar-position'], 'bottom', false ),
            'size'        => 1,
            'type'        => 'radio',
            'placeholder' => '',
            'class'       => 'dealsbar-option',
            'extra'       => 'Bottom'
        ));

        //toolbar-size is not actually a field that will ever be saved by the WP settings API, it's just a jQuery slider for toolbar-pixels field
        add_settings_field( 'toolbar-size', 'Dealsbar Height Slider', array( $this, 'render_settings_slider' ), 'shareasale_dealsbar', 'dealsbar_toolbar', array(
            'label_for' => 'toolbar-size'
        ));
        add_settings_field( 'toolbar-pixels', 'Dealsbar Height (pixels)', array( $this, 'render_settings_input' ), 'shareasale_dealsbar', 'dealsbar_toolbar', array(
            'label_for'   => 'toolbar-pixels',
            'id'          => 'toolbar-pixels',
            'name'        => 'toolbar-pixels',
            'value'       => !empty( $options['toolbar-pixels'] ) ? $options['toolbar-pixels'] : 15,
            'status'      => disabled( @$options['toolbar-setting'], '', false ) . 'min="15" max="60"',
            'size'        => 34,
            'type'        => 'number',
            'placeholder' => '',
            'class'       => 'dealsbar-option',
            'extra'       => ''
        ));
        add_settings_field( 'toolbar-bg-color', 'Dealsbar Background Color', array( $this, 'render_settings_input' ), 'shareasale_dealsbar', 'dealsbar_toolbar', array(    
            'id'          => 'toolbar-bg-color',
            'name'        => 'toolbar-bg-color',
            'value'       => !empty( $options['toolbar-bg-color'] ) ? $options['toolbar-bg-color'] : '#FFFFFF',
            'status'      => disabled( @$options['toolbar-setting'], '', false ),
            'size'        => '',
            'type'        => 'text',
            'placeholder' => '',
            'class'       => 'my-color-field',
            'extra'       => ''
        ));
        add_settings_field( 'toolbar-text-color', 'Dealsbar Text Color', array( $this, 'render_settings_input' ), 'shareasale_dealsbar', 'dealsbar_toolbar', array(    
            'id'          => 'toolbar-text-color',
            'name'        => 'toolbar-text-color',
            'value'       => !empty( $options['toolbar-text-color'] ) ? $options['toolbar-text-color'] : '#000000',
            'status'      => disabled( @$options['toolbar-setting'], '', false ),
            'size'        => '',
            'type'        => 'text',
            'placeholder' => '',
            'class'       => 'my-color-field',
            'extra'       => ''
        ));
        add_settings_field( 'toolbar-custom-css', 'Dealsbar Custom CSS? <br>ex. <i>font-weight: bolder;</i>', array( $this, 'render_settings_input' ), 'shareasale_dealsbar', 'dealsbar_toolbar', array(    
            'id'          => 'toolbar-custom-css',
            'name'        => 'toolbar-custom-css',
            'value'       => !empty( $options['toolbar-custom-css'] ) ? $options['toolbar-custom-css'] : '',
            'status'      => disabled( @$options['toolbar-setting'], '', false ),
            'size'        => 75,
            'type'        => 'text',
            'placeholder' => 'Enter your Toolbar Custom CSS',
            'class'       => 'dealsbar-option',
            'extra'       => ''
        ));

        $results = array();
        //relies on a PHP >=5.3 lambda fn. must do this to get the correct data structure back from the db using WordPress' $wpdb abstraction
        array_walk( 
        	( $this->wpdb->get_results( '
										SELECT DISTINCT
										merchant as value, merchantid as label 
										FROM ' . $this->wpdb->prefix . 'deals
										ORDER BY merchantid
										', 
										OBJECT_K 
									)
								),
            function( $obj ) use ( &$results, $options ){
                $results[$obj->label][] = array( 
                                            'value'    => $obj->value,
                                            'selected' => 
                                            	is_array( @$options['toolbar-merchants'] ) && in_array( $obj->value, @$options['toolbar-merchants'] ) ? 'selected' : ''
                                        	);
            }
        );

        add_settings_field( 'toolbar-merchants', 'Include These Merchants Random Deals (at least one) <br> <i>ctrl+click to select multiple</i>',
        	array( 
        		$this,
        		'render_settings_select'
        	),  'shareasale_dealsbar', 'dealsbar_toolbar', 
        	array(
	            'label_for' => 'toolbar-merchants',
	            'id'        => 'toolbar-merchants',
	            'name'      => 'toolbar-merchants',
	            'status'    => disabled( @$options['toolbar-setting'], '', false ),
	            'optgroups' => $results
        ));
    }

    /**
    * Method to wrap the WordPress admin_menu_page() function
    */
    public function admin_menu() {
        // Add the top-level admin menu
        $page_title = 'ShareASale Dealsbar Settings';
        $menu_title = 'Dealsbar';
        $capability = 'manage_options';
        $menu_slug  = 'dealsbar';
        $function   = array( $this, 'render_settings_page' );
        $icon_url   = plugin_dir_url( __FILE__ ) . 'images/star_big2.png';
        add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url );

        // Add submenu page with same slug as parent to ensure no duplicates
        $sub_menu_title = 'Settings';
        add_submenu_page( $menu_slug, $page_title, $sub_menu_title, $capability, $menu_slug, $function );

        /* Now add the submenu page for Help
        $submenu_page_title = 'ShareASale Dealsbar Help & FAQ';
        $submenu_title = 'Help';
        $submenu_slug = 'dealsbar-help';
        $submenu_function = 'dealsbar_help_page';
        add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
        */
     
    }

    /**
    * Method that displays the markup for the settings page, to be called in the WordPress add_menu_page() function
    */
    public function render_settings_page() {
        require_once plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-settings.php';
    }

    public function render_settings_api_section_text() {
        require_once plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-settings-api-section-text.php';
    }

    public function render_settings_toolbar_section_text() {
        require_once plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-settings-toolbar-section-text.php';
    }

    public function render_settings_slider(){
        require_once plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-settings-slider.php'; 
    }

    //input rendering functions meant to be as reusable as possible    
    public function render_settings_input( $attributes ) {
        $template      = file_get_contents( plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-settings-input.php' );
        $template_data = array_map( 'esc_attr', $attributes );
        
        foreach ( $template_data as $macro => $value ){
          $template = str_replace( "!!$macro!!", $value, $template );
        }
        echo $template; 
    }

    public function render_settings_select( $attributes ){
        $template      = file_get_contents( plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-settings-select.php' );
        $template_data = array_map( function( $attribute ){

            if( gettype( $attribute ) == 'string' ) return esc_attr( $attribute );
            return $attribute;            
        }, $attributes );

        foreach ($template_data as $macro => $value){
            if ( gettype( $value ) == 'string' )
                $template = str_replace( "!!$macro!!", $value, $template );
        }
        $template = str_replace( '!!optgroups!!', $this->render_settings_select_optgroup( $template_data['optgroups'] ), $template );
        echo $template;
    }

    private function render_settings_select_optgroup( $optgroups ){
        if( empty( $optgroups ) ) {
        	//if no Merchants pulled in from ShareASale API yet, create a blank <optgroup><option/><optgroup/>
            $optgroups = array( 'None'  => 
                                        array( 
                                            array(
                                                'value'    => 'No Merchants with deals yet. Click below to save.',
                                                'selected' => 'disabled'
                                            )
                                        )
                                    );
        }
        $template_fragment = '';
        foreach ( $optgroups as $optgroup => $options ) {
        	$html               = file_get_contents( plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-settings-select-optgroup.php' );
        	$template_fragment .= str_replace( array( 
                                                    '!!label!!',
                                                    '!!options!!' 
                                                ), array(
													esc_attr( $optgroup ),
													$this->render_settings_select_optgroup_option( $options )
												), $html );
        }
        return $template_fragment;
    }

    private function render_settings_select_optgroup_option( $options ){
    	$template_fragment = '';

        foreach ( $options as $option ) {
        	$html               = file_get_contents( plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-settings-select-optgroup-option.php' );
            $template_fragment .= str_replace( array( 
            									'!!value!!',
            									'!!selected!!'
            						 		), array( 
            									esc_attr( $option['value'] ),
            									esc_attr( $option['selected'] )
            								), $html );
        }
        return $template_fragment;
    }

    private function sanitize_settings( $settings ){
        $options      = get_option( 'dealsbar_options' );
        $affiliate_id = $settings['affiliate-id'];
        $api_key      = $settings['api-key'];
        $api_token    = $settings['api-token'];
        $req          = new ShareASaleAPI_db();
        // if( array_diff_assoc($settings, $options) )
        //are these new API credentials to check?
        if ($settings['affiliate-id'] != @$options['affiliate-id'] || $settings['api-key'] != @$options['api-key'] || $settings['api-token'] != @$options['api-token']){
            $record = $req->requestAPI('apitokencount', '', '', array('affiliate_id' => $affiliate_id, 'api_key' => $api_key, 'api_token' => $api_token));
            //if the API request didn't work, trigger a settings error
            if( stripos( $record, "Error" ) ){
                add_settings_error( 'dealsbar_API', 'API', 'Your API credentials did not work. Check your affiliate ID, key, and token.  <span style = "font-size: 10px">' . $record . '</span>'  );
                //clear out fields to reset API creds back to null
                $settings['affiliate-id'] = $settings['api-key'] = $settings['api-token'] = '';
              //otherwise it worked!
            }else{
            //if API request to ShareASale successful with new creds, do immediate deals sync after settings successfully updated mainly for first-time settings entries
                add_action('update_option_dealsbar_options','db_do_deal_update');
            }
        }       

        return $settings;
    }
}