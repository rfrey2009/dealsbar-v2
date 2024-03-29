<?php
	/*
	* Plugin Name: ShareASale Dealsbar
	* Description: Put a custom toolbar on your site populated with ShareASale Merchant deals!
	* Version: 2.0.3
	* Author: ShareASale
	* License: GPLv2 or later
	*/
if ( ! defined( 'WPINC' ) ) {
	die;
}
define( 'SHAREASALE_DEALSBAR_PLUGIN_FILENAME', plugin_basename( __FILE__ ) );
//require the core plugin class
require_once plugin_dir_path( __FILE__ ) . 'common/class-shareasale-dealsbar.php';
/**
* Kicks off the plugin init
*/
function run_shareasale_dealsbar() {

	$sas_dlsbr = new ShareASale_Dealsbar();
	$sas_dlsbr->run();

}
run_shareasale_dealsbar();
