<?php
/**
 * Install Wizard Header.
 *
 * This file provides the header for the install wizard.
 *
 * @link       https://wordlift.io
 * @since      3.9.0
 *
 * @package    Wordlift
 * @subpackage Wordlift/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta name="viewport" content="width=device-width"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title><?php esc_html_e( 'WordLift &rsaquo; Setup Wizard', 'wordlift' ); ?></title>
	<?php
	wp_enqueue_script( 'jquery' );
	wp_enqueue_media();
	wp_enqueue_style( 'common' );
	wp_print_styles();
	wp_print_scripts();
	?>
</head>
<body>
<div class="wl-setup">
	<a id="close" title="<?php esc_html_e( 'Exit the wizard', 'wordlift' ) ?>"
	   href="<?php esc_url( admin_url() ) ?>"><span
			class="fa fa-times"></span></a>
	<div id="wl-title">
		<div id="wl-logo">
			<span class="bold">Word</span>Lift
		</div>
		<div id="bullets">
			<span class="bullet <?php echo( 0 === $step ? 'active' : '' ); ?>" data-step="welcome"></span>
			<span class="bullet <?php echo( 1 === $step ? 'active' : '' ); ?>" data-step="license"></span>
			<span class="bullet <?php echo( 2 === $step ? 'active' : '' ); ?>" data-step="vocabulary"></span>
			<span class="bullet <?php echo( 3 === $step ? 'active' : '' ); ?>" data-step="language"></span>
			<span class="bullet <?php echo( 4 === $step ? 'active' : '' ); ?>" data-step="publisher"></span>
		</div>
		<div id="topright">
		</div>
		<div style="clear:both"></div>
	</div>
