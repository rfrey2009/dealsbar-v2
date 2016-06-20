<?php
	/*
	* Plugin Name: ShareASale Dealsbar
	* Description: Put a custom toolbar on your site populated with ShareASale Merchant deals!
	* Version: 1.2
	* Author: ShareASale
	* License: GPLv2 or later
	*/
 
if ( ! defined( 'WPINC' ) ) {
    die;
}

//require the core plugin class
require_once plugin_dir_path( __FILE__ ) . 'includes/class-shareasale_dealsbar.php';

/**
* Kicks off the plugin init
*/
function run_shareasale_dealsbar() {
 
    $sasdlsbr = new ShareASale_Dealsbar();
    $sasdlsbr->run();
 
}

run_shareasale_dealsbar();