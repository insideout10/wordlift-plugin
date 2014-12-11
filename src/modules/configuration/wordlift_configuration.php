<?php
/**
 * Handles the WordLift Plugin configuration by providing
 *  * configuration screens
 *  * methods for retrieving configuration data
 */

require_once( 'wordlift_configuration_constants.php' );

/**
 * This function is called by the *wl_admin_menu* hook which is raised when WordLift builds the admin_menu.
 *
 * @since 3.0.0
 *
 * @param string $parent_slug The parent slug for the menu.
 * @param string $capability The required capability to access the page.
 */
function wl_configuration_admin_menu( $parent_slug, $capability ) {

	// see http://codex.wordpress.org/Function_Reference/add_submenu_page
	add_submenu_page(
		$parent_slug, // The parent menu slug, provided by the calling hook.
		__( 'Configuration', 'wordlift' ),  // page title
		__( 'Configuration', 'wordlift' ),  // menu title
		$capability,                   // The required capability, provided by the calling hook.
		'wl_configuration_admin_menu',      // the menu slug
		'wl_configuration_admin_menu_callback' // the menu callback for displaying the page content
	);

}

add_action( 'wl_admin_menu', 'wl_configuration_admin_menu', 10, 2 );

/**
 * Displays the page content.
 *
 * @since 3.0.0
 *
 * @param boolean $display_page_title If true, prints out the page title.
 */
function wl_configuration_admin_menu_callback( $display_page_title = true ) {

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	$page_title = __( 'Configuration', 'wordlift' );

	?>

	<div class="wrap">
		<?php if ( $display_page_title ) {
			echo( "<h2>$page_title</h2>" );
		} ?>

		<br class="clear">

		<?php wl_settings_page(); ?>

	</div>

<?php
}

/**
 * Register WordLift's configuration settings. This method is run via the *admin_init* hook.
 *
 * @since 3.0.0
 */
function wl_settings_register() {

	register_setting( 'wordlift_settings', WL_OPTIONS_NAME );

	$section_id   = 'wordlift_settings_section';
	$section_page = 'wordlift_settings_section_page';

	// 1: the unique id for the section: wordlift_settings_section
	// 2: the title or name of the section: Main Settings
	// 3: callback to display the section: wordlift_settings_text
	// 4: the page name: wordlift_settings_section_page (matching the value used in *wordlift_settings_page*)
	add_settings_section( $section_id, __( 'main-settings-title', 'wordlift' ), 'wl_settings_text', $section_page );

	// Add the setting field for the display as default.
	add_settings_field(
		WL_CONFIG_ENTITY_DISPLAY_AS_DEFAULT_NAME,
		__( 'New Entity Posts are displayed as:', 'wordlift' ),
		'wl_settings_entity_display_as_select',
		$section_page,
		$section_id
	);

	// Add the setting field for the color coding of entities.
	add_settings_field(
		WL_CONFIG_ENABLE_COLOR_CODING_ON_FRONTEND_NAME,
		__( 'Enable color coding of Entities', 'wordlift' ),
		'wl_settings_enable_color_coding_of_entities_select',
		$section_page,
		$section_id
	);

	// 1: unique id for the field: application_key
	// 2: title for the field: Application Key
	// 3: function callback, to display the input box: application_key_input_box
	// 4: page name that this is attached to: wordlift_settings_section_page
	// 5: id of the settings section: wordlift_settings_section
	add_settings_field( WL_CONFIG_API_URL, __( 'API URL', 'wordlift' ), 'wl_settings_api_url_input_box', $section_page, $section_id );
	add_settings_field( WL_CONFIG_APPLICATION_KEY_NAME, __( 'application-key', 'wordlift' ), 'wl_settings_application_key_input_box', $section_page, $section_id );
	add_settings_field( WL_CONFIG_USER_ID_NAME, __( 'user-id', 'wordlift' ), 'wl_settings_user_id_input_box', $section_page, $section_id );
	add_settings_field( WL_CONFIG_DATASET_NAME, __( 'dataset-name', 'wordlift' ), 'wl_settings_dataset_input_box', $section_page, $section_id );
	add_settings_field( WL_CONFIG_DATASET_BASE_URI_NAME, __( 'dataset-base-uri', 'wordlift' ), 'wl_settings_dataset_base_uri_input_box', $section_page, $section_id );
	add_settings_field( WL_CONFIG_ANALYSIS_NAME, __( 'analysis-name', 'wordlift' ), 'wl_settings_analysis_input_box', $section_page, $section_id );
	add_settings_field( WL_CONFIG_SITE_LANGUAGE_NAME, __( 'site-language', 'wordlift' ), 'wl_settings_site_language_input_box', $section_page, $section_id );
}

add_action( 'admin_init', 'wl_settings_register' );

/**
 * Displays information for the section (as callback set using the *add_settings_section* method).
 *
 * @since 3.0.0
 */
function wl_settings_text() {
	echo '<p>' . __( 'main-settings-description', 'wordlift' ) . '</p>';
}

/**
 * Prints out the *entity display as* Select element.
 *
 * @since 3.0.0
 */
function wl_settings_entity_display_as_select() {

	$options = array(
		'index' => __( 'Index', 'wordlift' ),
		'page'  => __( 'Page', 'wordlift' )
	);

	wl_settings_select( WL_CONFIG_ENTITY_DISPLAY_AS_DEFAULT_NAME, $options, wl_config_get_entity_display_as_default() );
}

/**
 * Prints out the *enable color coding of entities* Select element.
 *
 * @since 3.0.0
 */
function wl_settings_enable_color_coding_of_entities_select() {

	$options = array(
		'yes' => __( 'Yes', 'wordlift' ),
		'no'  => __( 'No', 'wordlift' )
	);

	wl_settings_select(
		WL_CONFIG_ENABLE_COLOR_CODING_ON_FRONTEND_NAME,
		$options,
		( wl_config_get_enable_color_coding_of_entities_on_frontend() ? 'yes' : 'no' )
	);
}

/**
 * Displays the API URL input box
 *
 * @since 3.0.0
 */
function wl_settings_api_url_input_box() {

	// Get the setting value.
	$value = wl_config_get_api_url();

	// Call the helper function.
	wl_settings_input_box( WL_CONFIG_API_URL, $value );

}


/**
 * Displays the application key input box (as callback set using the *add_settings_field* method).
 *
 * @since 3.0.0
 */
function wl_settings_application_key_input_box() {

	// Get the setting value.
	$value = wl_config_get_application_key();

	// Call the helper function.
	wl_settings_input_box( WL_CONFIG_APPLICATION_KEY_NAME, $value );

}

/**
 * Displays the user id input box.
 *
 * @since 3.0.0
 */
function wl_settings_user_id_input_box() {

	// Get the setting value.
	$value = wl_config_get_user_id();

	// Call the helper function.
	wl_settings_input_box( WL_CONFIG_USER_ID_NAME, $value );
}

/**
 * Displays the dataset name input box.
 *
 * @since 3.0.0
 */
function wl_settings_dataset_input_box() {
	// Get the setting value.
	$value = wl_config_get_dataset();

	// Call the helper function.
	wl_settings_input_box( WL_CONFIG_DATASET_NAME, $value );
}

/**
 * Displays the analysis name input box.
 *
 * @since 3.0.0
 */
function wl_settings_analysis_input_box() {
	// Get the setting value.
	$value = wl_config_get_analysis();

	// Call the helper function.
	wl_settings_input_box( WL_CONFIG_ANALYSIS_NAME, $value );
}

/**
 * Displays the dataset name input box.
 *
 * @since 3.0.0
 */
function wl_settings_dataset_base_uri_input_box() {
	// Get the setting value.
	$value = wl_config_get_dataset_base_uri();

	// Call the helper function.
	wl_settings_input_box( WL_CONFIG_DATASET_BASE_URI_NAME, $value );
}

/**
 * Displays the default language input box.
 *
 * @since 3.0.0
 */
function wl_settings_site_language_input_box() {

	// prepare the language array.
	$langs = array();

	// set the path to the language file.
	$filename = dirname( __FILE__ ) . '/ISO-639-2_utf-8.txt';

	if ( ( $handle = fopen( $filename, 'r' ) ) !== false ) {
		while ( ( $data = fgetcsv( $handle, 1000, '|' ) ) !== false ) {
			if ( ! empty( $data[2] ) ) {
				$code           = $data[2];
				$label          = htmlentities( $data[3] );
				$langs[ $code ] = $label;
			}
		}
		fclose( $handle );
	}

	// sort the languages;
	asort( $langs );

	wl_settings_select( 'site_language', $langs, wl_config_get_site_language() );
}


/**
 * Create an input box for the specified field.
 *
 * @since 3.0.0
 *
 * @param string $field_name The setting field name.
 * @param string $value The setting value.
 */
function wl_settings_input_box( $field_name, $value ) {

	// get the existing setting.
	$value_e = esc_html( $value );
	echo "<input id='$field_name' name='wordlift_options[$field_name]' size='60' type='text' value='$value_e' />";
}

/**
 * Prints out a Select element with the provided parameters.
 *
 * @since 3.0.0
 *
 * @param string $field_name The field name.
 * @param array $options A hash of option values/texts.
 * @param string $current The current selected value.
 */
function wl_settings_select( $field_name, $options, $current ) {

	echo "<select id='$field_name' name='wordlift_options[$field_name]' >";
	foreach ( $options as $value => $text ) {
		$selected = ( $current === $value ? 'selected' : '' );

		$value_e = esc_attr( $value );
		$text_e  = esc_html( $text );
		echo "<option $selected value=\"$value_e\">$text_e</option>";
	}
	echo '</select>';
}

/**
 * Generates the page content (as callback set using the *add_options_page* method.
 *
 * @since 3.0.0
 */
function wl_settings_page() {

	?>
	<div>
		<h2>WordLift</h2>

		<form action="options.php" method="post">
			<?php settings_fields( 'wordlift_settings' ); ?>
			<?php do_settings_sections( 'wordlift_settings_section_page' ); ?>
			<input name="Submit" type="submit" value="<?php esc_attr_e( 'Save Changes' ); ?>"/>

		</form>

		<div style="margin-top: 100px; font-size: 10px;">The entities blocks are designed by Lukasz M. Pogoda from the
			Noun Project
		</div>
	</div>

<?php

}

/**
 * Create a link to WordLift settings page.
 *
 * @since 3.0.0
 *
 * @param array $links An array of links.
 *
 * @return array An array of links including those added by the plugin.
 */
function wl_settings_links( $links ) {
	array_push( $links, '<a href="' . get_admin_url( null, 'admin.php?page=wl_configuration_admin_menu' ) . '">Settings</a>' );

	return $links;
}

// add the settings link for the plugin.
add_filter( "plugin_action_links_wordlift/wordlift.php", 'wl_settings_links' );

/**
 * Get the default *entity display as* setting.
 *
 * @return string The default setting.
 */
function wl_config_get_entity_display_as_default() {

	return wl_config_get_setting( WL_CONFIG_ENTITY_DISPLAY_AS_DEFAULT_NAME, 'index' );
}

/**
 * Get a configuration setting by its name or return the specified default value.
 *
 * @param string $name The configuration name.
 * @param string $default_value The default value.
 *
 * @return string The setting value or, if not found, the default value.
 */
function wl_config_get_setting( $name, $default_value = '' ) {

	// get the plugin options.
	$options = get_option( WL_OPTIONS_NAME );

	return ( isset( $options[ $name ] ) ? $options[ $name ] : $default_value );
}


/**
 * Get the value for the *enable color coding of entities on front-end* setting.
 *
 * @return boolean True if enabled, otherwise false.
 */
function wl_config_get_enable_color_coding_of_entities_on_frontend() {

	return ( 'yes' === wl_config_get_setting( WL_CONFIG_ENABLE_COLOR_CODING_ON_FRONTEND_NAME, 'yes' ) );
}

/**
 * Get the API URL.
 */
function wl_config_get_api_url() {
	return wl_config_get_setting( WL_CONFIG_API_URL );
}

/**
 * Get the WordLift application key.
 */
function wl_config_get_application_key() {
	return wl_config_get_setting( WL_CONFIG_APPLICATION_KEY_NAME );
}

/**
 * Get the WordLift user id.
 * @return string The user id.
 */
function wl_config_get_user_id() {
	return wl_config_get_setting( WL_CONFIG_USER_ID_NAME );
}

/**
 * Get the WordLift dataset name.
 * @return string the dataset name.
 */
function wl_config_get_dataset() {
	return wl_config_get_setting( WL_CONFIG_DATASET_NAME );
}

/**
 * Get the WordLift analysis name (same as the dataset name).
 * @return string The analysis name.
 */
function wl_config_get_analysis() {
	return wl_config_get_setting( WL_CONFIG_ANALYSIS_NAME );
}


/**
 * Get the WordLift site language.
 * @return string The WordLift site language. By default 'en' (English) is returned.
 */
function wl_config_get_site_language() {
	return wl_config_get_setting( WL_CONFIG_SITE_LANGUAGE_NAME, WL_CONFIG_DEFAULT_SITE_LANGUAGE );
}

/**
 * Get the dataset base URI setting.
 * @return string The dataset base URI.
 */
function wl_config_get_dataset_base_uri() {

	// Get the option value.
	$options = get_option( WL_OPTIONS_NAME );
	$value   = ( isset( $options[ WL_CONFIG_DATASET_BASE_URI_NAME ] ) ? $options[ WL_CONFIG_DATASET_BASE_URI_NAME ] : 'http://data.redlink.io/<your-user-id>/<your-dataset-name>' );

	// Remove any slash at the end.
	if ( 0 < strlen( $value ) && 0 === substr_compare( $value, '/', - 1, 1 ) ) {
		$value = substr( $value, 0, strlen( $value ) - 1 );
	}

	return $value;
}


/**
 * Get the default recursion depth limitation on *entity metadata rendering*.
 *
 * @return string The default setting.
 */
function wl_config_get_recursion_depth() {

	return wl_config_get_setting( WL_CONFIG_RECURSION_DEPTH_ON_ENTITY_METADATA_PRINTING, WL_RECURSION_DEPTH_ON_ENTITY_METADATA_PRINTING );
}

// for general information about settings in WordPress read through the following pages:
//  * http://ottodestruct.com/blog/2009/wordpress-settings-api-tutorial/
//  * http://codex.wordpress.org/Settings_API


/**
 * Check WordLift configuration. If something is missing, display an admin notice.
 *
 * @uses wl_config_validate()
 */
function wl_config_check() {
	if ( false === wl_config_validate() ) {
		add_action( 'admin_notices', 'wl_config_admin_notices' );
	}
}

/**
 * Check WordLift's configuration.
 * @return bool True if the configuration is set otherwise false.
 */
function wl_config_validate() {

	$options = get_option( WL_OPTIONS_NAME );
	if ( false === isset( $options[ WL_CONFIG_APPLICATION_KEY_NAME ] ) ) {
		return false;
	}
	if ( false === isset( $options[ WL_CONFIG_DATASET_NAME ] ) ) {
		return false;
	}
	if ( false === isset( $options[ WL_CONFIG_ANALYSIS_NAME ] ) ) {
		return false;
	}
	if ( false === isset( $options[ WL_CONFIG_DATASET_BASE_URI_NAME ] ) ) {
		return false;
	}
	if ( false === isset( $options[ WL_CONFIG_SITE_LANGUAGE_NAME ] ) ) {
		return false;
	}
	if ( false === isset( $options[ WL_CONFIG_USER_ID_NAME ] ) ) {
		return false;
	}

	return true;
}

/**
 * Display admin notices.
 */
function wl_config_admin_notices() {

	// get the settings URL.
	$settings_url = get_admin_url( null, 'admin.php?page=wl_configuration_admin_menu' );
	?>
	<div class="error">
		<p><?php printf( __( 'application-key-not-set', 'wordlift' ), $settings_url ); ?></p>
	</div>

<?php

}

// call hooks.
add_action( 'admin_init', 'wl_config_check' );
