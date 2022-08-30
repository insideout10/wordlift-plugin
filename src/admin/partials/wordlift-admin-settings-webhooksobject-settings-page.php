<?php
/**
 * HTML for Webhook Settings Tab.
 * Added for feature request 1496
 */
settings_errors( 'wl_webhook_error' );
?>
<form method="post" action="options.php">
	<?php
	settings_fields( 'wl_settings__webhooks' );
	do_settings_sections( 'wl_settings__webhooks' );
	submit_button( 'Save Settings' );
	?>
</form>
