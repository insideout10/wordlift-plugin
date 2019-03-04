<?php
/**
 * Pages: Admin Settings page.
 *
 * @since   3.11.0
 * @package Wordlift/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_tab = 'search-keywords' === filter_input( INPUT_GET, 'tab' )
	? 'search-keywords'
	: 'general';

?>

<div class="wrap" id="wl-settings-page">
    <h2><?php esc_html_e( 'WordLift Settings', 'wordlift' ); ?></h2>
    <h2 class="nav-tab-wrapper">
        <a class="nav-tab<?php echo 'general' === $current_tab ? ' nav-tab-active' : ''; ?>"
           href="<?php echo admin_url( 'admin.php?page=wl_configuration_admin_menu' ); ?>"><?php echo esc_html( __( 'General', 'wordlift' ) ); ?></a>
        <a class="nav-tab<?php echo 'search-keywords' === $current_tab ? ' nav-tab-active' : ''; ?>"
           href="<?php echo admin_url( 'admin.php?page=wl_configuration_admin_menu&tab=search-keywords' ); ?>"><?php echo esc_html( __( 'Search Keywords', 'wordlift' ) ); ?></a>
    </h2>

	<?php require plugin_dir_path( dirname( __FILE__ ) ) . "partials/wordlift-admin-settings-$current_tab-page.php"; ?>

</div>
