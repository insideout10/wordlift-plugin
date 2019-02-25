<?php
/**
 * Pages: Admin Settings page.
 *
 * @since   3.11.0
 * @package Wordlift/admin
 */
?>

<div class="wrap" id="wl-settings-page">
    <h2><?php esc_html_e( 'WorldLift Settings', 'wordlift' ); ?></h2>
    <h2 class="nav-tab-wrapper">
        <a class="nav-tab nav-tab-active"
           href="<?php echo admin_url( 'admin.php?page=wl_configuration_admin_menu' ); ?>"><?php echo esc_html( __( 'General', 'wordlift' ) ); ?></a>
        <a class="nav-tab"
           href="<?php echo admin_url( 'admin.php?page=wl_search_keywords' ); ?>"><?php echo esc_html( __( 'Search Keywords', 'wordlift' ) ); ?></a>
    </h2>

	<?php settings_errors(); ?>

    <form action="options.php" method="post">
		<?php
		settings_fields( 'wl_general_settings' );
		do_settings_sections( 'wl_general_settings' );
		submit_button();
		?>
    </form>
</div>
