<?php
/**
 * Handles the WordLift Plugin configuration by providing
 *  * configuration screens
 *  * methods for retrieving configuration data
 */

require_once( 'wordlift_configuration_constants.php' );
require_once( 'wordlift_configuration_settings.php' );

/**
 * This function is called by the *wl_admin_menu* hook which is raised when WordLift builds the admin_menu.
 *
 * @since 3.0.0
 *
 * @param string $parent_slug The parent slug for the menu.
 * @param string $capability  The required capability to access the page.
 */
function wl_configuration_admin_menu( $parent_slug, $capability ) {

	// see http://codex.wordpress.org/Function_Reference/add_submenu_page
	add_submenu_page(
		$parent_slug, // The parent menu slug, provided by the calling hook.
		__( 'WorldLift Settings', 'wordlift' ),  // page title
		__( 'Settings', 'wordlift' ),  // menu title
		$capability,                   // The required capability, provided by the calling hook.
		'wl_configuration_admin_menu',      // the menu slug
		'wl_configuration_admin_menu_callback' // the menu callback for displaying the page content
	);

}

add_action( 'wl_admin_menu', 'wl_configuration_admin_menu', 10, 2 );

/**
 * Displays the settings page content.
 *
 * @since 3.0.0
 */
function wl_configuration_admin_menu_callback() {

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	// Ony show advanced settings tab if the relative constant exists and is set to true.
	$can_show_advanced_settings = defined( 'WL_ENABLE_ADVANCED_CONFIGURATION' ) && WL_ENABLE_ADVANCED_CONFIGURATION;

	?>

	<div class="wrap" >

		<h2 ><?php _e( 'WorldLift Settings', 'wordlift' ); ?></h2 >

		<?php settings_errors(); ?>

		<?php
		$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general_settings';
		?>

		<?php if ( $can_show_advanced_settings ) : ?>
			<a href="?page=<?php echo( $_GET['page'] ); ?>&tab=advanced_settings"
			   class="nav-tab <?php echo 'advanced_settings' == $active_tab ? 'nav-tab-active' : ''; ?>" ><?php esc_attr_e( 'Advanced', 'wordlift' ); ?></a >
		<?php endif; ?>

		<form action="options.php" method="post" >
			<?php
			if ( 'general_settings' === $active_tab ) {
				settings_fields( 'wl_general_settings' );
				do_settings_sections( 'wl_general_settings' );

			} elseif ( $can_show_advanced_settings && 'advanced_settings' === $active_tab ) {
				settings_fields( 'wl_advanced_settings' );
				do_settings_sections( 'wl_advanced_settings' );
			}

			submit_button();
			?>
		</form >

		<div style="margin-top: 100px; font-size: 10px;" >The entities blocks
			are
			designed by Lukasz M. Pogoda from the
			Noun Project
		</div >
	</div >

	<?php
}


/**
 * Configure all the configuration parameters. The configuration parameters are grouped in two tabs:
 *  * General
 *  * Advanced (only available if the WL_ENABLE_ADVANCED_CONFIGURATION constant exists and is set to True)
 *
 * Called by the *admin_init* hook.
 *
 * @since 3.0.0
 */
function wl_configuration_settings() {

	register_setting(
		'wl_general_settings',
		'wl_general_settings',
		'wl_configuration_sanitize_settings'
	);

	add_settings_section(
		'wl_general_settings_section',          // ID used to identify this section and with which to register options
		'',   								// Section header
		'', 								// Callback used to render the description of the section
		'wl_general_settings'              // Page on which to add this section of options
	);

	add_settings_field(
		WL_CONFIG_WORDLIFT_KEY,             // ID used to identify the field throughout the theme
		__( 'WordLift Key', 'wordlift' ),   // The label to the left of the option interface element
		'wl_configuration_input_box',       // The name of the function responsible for rendering the option interface
		'wl_general_settings',         // The page on which this option will be displayed
		'wl_general_settings_section',      // The name of the section to which this field belongs
		array(                              // The array of arguments to pass to the callback. In this case, just a description.
		                                    'id'          => 'wl-key',
		                                    'name'        => 'wl_general_settings[key]',
		                                    'value'       => wl_configuration_get_key(),
		                                    'description' => __( 'Insert the <a href="https://www.wordlift.io/blogger">WordLift Key</a> you received via email.', 'wordlift' ),
		)
	);

	// Entity Base Path input.

	$entity_base_path_args = array(                              // The array of arguments to pass to the callback. In this case, just a description.
	                                                             'id'          => 'wl-entity-base-path',
	                                                             'name'        => 'wl_general_settings[' . Wordlift_Configuration_Service::ENTITY_BASE_PATH_KEY . ']',
	                                                             'value'       => Wordlift_Configuration_Service::get_instance()
	                                                                                                            ->get_entity_base_path(),
	                                                             'description' => __( 'All new pages created with WordLift, will be stored inside your internal vocabulary. You can customize the url pattern of these pages in the field above. Check our <a href="https://wordlift.io/wordlift-user-faqs/#10-why-and-how-should-i-customize-the-url-of-the-entity-pages-created-in-my-vocabulary">FAQs</a> if you need more info.', 'wordlift' ),
	);

	if ( Wordlift_Entity_Service::get_instance()->count() ) {
		// Mark the field readonly, the value can be anything.
		$entity_base_path_args['readonly'] = '';
	}

	add_settings_field(
		Wordlift_Configuration_Service::ENTITY_BASE_PATH_KEY,             // ID used to identify the field throughout the theme
		__( 'Entity Base Path', 'wordlift' ),   // The label to the left of the option interface element
		'wl_configuration_input_box',       // The name of the function responsible for rendering the option interface
		'wl_general_settings',         // The page on which this option will be displayed
		'wl_general_settings_section',      // The name of the section to which this field belongs
		$entity_base_path_args
	);

	// Site Language input.

	add_settings_field(
		WL_CONFIG_SITE_LANGUAGE_NAME,
		__( 'Site Language', 'wordlift' ),
		'wl_configuration_select',
		'wl_general_settings',
		'wl_general_settings_section',
		array(                              // The array of arguments to pass to the callback. In this case, just a description.
		                                    'id'          => 'wl-site-language',
		                                    'name'        => 'wl_general_settings[site_language]',
		                                    'value'       => wl_configuration_get_site_language(),
		                                    'description' => __( 'Each WordLift Key can be used only in one language. Pick yours.', 'wordlift' ),
		                                    'options'     => wl_configuration_get_languages(),
		)
	);

	if ( defined( 'WL_ENABLE_ADVANCED_CONFIGURATION' ) && WL_ENABLE_ADVANCED_CONFIGURATION ) {

		register_setting(
			'wl_advanced_settings',
			'wl_advanced_settings',
			'wl_configuration_sanitize_settings'
		);

		add_settings_section(
			'wl_advanced_settings_section',          // ID used to identify this section and with which to register options
			'Advanced',                              // Title to be displayed on the administration page
			'wl_configuration_advanced_settings_section_callback', // Callback used to render the description of the section
			'wl_advanced_settings'              // Page on which to add this section of options
		);

		add_settings_field(
			WL_CONFIG_API_URL,             // ID used to identify the field throughout the theme
			__( 'API URL', 'wordlift' ),   // The label to the left of the option interface element
			'wl_configuration_input_box',       // The name of the function responsible for rendering the option interface
			'wl_advanced_settings',         // The page on which this option will be displayed
			'wl_advanced_settings_section',      // The name of the section to which this field belongs
			array(                              // The array of arguments to pass to the callback. In this case, just a description.
			                                    'id'          => 'wl-api-url',
			                                    'name'        => 'wl_advanced_settings[api_url]',
			                                    'value'       => wl_configuration_get_api_url(),
			                                    'description' => __( 'The API URL', 'wordlift' ),
			)
		);

		add_settings_field(
			WL_CONFIG_APPLICATION_KEY_NAME,             // ID used to identify the field throughout the theme
			__( 'Redlink Key', 'wordlift' ),   // The label to the left of the option interface element
			'wl_configuration_input_box',       // The name of the function responsible for rendering the option interface
			'wl_advanced_settings',         // The page on which this option will be displayed
			'wl_advanced_settings_section',      // The name of the section to which this field belongs
			array(                              // The array of arguments to pass to the callback. In this case, just a description.
			                                    'id'          => 'wl-redlink-key',
			                                    'name'        => 'wl_advanced_settings[redlink_key]',
			                                    'value'       => wl_configuration_get_redlink_key(),
			                                    'description' => __( 'The Redlink key', 'wordlift' ),
			)
		);

		add_settings_field(
			WL_CONFIG_USER_ID_NAME,             // ID used to identify the field throughout the theme
			__( 'Redlink User Id', 'wordlift' ),   // The label to the left of the option interface element
			'wl_configuration_input_box',       // The name of the function responsible for rendering the option interface
			'wl_advanced_settings',         // The page on which this option will be displayed
			'wl_advanced_settings_section',      // The name of the section to which this field belongs
			array(                              // The array of arguments to pass to the callback. In this case, just a description.
			                                    'id'          => 'wl-redlink-user-id',
			                                    'name'        => 'wl_advanced_settings[redlink_user_id]',
			                                    'value'       => wl_configuration_get_redlink_user_id(),
			                                    'description' => __( 'The Redlink User Id', 'wordlift' ),
			)
		);

		add_settings_field(
			WL_CONFIG_DATASET_NAME,             // ID used to identify the field throughout the theme
			__( 'Redlink Dataset name', 'wordlift' ),   // The label to the left of the option interface element
			'wl_configuration_input_box',       // The name of the function responsible for rendering the option interface
			'wl_advanced_settings',         // The page on which this option will be displayed
			'wl_advanced_settings_section',      // The name of the section to which this field belongs
			array(                              // The array of arguments to pass to the callback. In this case, just a description.
			                                    'id'          => 'wl-redlink-dataset-name',
			                                    'name'        => 'wl_advanced_settings[redlink_dataset_name]',
			                                    'value'       => wl_configuration_get_redlink_dataset_name(),
			                                    'description' => __( 'The Redlink Dataset Name', 'wordlift' ),
			)
		);

		add_settings_field(
			WL_CONFIG_DATASET_BASE_URI_NAME,             // ID used to identify the field throughout the theme
			__( 'Redlink Dataset URI', 'wordlift' ),   // The label to the left of the option interface element
			'wl_configuration_input_box',       // The name of the function responsible for rendering the option interface
			'wl_advanced_settings',         // The page on which this option will be displayed
			'wl_advanced_settings_section',      // The name of the section to which this field belongs
			array(                              // The array of arguments to pass to the callback. In this case, just a description.
			                                    'id'          => 'wl-redlink-dataset-uri',
			                                    'name'        => 'wl_advanced_settings[redlink_dataset_uri]',
			                                    'value'       => wl_configuration_get_redlink_dataset_uri(),
			                                    'description' => __( 'The Redlink Dataset URI', 'wordlift' ),
			)
		);

		add_settings_field(
			WL_CONFIG_ANALYSIS_NAME,             // ID used to identify the field throughout the theme
			__( 'Redlink Application Name', 'wordlift' ),   // The label to the left of the option interface element
			'wl_configuration_input_box',       // The name of the function responsible for rendering the option interface
			'wl_advanced_settings',         // The page on which this option will be displayed
			'wl_advanced_settings_section',      // The name of the section to which this field belongs
			array(                              // The array of arguments to pass to the callback. In this case, just a description.
			                                    'id'          => 'wl-redlink-application-name',
			                                    'name'        => 'wl_advanced_settings[redlink_application_name]',
			                                    'value'       => wl_configuration_get_redlink_application_name(),
			                                    'description' => __( 'The Redlink Application Name', 'wordlift' ),
			)
		);
	}
}

add_action( 'admin_init', 'wl_configuration_settings' );

/**
 * Display the advanced settings description. Called from a hook set by *wl_configuration_settings*.
 *
 * @since 3.0.0
 */
function wl_configuration_advanced_settings_section_callback() {

	// TODO: set the following text.
	?>
	Configure WordLift advanced options.
	<?php
}

/**
 * Sanitize the configuration settings to be stored. Configured as a hook from *wl_configuration_settings*.
 *
 * @since 3.0.0
 *
 * @param array $input The configuration settings array.
 *
 * @return mixed
 */
function wl_configuration_sanitize_settings( $input ) {

	// TODO: add sanitization checks.
	return apply_filters( 'wl_configuration_sanitize_settings', $input, $input );

}

/**
 * Draw an input text with the provided parameters.
 *
 * @since 3.0.0
 *
 * @param array $args An array of configuration parameters.
 */
function wl_configuration_input_box( $args ) {
	?>
	<input type="text" id="<?php echo esc_attr( $args['id'] ); ?>"
	       name="<?php echo esc_attr( $args['name'] ); ?>"
	       value="<?php echo esc_attr( $args['value'] ); ?>"
		   <?php if ( isset( $args['readonly'] ) ) { ?>readonly<?php } ?>
	/>

	<?php
	if ( isset( $args['description'] ) ) {
			?>
			<p><?php echo $args['description'];?></p>
			<?php
	}

}

/**
 * Display a select.
 *
 * @deprecated only used by the languages select.
 *
 * @see        https://github.com/insideout10/wordlift-plugin/issues/349
 *
 * @since      3.0.0
 *
 * @param array $args The select configuration parameters.
 */
function wl_configuration_select( $args ) {
	?>

	<select id="<?php echo esc_attr( $args['id'] ); ?>"
	        name="<?php echo esc_attr( $args['name'] ); ?>" >
		<?php
		// Print all the supported language, preselecting the one configured in WP (or English if not supported).
		// We now use the `Wordlift_Languages` class which provides the list of languages supported by WordLift.
		// See https://github.com/insideout10/wordlift-plugin/issues/349

		// Get WordLift's supported languages.
		$languages = Wordlift_Languages::get_languages();

		// If we support WP's configured language, then use that, otherwise use English by default.
		$language = isset( $languages[ $args['value'] ] ) ? $args['value'] : 'en';

		foreach ( $languages as $code => $label ) { ?>
			<option
				value="<?php echo esc_attr( $code ) ?>" <?php echo selected( $code, $language, false ) ?>><?php echo esc_html( $label ) ?></option >
		<?php } ?>
	</select >

	<?php
	if ( isset( $args['description'] ) ) {
			?>
			<p><?php echo $args['description'];?></p>
			<?php
	}
}

/**
 * Display a checkbox.
 *
 * @since 3.0.0
 *
 * @param array $args The checkbox parameters.
 */
function wl_configuration_checkbox( $args ) {
	?>

	<input type="checkbox" id="<?php echo esc_attr( $args['id'] ); ?>"
	       name="<?php echo esc_attr( $args['name'] ); ?>"
	       value="1" <?php checked( 1, $args['value'], true ); ?>/>

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
function wl_configuration_settings_links( $links ) {

	// TODO: this link is different within SEO Ultimate.
	array_push( $links, '<a href="' . get_admin_url( null, 'admin.php?page=wl_configuration_admin_menu' ) . '">Settings</a>' );

	return $links;
}

// add the settings link for the plugin.
add_filter( 'plugin_action_links_wordlift/wordlift.php', 'wl_configuration_settings_links' );


/**
 * Get the available languages.
 *
 * @since 3.0.0
 *
 * @return array An array of languages key values (key being the language code and values the language names).
 */
function wl_configuration_get_languages() {

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

	return $langs;

}


/**
 * Get the default recursion depth limitation on *entity metadata rendering*.
 *
 * @deprecated
 * @return string The default setting.
 */
function wl_config_get_recursion_depth() {

	// get the plugin options.
	$options = get_option( WL_OPTIONS_NAME );

	return ( isset( $options[ WL_CONFIG_RECURSION_DEPTH_ON_ENTITY_METADATA_PRINTING ] )
	         && is_numeric( $options[ WL_CONFIG_RECURSION_DEPTH_ON_ENTITY_METADATA_PRINTING ] )
		? $options[ WL_CONFIG_RECURSION_DEPTH_ON_ENTITY_METADATA_PRINTING ]
		: WL_RECURSION_DEPTH_ON_ENTITY_METADATA_PRINTING );
}

/**
 * Intercept the change of the WordLift key in order to set the dataset URI.
 *
 * @since 3.0.0
 *
 * @param array $old_value The old settings.
 * @param array $new_value The new settings.
 */
function wl_configuration_update_key( $old_value, $new_value ) {

	// wl_write_log( "Going to request set redlink dataset uri if needed" );

	// Check the old key value and the new one. We're going to ask for the dataset URI only if the key has changed.
	$old_key = isset( $old_value['key'] ) ? $old_value['key'] : '';
	$new_key = isset( $new_value['key'] ) ? $new_value['key'] : '';

	// wl_write_log( "[ old value :: $old_key ][ new value :: $new_key ]" );

	// If the key hasn't changed, don't do anything.
	// WARN The 'update_option' hook is fired only if the new and old value are not equal
	if ( $old_key === $new_key ) {
		return;
	}

	// If the key is empty, empty the dataset URI.
	if ( '' === $new_key ) {
		wl_configuration_set_redlink_dataset_uri( '' );
	}

	// Request the dataset URI.
	$response = wp_remote_get( wl_configuration_get_accounts_by_key_dataset_uri( $new_key ), unserialize( WL_REDLINK_API_HTTP_OPTIONS ) );

	// If the response is valid, then set the value.
	if ( ! is_wp_error( $response ) && 200 === (int) $response['response']['code'] ) {

		// wl_write_log( "[ Retrieved dataset :: " . $response['body'] . " ]" );
		wl_configuration_set_redlink_dataset_uri( $response['body'] );

	} else {
		wl_write_log( 'Error on dataset uri remote retrieving [ ' . var_export( $response, true ) . ' ]' );
	}

}

add_action( 'update_option_wl_general_settings', 'wl_configuration_update_key', 10, 2 );
