<?php
class ShareASale_Dealsbar_Uninstaller {
	/**
	* @var Wpdb $wpdb WordPress global database connection singleton
	* @var float $version Plugin version, used for cache-busting
	*/
	private $wpdb, $version;

	public function __construct( $version ) {
		$this->version = $version;
		$this->load_dependencies();
	}

	private function load_dependencies() {
		global $wpdb;

		$this->wpdb = &$wpdb;
	}

	public function uninstall() {
		$deals_table = $this->wpdb->prefix . 'deals';
		//nuke deals table
		$query = 'DROP TABLE ' . $deals_table;
		$this->wpdb->query( $query );
		//clear crons
		wp_clear_scheduled_hook( 'dealsbardealsupdate' );
		//remove settings
		unregister_setting( 'dealsbar_options','dealsbar_options' );
		delete_option( 'dealsbar_options' );
	}

	public function disable() {
		wp_clear_scheduled_hook( 'dealsbardealsupdate' );
	}
}
