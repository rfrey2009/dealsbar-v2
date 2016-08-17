<?php
class ShareASale_Dealsbar_Installer {

	private static function load_dependencies() {
		//necessary for using dbDelta() to create and update WordPress tables
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	}

	public static function install() {
		self::load_dependencies();

		update_option( 'dealsbar_options', '' );

		global $wpdb;
		$deals_table = $wpdb->prefix . 'deals';
		$query = 'CREATE TABLE IF NOT EXISTS ' . $deals_table . ' (
			dealid BIGINT( 25 ) NOT NULL ,
			merchantid INT( 10 ) NOT NULL ,
			merchant VARCHAR( 255 ) NOT NULL ,
			startdate DATE NOT NULL ,
			enddate DATE NOT NULL ,
			publishdate DATE NOT NULL ,
			title TEXT NOT NULL ,
			bigimage VARCHAR( 255 ) NOT NULL ,
			trackingurl VARCHAR( 255 ) NOT NULL ,
			smallimage VARCHAR( 255 ) NOT NULL ,
			category TEXT NOT NULL ,
			description TEXT NOT NULL ,
			restrictions TEXT NOT NULL ,
			keywords TEXT NOT NULL ,
			couponcode VARCHAR( 255 ) NOT NULL ,
			editdate DATE NOT NULL ,
			PRIMARY KEY  (dealid)
			) ENGINE = INNODB DEFAULT CHARSET = latin1';

		dbDelta( $query );

		$random_hour = rand( 1,9 );
		//setup the sync for a random time of the day between 1-8PM ET to avoid overloading ShareASale
		$timestamp = strtotime( 'today' . $random_hour . 'PM EST' );

		if ( ! wp_get_schedule( 'dealsbardealsupdate' ) ) {
			wp_schedule_event( $timestamp, 'daily', 'dealsbardealsupdate' );
		}
	}
}
