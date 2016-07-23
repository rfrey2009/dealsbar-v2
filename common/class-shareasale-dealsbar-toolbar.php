<?php
class ShareASale_Dealsbar_Toolbar {
	/**
	* @var Wpdb $wpdb WordPress global database connection singleton
	* @var float $version Plugin version, used for cache-busting
	* @var array $settings user's configuration choices for dealsbar toolbar design and Merchants
	* @var array $deals array containing chosen Merchant's deals for the dealsbar toolbar
	*/
	private $wpdb, $version, $settings, $deals;

	public function __construct( $version ) {
		$this->version = $version;
		$this->load_dependencies();
	}

	private function load_dependencies() {
		global $wpdb;

		$this->wpdb     = &$wpdb;
		$this->settings = get_option( 'dealsbar_options' );
		$this->deals    = $this->get_deals( @$this->settings['toolbar-merchants'] );
	}

	private function get_deals( $merchants ) {
		return;
	}

	public function render_custom_css() {
		$custom_inline_styles =
			'div#dealsbar-deals-toolbar{
				background-color: ' . @$this->settings['toolbar-bg-color'] . ';
				color: ' . @$this->settings['toolbar-text-color'] . ';
				height: ' . @$this->settings['toolbar-pixels'] . 'px;      
				font-size: ' . ( wp_is_mobile() ? '2vmax;' : (int) @$this->settings['toolbar-pixels'] / 2 . 'px;' ) . '
				' . @$this->settings['toolbar-position'] . ': 0;
				' . wp_strip_all_tags( @$this->settings['toolbar-custom-css'] ) . '
			}';
		$custom_inline_styles .=
			'#dealsbar-toolbar-ad{
				display: ' . ( ! is_admin() ? 'block' : 'none' ) . '
			}';
		$custom_inline_styles .=
			'#dealsbar-toolbar-warning{
				display: ' . ( is_admin() ? 'block' : 'none' ) . '
			}';

		wp_add_inline_style( 'dealsbar-standard-styles', $custom_inline_styles );
	}

	public function render_toolbar() {
		if ( ! @$this->settings['toolbar-setting'] ) {
			return;
		}

		$template = file_get_contents( plugin_dir_path( __FILE__ ) . 'templates/shareasale-dealsbar-toolbar.php' );
		echo wp_kses( $template, wp_kses_allowed_html( 'post' ) );
	}

	public function enqueue_styles( $hook ) {
		if ( ! @$this->settings['toolbar-setting'] || ( is_admin() && 'toplevel_page_dealsbar' !== $hook  ) ) {
			return;
		}
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
		if ( ! @$this->settings['toolbar-setting'] || ( is_admin() && 'toplevel_page_dealsbar' !== $hook ) ) {
			return;
		}
		wp_enqueue_script(
			'dealsbar-deals-toolbar',
			plugin_dir_url( __FILE__ ) . 'js/shareasale-dealsbar-toolbar.js',
			array( 'jquery' ),
			$this->version
		);

		wp_localize_script(
			'dealsbar-deals-toolbar',
			'dealsbarToolbarSettings',
			array(
				'start_index' => '',//$random_deal_index,
				'deals'       => '',//$results,
				'is_backend'  => '',//$is_backend
			)
		);
	}
}
