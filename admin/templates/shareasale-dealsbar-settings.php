<div id="shareasale_dealsbar">
<?php
	include_once 'options-head.php'; //to get settings errors set with add_settings_error in the validation/sanitization callback working in the custom menu page

	if ( !current_user_can('manage_options') ) {
	  wp_die( 'You do not have sufficient permissions to access this page.' );
	}

  //stop here if cURL not enabled
  if(!function_exists('curl_version')){
      echo '<b>cURL is not enabled on your server. Please contact your host to have cURL enabled to use this plugin.</b>';
      return;
  }

?>
  <div>    
    <h2><img src = <?php echo '"' . plugin_dir_url( __FILE__ ) . '../images/star_big2.png"' ?>>ShareASale Dealsbar Settings</h2>
    <form action="options.php" method="post">
    <div id = 'dealsbar_options'>
    <?php
      settings_fields( 'dealsbar_options' );
      do_settings_sections( 'shareasale_dealsbar' );
    ?>     
    </div>
    <button id = "dealsbar_options_save" name="Submit">Save Settings</button>
    </form>
  </div> 
</div><!-- #shareasale_dealsbar -->