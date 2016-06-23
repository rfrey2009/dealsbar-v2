<?php
 
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

        if( $hook == 'post.php' && @$options['AffiliateID'] ) {

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
        register_setting( 'dealsbar_options', 'dealsbar_options'/*, 'dealsbar_sanitize'*/ );

        //API settings
        add_settings_section( 'dealsbar_api', 'API Settings', array( $this, 'render_settings_api_section_text'), 'shareasale_dealsbar' );
        add_settings_field( 'AffiliateID', 'Affiliate ID', array( $this, 'render_settings_input'), 'shareasale_dealsbar', 'dealsbar_api', array(
            'label_for'   => 'AffiliateID',
            'id'          => 'AffiliateID',
            'name'        => 'AffiliateID',
            'value'       => @$options['AffiliateID'],
            'status'      => '',
            'size'        => 16,
            'type'        => 'text',
            'placeholder' => 'Enter your Affiliate ID',
            'class'       => 'dealsbar_option',
            'extra'       => ''
        ));  
        add_settings_field( 'APIToken', 'API Token', array( $this, 'render_settings_input'), 'shareasale_dealsbar', 'dealsbar_api', array(
            'label_for'   => 'APIToken',
            'id'          => 'APIToken',
            'name'        => 'APIToken',
            'value'       => @$options['APIToken'],
            'status'      => '',
            'size'        => 18,
            'type'        => 'text',
            'placeholder' => 'Enter your API Token',
            'class'       => 'dealsbar_option',
            'extra'       => ''
        ));
        add_settings_field( 'APIKey', 'API Key', array( $this, 'render_settings_input'), 'shareasale_dealsbar', 'dealsbar_api', array(
            'label_for'   => 'APIKey',
            'id'          => 'APIKey',
            'name'        => 'APIKey',
            'value'       => @$options['APIKey'],
            'status'      => '',
            'size'        => 34,
            'type'        => 'text',
            'placeholder' => 'Enter your API Key',
            'class'       => 'dealsbar_option',
            'extra'       => ''
        ));
          
        //dealsbar settings
        add_settings_section( 'dealsbar_Toolbar', 'Dealsbar', array( $this, 'render_settings_toolbar_section_text'), 'shareasale_dealsbar' );
        add_settings_field( 'ToolbarSetting', 'Dealsbar Enabled', array( $this, 'render_settings_input'), 'shareasale_dealsbar', 'dealsbar_Toolbar', array(
            'label_for'   => 'ToolbarSetting',
            'id'          => 'ToolbarSetting',
            'name'        => 'ToolbarSetting',
            'value'       => 1,
            'status'      => checked( @$options['ToolbarSetting'], 1, false ),
            'size'        => '',
            'type'        => 'checkbox',
            'placeholder' => '',
            'class'       => 'dealsbar_option',
            'extra'       => ''
        ));
        add_settings_field( 'ToolbarText', 'Dealsbar Text', array( $this, 'render_settings_input'), 'shareasale_dealsbar', 'dealsbar_Toolbar', array(
            'label_for'   => 'ToolbarText',
            'id'          => 'ToolbarText',
            'name'        => 'ToolbarText',
            'value'       => @$options['ToolbarText'],
            'status'      => checked( @$options['ToolbarSetting'], 1, false ) ? '' : 'disabled',
            'size'        => '34',
            'type'        => 'text',
            'placeholder' => 'Enter your Toolbar Text',
            'class'       => 'dealsbar_option',
            'extra'       => ''
        ));
        add_settings_field( 'ToolbarPositionTop', 'Dealsbar Position', array( $this, 'render_settings_input'), 'shareasale_dealsbar', 'dealsbar_Toolbar', array(
            'label_for'   => 'ToolbarPositionTop', //top is default
            'id'          => 'ToolbarPositionTop',
            'name'        => 'ToolbarPosition',
            'value'       => 'top',
            'status'      => checked( @$options['ToolbarSetting'], 1, false ) ? '' : 'disabled',
            'size'        => '',
            'type'        => 'radio',
            'placeholder' => '',
            'class'       => 'dealsbar_option',
            'extra'       => 'Top'
        ));
        add_settings_field( 'ToolbarPositionBottom', '', array( $this, 'render_settings_input'), 'shareasale_dealsbar', 'dealsbar_Toolbar', array(
            'id'          => 'ToolbarPositionBottom',
            'name'        => 'ToolbarPosition',
            'value'       => 'bottom',
            'status'      => checked( @$options['ToolbarSetting'], 1, false ) ? '' : 'disabled',
            'size'        => '',
            'type'        => 'radio',
            'placeholder' => '',
            'class'       => 'dealsbar_option',
            'extra'       => 'Bottom'
        ));

        //ToolbarSize is not actually a field that will ever be saved by the WP settings API, it's just a jQuery slider for ToolbarPixels field
        add_settings_field( 'ToolbarSize', 'Dealsbar Height Slider', array( $this, 'render_settings_slider'), 'shareasale_dealsbar', 'dealsbar_Toolbar', array(
            'label_for' => 'ToolbarSize'
        ));
        add_settings_field( 'ToolbarPixels', 'Dealsbar Height (pixels)', array( $this, 'render_settings_input'), 'shareasale_dealsbar', 'dealsbar_Toolbar', array(
            'label_for'   => 'ToolbarPixels',
            'id'          => 'ToolbarPixels',
            'name'        => 'ToolbarPixels',
            'value'       => @$options['ToolbarPixels'] ? @$options['ToolbarPixels'] : 15,
            'status'      => checked( @$options['ToolbarSetting'], 1, false ) ? 'min="15" max="60"' : 'min="15" max="60" disabled',
            'size'        => 34,
            'type'        => 'number',
            'placeholder' => '',
            'class'       => 'dealsbar_option',
            'extra'       => ''
        ));
        add_settings_field( 'ToolbarBGColor', 'Dealsbar Background Color', array( $this, 'render_settings_input'), 'shareasale_dealsbar', 'dealsbar_Toolbar', array(    
            'id'          => 'ToolbarBGColor',
            'name'        => 'ToolbarBGColor',
            'value'       => @$options['ToolbarBGColor'],
            'status'      => '',
            'size'        => '',
            'type'        => 'text',
            'placeholder' => '',
            'class'       => 'my-color-field',
            'extra'       => ''
        ));
        add_settings_field( 'ToolbarTextColor', 'Dealsbar Text Color', array( $this, 'render_settings_input'), 'shareasale_dealsbar', 'dealsbar_Toolbar', array(    
            'id'          => 'ToolbarTextColor',
            'name'        => 'ToolbarTextColor',
            'value'       => @$options['ToolbarTextColor'],
            'status'      => '',
            'size'        => '',
            'type'        => 'text',
            'placeholder' => '',
            'class'       => 'my-color-field',
            'extra'       => ''
        ));
        add_settings_field( 'ToolbarCustomCSS', 'Dealsbar Custom CSS? <br>ex. <i>font-weight: bolder;</i>', array( $this, 'render_settings_input'), 'shareasale_dealsbar', 'dealsbar_Toolbar', array(    
            'id'          => 'ToolbarCustomCSS',
            'name'        => 'ToolbarCustomCSS',
            'value'       => @$options['ToolbarCustomCSS'],
            'status'      => '',
            'size'        => 75,
            'type'        => 'text',
            'placeholder' => 'Enter your Toolbar Custom CSS',
            'class'       => 'dealsbar_option',
            'extra'       => ''
            ));

        $results = array();
        //relies on a PHP >=5.3 lambda fn. must do this to get the correct data structure back from the db using WordPress' $wpdb abstraction
        array_walk( ( $this->wpdb->get_results( 'SELECT DISTINCT merchant as value, merchantid as label FROM ' . $this->wpdb->prefix . 'deals ORDER BY merchantid', OBJECT_K ) ),
            function( $obj ) use ( &$results, $options ){
                $results[$obj->label][] = array( 
                                            'value'    => $obj->value,
                                            'selected' => ( is_array( @$options['ToolbarMerchants'] ) && in_array( $obj->value, @$options['ToolbarMerchants'] ) ? 'selected' : '' )
                                        );
            }
        );

        add_settings_field( 'ToolbarMerchants', 'Include These Merchants Random Deals (at least one) <br> <i>ctrl+click to select multiple</i>', array( $this, 'render_settings_select' ), 'shareasale_dealsbar', 'dealsbar_Toolbar', array(
            'label_for' => 'ToolbarMerchants',
            'id'        => 'ToolbarMerchants',
            'name'      => 'ToolbarMerchants',
            'status'    => @$options['ToolbarSetting'] ? '' : 'disabled',
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
        add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url);

        // Add submenu page with same slug as parent to ensure no duplicates
        $sub_menu_title = 'Settings';
        add_submenu_page($menu_slug, $page_title, $sub_menu_title, $capability, $menu_slug, $function);

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

    public function render_settings_affiliate_ID_text() {
        require_once plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-settings-affiliate-id-text.php';
    }

    public function render_settings_toolbar_section_text() {
        require_once plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-settings-toolbar-section-text.php';
    }

    public function render_settings_slider(){
        require_once plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-settings-slider.php'; 
    }
    
    public function render_settings_input( $args ) {
        $template  = file_get_contents( plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-settings-input.php' );
        $templateData = array_map( 'esc_attr', $args );
        
        foreach ($templateData as $macro => $value){
          $template = str_replace( "!!$macro!!", $value, $template );
        }
        echo $template; 
    }

    public function render_settings_select( $args ){
        $template  = file_get_contents( plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-settings-select.php' );
        $templateData = array_map( function( $arg ){

            if(gettype($arg) == 'string') return esc_attr($arg);

            return $arg;            

        }, $args );

        foreach ($templateData as $macro => $value){
            if (gettype($value) == 'string')
                $template = str_replace( "!!$macro!!", $value, $template );
        }

        $template = str_replace( '!!optgroups!!', $this->render_settings_select_optgroup( $templateData['optgroups'] ), $template );
        echo $template;
    }

    private function render_settings_select_optgroup( $optgroups ){
        if( empty( $optgroups ) ) {
            $optgroups = array( 'None'  => 
                                        array( 
                                            array(
                                                'value'    => 'No Merchants with deals yet. Click below to save.',
                                                'selected' => 'disabled'
                                            )
                                        )
                                    );
        }
        $template = str_repeat( file_get_contents( plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-settings-select-optgroup.php' ), count($optgroups) );

        foreach ( $optgroups as $optgroup => $options ) {
            $template = preg_replace( '/!!label!!/', $optgroup, $template, 1 );
            $template = preg_replace( '/!!options!!/', $this->render_settings_select_optgroup_option( $options ), $template, 1 );
        }
        return $template;
    }

    private function render_settings_select_optgroup_option( $options ){
        $template = str_repeat( file_get_contents( plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-settings-select-optgroup-option.php' ), count($options) );

        foreach ( $options as $option ) {
            $template = preg_replace( '/!!value!!/', $option['value'], $template, 2 );
            $template = preg_replace( '/!!selected!!/', $option['selected'], $template, 1 );
        }
        return $template;
    }
}