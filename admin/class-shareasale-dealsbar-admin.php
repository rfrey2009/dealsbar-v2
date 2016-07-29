<?php
class ShareASale_Dealsbar_Admin {
	/**
	* @var Wpdb $wpdb WordPress global database connection singleton
	* @var float $version Plugin version
	*/
	private $wpdb, $version;

	public function __construct( $version ) {
		$this->version = $version;
		$this->load_dependencies();
	}

	private function load_dependencies() {
		require_once plugin_dir_path( __FILE__ ) . '../common/class-shareasale-dealsbar-api.php';
		global $wpdb;

		$this->wpdb = &$wpdb;
	}

	public function enqueue_styles( $hook ) {
		if ( 'toplevel_page_shareasale_dealsbar' === $hook || 'shareasale-dealsbar_page_shareasale_dealsbar_customization' === $hook ) {
				wp_enqueue_style(
					'shareasale-dealsbar-admin-css',
					plugin_dir_url( __FILE__ ) . 'css/shareasale-dealsbar-admin.css',
					array(),
					$this->version
				);

				//jquery ui css i.e. for tabs & slider
				wp_enqueue_style(
					'dealsbar-jquery-custom-css',
					plugin_dir_url( __FILE__ ) . 'css/jquery-ui.css',
					array(),
					'1.11.4'
				);

				wp_enqueue_style( 'wp-color-picker' );
		}
	}

	public function enqueue_scripts( $hook ) {
		if ( 'shareasale-dealsbar_page_shareasale_dealsbar_customization' === $hook ) {
				wp_enqueue_script(
					'shareasale-dealsbar-admin-js',
					plugin_dir_url( __FILE__ ) . 'js/shareasale-dealsbar-admin.js',
					array( 'jquery' ),
					$this->version
				);
				//WP
				wp_enqueue_script( 'wp-color-picker' );
				//jQuery
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-widget' );
				wp_enqueue_script( 'jquery-ui-mouse' );
				wp_enqueue_script( 'jquery-ui-position' );
				wp_enqueue_script( 'jquery-ui-slider' );
		}
	}

	public function admin_init() {
		$options = get_option( 'dealsbar_options' );
		register_setting( 'dealsbar_options', 'dealsbar_options', array( $this, 'sanitize_settings' ) );

		//API settings... so much boilerplate WordPress code.
		//HTML name attributes have hyphens for spaces, therefore PHP array indexes do too (instead of usual _underscore or camelCase) for HTML value attributes
		add_settings_section( 'dealsbar_api', 'API Settings', array( $this, 'render_settings_api_section_text' ), 'shareasale_dealsbar' );
		add_settings_field( 'affiliate-id', 'Affiliate ID', array( $this, 'render_settings_input' ), 'shareasale_dealsbar', 'dealsbar_api',
			array(
				'label_for'   => 'affiliate-id',
				'id'          => 'affiliate-id',
				'name'        => 'affiliate-id',
				'value'       => ! empty( $options['affiliate-id'] ) ? $options['affiliate-id'] : '',
				'status'      => '',
				'size'        => 18,
				'type'        => 'text',
				'placeholder' => 'Enter your Affiliate ID',
				'class'       => 'dealsbar-option',
				'extra'       => '',
		));
		add_settings_field( 'api-token', 'API Token', array( $this, 'render_settings_input' ), 'shareasale_dealsbar', 'dealsbar_api',
			array(
				'label_for'   => 'api-token',
				'id'          => 'api-token',
				'name'        => 'api-token',
				'value'       => ! empty( $options['api-token'] ) ? $options['api-token'] : '',
				'status'      => '',
				'size'        => 20,
				'type'        => 'text',
				'placeholder' => 'Enter your API Token',
				'class'       => 'dealsbar-option',
				'extra'       => '',
		));
		add_settings_field( 'api-secret', 'API Secret', array( $this, 'render_settings_input' ), 'shareasale_dealsbar', 'dealsbar_api',
			array(
				'label_for'   => 'api-secret',
				'id'          => 'api-secret',
				'name'        => 'api-secret',
				'value'       => ! empty( $options['api-secret'] ) ? $options['api-secret'] : '',
				'status'      => '',
				'size'        => 34,
				'type'        => 'text',
				'placeholder' => 'Enter your API Secret',
				'class'       => 'dealsbar-option',
				'extra'       => '',
		));

		//dealsbar settings
		add_settings_section( 'dealsbar_toolbar', 'Dealsbar Settings', array( $this, 'render_settings_toolbar_section_text' ), 'shareasale_dealsbar_customization' );
		//hidden input named same as checkbox to save unchecked 0 value case
		add_settings_field( 'toolbar-setting-hidden', '', array( $this, 'render_settings_input' ), 'shareasale_dealsbar_customization', 'dealsbar_toolbar',
			array(
				'id'          => 'toolbar-setting-hidden',
				'name'        => 'toolbar-setting',
				'value'       => 0,
				'status'      => '',
				'size'        => 1,
				'type'        => 'hidden',
				'placeholder' => '',
				'class'       => 'dealsbar-option-hidden',
				'extra'       => '',
		));
		add_settings_field( 'toolbar-setting', 'Dealsbar Enabled', array( $this, 'render_settings_input' ), 'shareasale_dealsbar_customization', 'dealsbar_toolbar',
			array(
				'label_for'   => 'toolbar-setting',
				'id'          => 'toolbar-setting',
				'name'        => 'toolbar-setting',
				'value'       => 1,
				'status'      => checked( @$options['toolbar-setting'], 1, false ),
				'size'        => 1,
				'type'        => 'checkbox',
				'placeholder' => '',
				'class'       => 'dealsbar-option',
				'extra'       => '',
		));
		add_settings_field( 'toolbar-text', 'Dealsbar Text', array( $this, 'render_settings_input' ), 'shareasale_dealsbar_customization', 'dealsbar_toolbar',
			array(
				'label_for'   => 'toolbar-text',
				'id'          => 'toolbar-text',
				'name'        => 'toolbar-text',
				'value'       => ! empty( $options['toolbar-text'] ) ? $options['toolbar-text'] : '',
				'status'      => disabled( @$options['toolbar-setting'], 0, false ),
				'size'        => '34',
				'type'        => 'text',
				'placeholder' => 'Enter your Toolbar Text',
				'class'       => 'dealsbar-option',
				'extra'       => '',
		));
		add_settings_field( 'toolbar-position-top', 'Dealsbar Position', array( $this, 'render_settings_input' ), 'shareasale_dealsbar_customization', 'dealsbar_toolbar',
			array(
				'label_for'   => 'toolbar-position-top',
				'id'          => 'toolbar-position-top',
				'name'        => 'toolbar-position',
				'value'       => 'top',
				'status'      =>
					disabled( @$options['toolbar-setting'], 0, false ) . checked( @$options['toolbar-position'], 'top', false ) . checked( @$options['toolbar-position'], '', false ),
				'size'        => 1,
				'type'        => 'radio',
				'placeholder' => '',
				'class'       => 'dealsbar-option',
				'extra'       => 'Top',
		));
		add_settings_field( 'toolbar-position-bottom', '', array( $this, 'render_settings_input' ), 'shareasale_dealsbar_customization', 'dealsbar_toolbar',
			array(
				'id'          => 'toolbar-position-bottom',
				'name'        => 'toolbar-position',
				'value'       => 'bottom',
				'status'      => disabled( @$options['toolbar-setting'], 0, false ) . checked( @$options['toolbar-position'], 'bottom', false ),
				'size'        => 1,
				'type'        => 'radio',
				'placeholder' => '',
				'class'       => 'dealsbar-option',
				'extra'       => 'Bottom',
		));

		//toolbar-size is not actually a field that will ever be saved by the WP settings API, it's just a jQuery slider for toolbar-pixels field
		add_settings_field( 'toolbar-size', 'Dealsbar Height Slider', array( $this, 'render_settings_slider' ), 'shareasale_dealsbar_customization', 'dealsbar_toolbar',
			array(
			'label_for' => 'toolbar-size',
		));
		add_settings_field( 'toolbar-pixels', 'Dealsbar Height (pixels)', array( $this, 'render_settings_input' ), 'shareasale_dealsbar_customization', 'dealsbar_toolbar',
			array(
				'label_for'   => 'toolbar-pixels',
				'id'          => 'toolbar-pixels',
				'name'        => 'toolbar-pixels',
				'value'       => ! empty( $options['toolbar-pixels'] ) ? $options['toolbar-pixels'] : 15,
				'status'      => disabled( @$options['toolbar-setting'], 0, false ) . 'min=15 max=60',
				'size'        => 34,
				'type'        => 'number',
				'placeholder' => '',
				'class'       => 'dealsbar-option',
				'extra'       => '',
		));
		add_settings_field( 'toolbar-bg-color', 'Dealsbar Background Color', array( $this, 'render_settings_input' ), 'shareasale_dealsbar_customization', 'dealsbar_toolbar',
			array(
				'id'          => 'toolbar-bg-color',
				'name'        => 'toolbar-bg-color',
				'value'       => ! empty( $options['toolbar-bg-color'] ) ? $options['toolbar-bg-color'] : '#FFFFFF',
				'status'      => disabled( @$options['toolbar-setting'], 0, false ),
				'size'        => '',
				'type'        => 'text',
				'placeholder' => '',
				'class'       => 'my-color-field',
				'extra'       => '',
		));
		add_settings_field( 'toolbar-text-color', 'Dealsbar Text Color', array( $this, 'render_settings_input' ), 'shareasale_dealsbar_customization', 'dealsbar_toolbar',
			array(
				'id'          => 'toolbar-text-color',
				'name'        => 'toolbar-text-color',
				'value'       => ! empty( $options['toolbar-text-color'] ) ? $options['toolbar-text-color'] : '#000000',
				'status'      => disabled( @$options['toolbar-setting'], 0, false ),
				'size'        => '',
				'type'        => 'text',
				'placeholder' => '',
				'class'       => 'my-color-field',
				'extra'       => '',
		));
		add_settings_field( 'toolbar-custom-css', 'Dealsbar Custom CSS <br>ex. <i>font-weight: bolder;</i>', array( $this, 'render_settings_input' ), 'shareasale_dealsbar_customization', 'dealsbar_toolbar',
			array(
				'id'          => 'toolbar-custom-css',
				'name'        => 'toolbar-custom-css',
				'value'       => ! empty( $options['toolbar-custom-css'] ) ? $options['toolbar-custom-css'] : '',
				'status'      => disabled( @$options['toolbar-setting'], 0, false ),
				'size'        => 75,
				'type'        => 'text',
				'placeholder' => 'Enter your Toolbar Custom CSS i.e. font-weight: bolder; text-transform: uppercase;',
				'class'       => 'dealsbar-option',
				'extra'       => '',
		));

		$results = array();
		//relies on a PHP >=5.3 anon fn. must do this to get the correct data structure back from the db using WordPress $wpdb abstraction
		array_walk(
			( $this->wpdb->get_results( '
								SELECT DISTINCT
								merchant as value, merchantid as label 
								FROM ' . $this->wpdb->prefix . 'deals
								ORDER BY merchantid
								', OBJECT_K )
			), function( $obj ) use ( &$results, $options ) {
				$results[ $obj->label ][] = array(
												'value'    => $obj->value,
												'selected' =>
													is_array( @$options['toolbar-merchants'] ) && in_array( $obj->value, @$options['toolbar-merchants'], true ) ? 'selected' : '',
											);
			}
		);

		add_settings_field( 'toolbar-merchants', 'Include These Merchants Random Deals (at least one) <br> <i>ctrl+click to select multiple</i>',
			array( $this, 'render_settings_select' ), 'shareasale_dealsbar_customization', 'dealsbar_toolbar',
			array(
				'label_for' => 'toolbar-merchants',
				'id'        => 'toolbar-merchants',
				'name'      => 'toolbar-merchants',
				'status'    => disabled( @$options['toolbar-setting'], 0, false ),
				'optgroups' => $results,
			)
		);
		add_settings_field( 'toolbar-afftrack', 'Affiliate-Defined Tracking', array( $this, 'render_settings_input' ), 'shareasale_dealsbar_customization', 'dealsbar_toolbar',
			array(
				'id'          => 'toolbar-afftrack',
				'name'        => 'toolbar-afftrack',
				'value'       => ! empty( $options['toolbar-afftrack'] ) ? $options['toolbar-afftrack'] : '',
				'status'      => disabled( @$options['toolbar-setting'], 0, false ),
				'size'        => 63,
				'type'        => 'text',
				'placeholder' => 'Enter your any affiliate-defined/subid tracking for your dealsbar links',
				'class'       => 'dealsbar-option',
				'extra'       => '',
		));
	}

	public function admin_menu() {
		//Add the top-level admin menu
		$page_title = 'ShareASale Dealsbar Settings';
		$menu_title = 'ShareASale Dealsbar';
		$capability = 'manage_options';
		$menu_slug  = 'shareasale_dealsbar';
		$function   = array( $this, 'render_settings_page' );
		$icon_url   = plugin_dir_url( __FILE__ ) . 'images/star_big2.png';
		add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url );

		$sub_menu_title = 'API Settings';
		add_submenu_page( $menu_slug, $page_title, $sub_menu_title, $capability, $menu_slug, $function );

		$submenu_page_title = 'Dealsbar Customization';
	    $submenu_title      = 'Dealsbar Customization';
	    $submenu_slug       = 'shareasale_dealsbar_customization';
	    $submenu_function   = array( $this, 'render_settings_page_submenu' );
	   	add_submenu_page( $menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function );
	}

	public function render_settings_page() {
		include_once 'options-head.php';
		//errors are stylized off add_settings_error() from WordPress. Can't be called here since not submitting to options.php.
		if ( ! function_exists( 'curl_version' ) ) {
			echo '<div id="setting-error-plugin-depends" class="error settings-error notice is-dismissible"> 
						<p>
							<strong>cURL is not enabled on your shop\'s server. Please contact your webhost to have cURL enabled to use automatic reconciliation.</a></strong>
						</p>
						<button type="button" class="notice-dismiss">
							<span class="screen-reader-text">Dismiss this notice.</span>
						</button>
					</div>';
			return;
		}

		require_once plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-settings-api.php';
	}

	public function render_settings_page_submenu() {
		include_once 'options-head.php';
		//errors are stylized off add_settings_error() from WordPress. Can't be called here since not submitting to options.php.
		if ( ! function_exists( 'curl_version' ) ) {
			echo '<div id="setting-error-plugin-depends" class="error settings-error notice is-dismissible"> 
						<p>
							<strong>cURL is not enabled on your shop\'s server. Please contact your webhost to have cURL enabled to use automatic reconciliation.</a></strong>
						</p>
						<button type="button" class="notice-dismiss">
							<span class="screen-reader-text">Dismiss this notice.</span>
						</button>
					</div>';
			return;
		}

		require_once plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-settings-dealsbar.php';
	}

	public function render_settings_api_section_text() {
		require_once plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-settings-api-section-text.php';
	}

	public function render_settings_toolbar_section_text() {
		require_once plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-settings-toolbar-section-text.php';
	}

	public function render_settings_slider() {
		require_once plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-settings-slider.php';
	}

	//render dynamic templates for settings page. methods meant to be as reusable as possible for future settings
	public function render_settings_input( $attributes ) {
		$template      = file_get_contents( plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-settings-input.php' );
		$template_data = array_map( 'esc_attr', $attributes );
		foreach ( $template_data as $macro => $value ) {
			$template = str_replace( "!!$macro!!", $value, $template );
		}
		echo wp_kses( $template, array(
									'input' => array(
										'accept'         => true,
										'align'          => true,
										'alt'            => true,
										'autocomplete'   => true,
										'autofocus'      => true,
										'checked'        => true,
										'class'          => true,
										'dirname'        => true,
										'disabled'       => true,
										'form'           => true,
										'formaction'     => true,
										'formenctype'    => true,
										'formmethod'     => true,
										'formnovalidate' => true,
										'formtarget'     => true,
										'height'         => true,
										'id'             => true,
										'list'           => true,
										'max'            => true,
										'maxlength'      => true,
										'min'            => true,
										'multiple'       => true,
										'name'           => true,
										'pattern'        => true,
										'placeholder'    => true,
										'readonly'       => true,
										'required'       => true,
										'size'           => true,
										'src'            => true,
										'step'           => true,
										'type'           => true,
										'value'          => true,
										'width'          => true,
									),
								)
		);
	}

	public function render_settings_select( $attributes ) {
		$template      = file_get_contents( plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-settings-select.php' );
		$template_data = array_map( function( $attribute ) {
			if ( gettype( $attribute ) === 'string' ) {
				return esc_attr( $attribute );
			}
			return $attribute;
		}, $attributes );

		foreach ( $template_data as $macro => $value ) {
			if ( gettype( $value ) === 'string' ) {
					$template = str_replace( "!!$macro!!", $value, $template );
			}
		}
		$template = str_replace( '!!optgroups!!', $this->render_settings_select_optgroup( $template_data['optgroups'] ), $template );

		echo wp_kses( $template, array(
									'select' => array(
										'autofocus' => true,
										'class'     => true,
										'disabled'  => true,
										'form'      => true,
										'id'        => true,
										'multiple'  => true,
										'name'      => true,
										'required'  => true,
										'size'      => true,
									),
									'optgroup' => array(
										'class'    => true,
										'disabled' => true,
										'id'       => true,
										'label'    => true,
									),
									'option' => array(
										'class'    => true,
										'disabled' => true,
										'id'       => true,
										'label'    => true,
										'selected' => true,
										'value'    => true,
									),
								)
		);
	}

	private function render_settings_select_optgroup( $optgroups ) {
		if ( empty( $optgroups ) ) {
			//if no Merchants pulled in from ShareASale API yet, create a single warning <optgroup><option/><optgroup/>
			$optgroups = array(
				'None' => array(
							array(
								'value'    => 'No Merchants with deals yet. Click below to save.',
								'selected' => 'disabled',
							),
						),
					);
		}

		$template_fragment = '';
		foreach ( $optgroups as $optgroup => $options ) {
			$html               = file_get_contents( plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-settings-select-optgroup.php' );
			$template_fragment .= str_replace( array(
												'!!label!!',
												'!!options!!',
											), array(
												esc_attr( $optgroup ),
												$this->render_settings_select_optgroup_option( $options ),
											),
			$html );
		}
		return $template_fragment;
	}

	private function render_settings_select_optgroup_option( $options ) {
		$template_fragment = '';

		foreach ( $options as $option ) {
			$html 				= file_get_contents( plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-settings-select-optgroup-option.php' );
			$template_fragment .= str_replace( array(
												'!!value!!',
												'!!selected!!',
											), array(
												esc_attr( $option['value'] ),
												esc_attr( $option['selected'] ),
											),
			$html );
		}
		return $template_fragment;
	}

	//add shortcut to settings page from the plugin admin entry for dealsbar
	public function render_settings_shortcut( $links ) {
		$settings_link = '<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/admin.php?page=dealsbar">Settings</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}
	//mainly to sanitize the incoming API credential inputs and check if they're valid
	public function sanitize_settings( $new_settings = array() ) {
		$old_settings      = get_option( 'dealsbar_options' ) ?: array();
		$diff_new_settings = array_diff_assoc( $new_settings, $old_settings );

		if ( isset( $diff_new_settings['affiliate-id'] ) || isset( $diff_new_settings['api-token'] ) || isset( $diff_new_settings['api-secret'] ) ) {
			$shareasale_api = new ShareASale_Dealsbar_API( $new_settings['affiliate-id'], $new_settings['api-token'], $new_settings['api-secret'] );
			$req = $shareasale_api->token_count()->exec();

			if ( ! $req ) {
				add_settings_error(
					'dealsbar_api',
					'api',
					'Your API credentials did not work. Check your affiliate ID, key, and token.
					<span style = "font-size: 10px">'
					. $shareasale_api->get_error_msg() .
					'</span>'
				);
				//if API credentials failed, sanitize those options prior to saving
				$new_settings['affiliate-id'] = $new_settings['api-token'] = $new_settings['api-secret'] = '';
				$new_settings['toolbar-setting'] = 0;
			}
		}
		//array order is important to the merge
		return array_merge( $old_settings, $new_settings );
	}
	/*
	*hooked to run immediately *after* plugin options are saved to db
	*so ShareASale_Dealsbar_Updater() hooked to dealsbardealsupdate scheduled action can also check for new credentials and possibly run a fresh sync
	*/
	public function update_option_dealsbar_options( $old_settings, $new_settings ) {
		$diff_new_settings = array_diff_assoc( $new_settings, (array) $old_settings );
		//if first time or different successful API credentials, immediately do a deal sync
		if ( isset( $diff_new_settings['affiliate-id'] ) || isset( $diff_new_settings['api-token'] ) || isset( $diff_new_settings['api-secret'] ) ) {
			do_action( 'dealsbardealsupdate' );
		}
	}
}
