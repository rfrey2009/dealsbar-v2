<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

class ShareASale_Dealsbar_Uninstaller {

	public static function uninstall() {
		global $wpdb;
		$deals_table = $wpdb->prefix . 'deals';
		//drop deals table
		$query = 'DROP TABLE ' . $deals_table;
		$wpdb->query( $query );
		//clear crons
		wp_clear_scheduled_hook( 'dealsbardealsupdate' );
		//remove settings
		unregister_setting( 'dealsbar_options','dealsbar_options' );
		delete_option( 'dealsbar_options' );
	}

	public static function disable() {
		wp_clear_scheduled_hook( 'dealsbardealsupdate' );
	}
}
