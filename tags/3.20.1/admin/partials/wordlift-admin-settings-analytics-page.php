<?php
/**
 * Pages: Analytics Settings
 *
 * @since   3.21.0
 * @package Wordlift/admin
 */

?>
<div class="wrap" id="wl-settings-page">
	<?php settings_errors(); ?>
	<form action="options.php" method="post">
		<?php
		settings_fields( 'wl_analytics_settings' );
		do_settings_sections( 'wl_analytics_settings' );
		submit_button();
		?>
	</form>
</div>
