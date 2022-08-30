<?php
/**
 * Pages: Admin Settings page.
 *
 * PHPCS doesn't know this file is loaded inside an existing scope. Variables
 * aren't really global here.
 * PHPCS:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
 *
 * @since   3.11.0
 * @since   3.21.0  Tabs have been filterable.
 * @package Wordlift/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get a list of tabs filtered in by the classes that want to add them.
$admin_tabs = apply_filters( 'wl_admin_page_tabs', array() );
// Generate a list of valid tabs that we could have to validate the input against.
$valid_tabs = array();
foreach ( $admin_tabs as $admin_tab ) {
	$valid_tabs[] = $admin_tab['slug'];
}
$input_tab   = filter_input( INPUT_GET, 'tab' );
$current_tab = ( in_array( $input_tab, $valid_tabs, true ) )
	? $input_tab
	: 'general';

?>

<div class="wrap" id="wl-settings-page">
	<h2><?php esc_html_e( 'WordLift Settings', 'wordlift' ); ?></h2>
	<h2 class="nav-tab-wrapper">
		<a class="nav-tab<?php echo 'general' === $current_tab ? ' nav-tab-active' : ''; ?>"
		   href="<?php echo esc_html( admin_url( 'admin.php?page=wl_configuration_admin_menu' ) ); ?>"><?php echo esc_html( __( 'General', 'wordlift' ) ); ?></a>
		<?php
		foreach ( $admin_tabs as $admin_tab ) {
			?>
			<a class="nav-tab<?php echo esc_attr( $admin_tab['slug'] === $current_tab ? ' nav-tab-active' : '' ); ?>"
				href="<?php echo esc_url( admin_url( 'admin.php?page=wl_configuration_admin_menu&tab=' . $admin_tab['slug'] ) ); ?>"><?php echo esc_html( $admin_tab['title'] ); ?></a>
			<?php
		}
		?>
	</h2>

	<?php require plugin_dir_path( __DIR__ ) . "partials/wordlift-admin-settings-$current_tab-page.php"; ?>

</div>
