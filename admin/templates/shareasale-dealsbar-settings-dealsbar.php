<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>

<div id = "shareasale-dealsbar">
	<div class = "wrap">    
		<h2>
			<img id = "shareasale-logo" src = <?php echo '"' . plugin_dir_url( __FILE__ ) . '../images/star_big2.png"' ?>> ShareASale Dealsbar API Settings
		</h2>
		<h2 class = "nav-tab-wrapper">
			<a href = "?page=shareasale_dealsbar" class = "nav-tab">
				API Settings
			</a>
			<a href = "?page=shareasale_dealsbar_customization" class = "nav-tab nav-tab-active">
				Dealsbar Customization
			</a>
		</h2>
		<form action = "options.php" method = "post">
			<div id = 'dealsbar-options'>
			<?php
				settings_fields( 'dealsbar_options' );
				do_settings_sections( 'shareasale_dealsbar_customization' );
			?>     
			</div>
			<button id = "dealsbar-options-save" name = "Submit">Save Settings</button>
		</form>
	</div>
</div>
