<?php
/**
 * Pages: Admin Settings / Search Keywords page.
 *
 * @since   3.20.0
 * @package Wordlift/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php settings_errors(); ?>

<form action="options.php" method="post">
	<?php
	settings_fields( 'wl_general_settings' );
	do_settings_sections( 'wl_general_settings' );
	submit_button();
	?>
</form>
