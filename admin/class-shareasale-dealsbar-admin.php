<?php
 
class ShareASale_Dealsbar_Admin {
     /**
   * @var float $version Plugin version, used for cache-busting
   */
	private $version;
 
    public function __construct( $version ) {
        $this->version = $version;
    }

    /**
    * Method to wrap the WordPress wp_enqueue_style() function
    */
    public function enqueue_styles() { 
        wp_enqueue_style(
            'shareasale_dealsbar-admin-css',
            plugin_dir_url( __FILE__ ) . 'css/shareasale_dealsbar-admin.css',
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

        if($hook == 'post.php' && $options['Affiliate ID']) {

            wp_register_script(
                'shareasale_dealsbar-admin-js',
                plugin_dir_url( __FILE__ ) . 'js/shareasale_dealsbar-admin.js',
                array('jquery'),
                $this->version,
                FALSE
            );

            wp_localize_script( 'shareasale_dealsbar-admin-js', 'shareasale_dealsbar_data', $options );
            wp_enqueue_script( 'shareasale_dealsbar-admin-js' );

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
        register_setting( 'dealsbar_options', 'dealsbar_options', 'dealsbar_sanitize');

        //API settings
        add_settings_section('dealsbar_api', 'API Settings', array( $this, 'render_settings_api_section_text'), 'dealsbar');
        add_settings_field('AffiliateID', 'Affiliate ID', array( $this, 'render_settings_input'), 'dealsbar', 'dealsbar_api', array(
            'label_for' => 'AffiliateID',
            'id' => 'AffiliateID',
            'name' => 'Affiliate ID',
            'value' => $options['Affiliate ID'],
            'status' => '',
            'size' => 16,
            'type' => 'text'
        ));  
        add_settings_field('APIToken', 'API Token', array( $this, 'render_settings_input'), 'dealsbar', 'dealsbar_api', array(
            'label_for' => 'APIToken',
            'id' => 'APIToken',
            'name' => 'API Token',
            'value' => $options['API Token'],
            'status' => '',
            'size' => 18,
            'type' => 'text'
        ));
        add_settings_field('APIKey', 'API Key', array( $this, 'render_settings_input'), 'dealsbar', 'dealsbar_api', array(
            'label_for' => 'APIKey',
            'id' => 'APIKey',
            'name' => 'API Key',
            'value' => $options['API Key'],
            'status' => '',
            'size' => 34,
            'type' => 'text'
        ));
          
        //dealsbar settings
        add_settings_section('dealsbar_Toolbar', 'Dealsbar', array( $this, 'render_settings_toolbar_section_text'), 'dealsbar');
        add_settings_field('ToolbarSetting', 'Dealsbar Enabled', array( $this, 'render_settings_input'), 'dealsbar', 'dealsbar_Toolbar', array(
            'label_for' => 'ToolbarSetting',
            'id' => 'ToolbarSetting',
            'name' => 'Toolbar Setting',
            'value' => 1,
            'status' => checked( $options['Toolbar Setting'], 1, false ),
            'size' => '',
            'type' => 'checkbox'
        ));
        add_settings_field('ToolbarText', 'Dealsbar Text', array( $this, 'render_settings_input'), 'dealsbar', 'dealsbar_Toolbar', array(
            'label_for' => 'ToolbarText',
            'id' => 'ToolbarText',
            'name' => 'Toolbar Text',
            'value' => $options['Toolbar Text'],
            'status' => checked( $options['Toolbar Setting'], 1, false ) ? '' : 'disabled',
            'size' => '34',
            'type' => 'text'
        ));
        add_settings_field('ToolbarPosition', 'Dealsbar Position', array( $this, 'render_settings_radio'), 'dealsbar', 'dealsbar_Toolbar', array(
            'label_for' => 'ToolbarPositionTop', //top is default
            'status' => checked( $options['Toolbar Setting'], 1, false ) ? '' : 'disabled'
        ));

        //ToolbarSize is not actually a field that will ever be saved by the WP settings API, it's just a jQuery slider for ToolbarPixels field
        add_settings_field('ToolbarSize', 'Dealsbar Height Slider', array( $this, 'render_settings_slider'), 'dealsbar', 'dealsbar_Toolbar', array(
            'label_for' => 'ToolbarSize'
        ));
        add_settings_field('ToolbarPixels', 'Dealsbar Height (pixels)', array( $this, 'render_settings_input'), 'dealsbar', 'dealsbar_Toolbar', array(
            'label_for' => 'ToolbarPixels',
            'id' => 'ToolbarPixels',
            'name' => 'Toolbar Pixels',
            'value' => $options['Toolbar Pixels'] ? $options['Toolbar Pixels'] : 15,
            'status' => checked( $options['Toolbar Setting'], 1, false ) ? 'min="15" max="60"' : 'min="15" max="60" disabled',
            'size' => 34,
            'type' => 'number'
        ));
        add_settings_field('ToolbarBGColor', 'Dealsbar Background Color', array( $this, 'render_settings_input'), 'dealsbar', 'dealsbar_Toolbar', array(    
            'id' => 'ToolbarBGColor',
            'name' => 'Toolbar BGColor',
            'value' => $options['Toolbar BGColor'],
            'status' => 'class = "my-color-field"',
            'size' => '',
            'type' => 'text'
        ));
        add_settings_field('ToolbarTextColor', 'Dealsbar Text Color', array( $this, 'render_settings_input'), 'dealsbar', 'dealsbar_Toolbar', array(    
            'id' => 'ToolbarTextColor',
            'name' => 'Toolbar Text Color',
            'value' => $options['Toolbar Text Color'],
            'status' => 'class = "my-color-field"',
            'size' => '',
            'type' => 'text'
        ));
        add_settings_field('ToolbarCustomCSS', 'Dealsbar Custom CSS? <br>ex. <i>font-weight: bolder;</i>', array( $this, 'render_settings_input'), 'dealsbar', 'dealsbar_Toolbar', array(    
            'id' => 'ToolbarCustomCSS',
            'name' => 'Toolbar Custom CSS',
            'value' => $options['Toolbar Custom CSS'],
            'status' => '',
            'size' => 75,
            'type' => 'text'
        ));
        add_settings_field('ToolbarMerchants', 'Include These Merchants Random Deals (at least one) <br> <i>ctrl+click to select multiple</i>', array( $this, 'render_settings_select'), 'dealsbar', 'dealsbar_Toolbar', array(
            'label_for' => 'ToolbarMerchants',
            'status' => $options['Toolbar Setting'] ? '' : 'disabled'
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
        require_once plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-settings-affiliate-id-text.php';
    }
    
    public function render_settings_input($args) {
        $id    = esc_attr( $args['id'] );
        $name  = esc_attr( $args['name'] );
        $value = esc_attr( $args['value'] );
        $size  = $args['size'] ? 'size = ' . esc_attr( $args['size']) : '' ;
        $type  = esc_attr( $args['type'] );
        echo "<input id = '$id' placeholder = 'Enter Your " . $name . "' type='$type' name='dealsbar_options[$name]' value='$value' $size />"; 
    }

    public function render_settings_radio($args){

        $status  = $args['status'];
        $options = get_option('dealsbar_options');
        $name    = 'Toolbar Position';

        $html  = "<input class = 'dealsbar_option' type='radio' id='ToolbarPositionTop' name='dealsbar_options[$name]' value='top'" . checked( $options[$name], 'top', false ) . $status . "/>";
        $html .= "<label for='ToolbarPositionTop'>Top</label>&nbsp;";
         
        $html .= "<input class = 'dealsbar_option' type='radio' id='ToolbarPositionBottom' name='dealsbar_options[$name]' value='bottom'" . checked( $options[$name], 'bottom', false ) . $status . "/>";
        $html .= "<label for='ToolbarPositionBottom'>Bottom</label>";
         
        echo $html;
    }

    public function render_settings_slider(){ //renders the jquery slider for toolbar settings
        require_once plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-settings-slider.php'; 
    }

    public function render_settings_select($args){ //one-time function that renders select box for toolbar settings

        global $wpdb;
        $options = get_option( 'dealsbar_options' );
        //if no merchants selected, make an empty array for the sake of later arguments
        $options['Toolbar Merchants'] = empty($options['Toolbar Merchants']) ? array() : $options['Toolbar Merchants'];
        //gotta grab what merchants we even have deals for...
        $deals_table = $wpdb->prefix . 'deals';      
        $merchants_with_deals = $wpdb->get_results("SELECT DISTINCT merchant, merchantid FROM $deals_table ORDER BY merchantid");
        //status is disabled if the deals toolbar checkbox wasn't checked
        $status = $args['status'];
        //in case no merchants are selected yet, set the first in the list to selected instead. Must have at least one merchant for deals toolbar to work
        $default = (empty($options['Toolbar Merchants']) ? 'selected ': '');

        echo "<select multiple id='ToolbarMerchants' name='dealsbar_options[Toolbar Merchants][]' $status>";
        if (empty($merchants_with_deals))

        echo '<option disabled>No merchants with deals yet. Click below to save.</option>';

        foreach ($merchants_with_deals as $merchant_with_deal) {

            if($merchant_with_deal->merchantid != $curmerchantid){
              echo '<optgroup label = "Merchant ID: ' . $merchant_with_deal->merchantid . '">';
            }

            echo '<option ' . $default . 'value="' . $merchant_with_deal->merchant . '"' . (in_array($merchant_with_deal->merchant, $options['Toolbar Merchants']) ? 'selected' : '') . '>' . $merchant_with_deal->merchant . '</option>';
                $default = '';
                $curmerchantid = $merchant_with_deal->merchantid;

        }
        echo "</select>";
    }
}