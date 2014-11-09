<?php

// for general information about settings in WordPress read through the following pages:
//  * http://ottodestruct.com/blog/2009/wordpress-settings-api-tutorial/
//  * http://codex.wordpress.org/Settings_API

/**
 * Get a configuration setting by its name or return the specified default value.
 * @param string $name The configuration name.
 * @param string $default_value The default value.
 * @return string The setting value or, if not found, the default value.
 */
function wl_config_get_setting($name, $default_value = '') {

    // get the plugin options.
    $options = get_option( WL_OPTIONS_NAME );
    return ( isset( $options[$name] ) ? $options[$name] : $default_value );
}

/**
 * Get the WordLift application key.
 */
function wl_config_get_application_key()
{
    return wl_config_get_setting(WL_CONFIG_APPLICATION_KEY_NAME);
}

/**
 * Get the WordLift user id.
 * @return string The user id.
 */
function wl_config_get_user_id()
{
    return wl_config_get_setting(WL_CONFIG_USER_ID_NAME);
}

/**
 * Get the WordLift dataset name.
 * @return string the dataset name.
 */
function wl_config_get_dataset()
{
    return wl_config_get_setting(WL_CONFIG_DATASET_NAME);
}

/**
 * Get the WordLift analysis name (same as the dataset name).
 * @return string The analysis name.
 */
function wl_config_get_analysis()
{
    return wl_config_get_setting(WL_CONFIG_ANALYSIS_NAME);
}


/**
 * Get the WordLift site language.
 * @return string The WordLift site language. By default 'en' (English) is returned.
 */
function wl_config_get_site_language()
{
    return wl_config_get_setting(WL_CONFIG_SITE_LANGUAGE_NAME, WL_CONFIG_DEFAULT_SITE_LANGUAGE);
}

/**
 * Get the dataset base URI setting.
 * @return string The dataset base URI.
 */
function wl_config_get_dataset_base_uri()
{

    // Get the option value.
    $options = get_option(WL_OPTIONS_NAME);
    $value = (isset($options[WL_CONFIG_DATASET_BASE_URI_NAME]) ? $options[WL_CONFIG_DATASET_BASE_URI_NAME] : 'http://data.redlink.io/<your-user-id>/<your-dataset-name>');

    // Remove any slash at the end.
    if (0 < strlen($value) && 0 === substr_compare($value, '/', -1, 1)) {
        $value = substr($value, 0, strlen($value) - 1);
    }

    return $value;
}

/**
 * Get the default *entity display as* setting.
 *
 * @return string The default setting.
 */
function wl_config_get_entity_display_as_default() {

    return wl_config_get_setting( WL_CONFIG_ENTITY_DISPLAY_AS_DEFAULT_NAME, 'index' );
}

/**
 * Get the default recursion depth limitation on *entity metadata rendering*.
 *
 * @return string The default setting.
 */
function wl_config_get_recursion_depth() {

    return wl_config_get_setting( WL_CONFIG_RECURSION_DEPTH_ON_ENTITY_METADATA_PRINTING, WL_RECURSION_DEPTH_ON_ENTITY_METADATA_PRINTING );
}

/**
 * Get the value for the *enable color coding of entities on front-end* setting.
 *
 * @return boolean True if enabled, otherwise false.
 */
function wl_config_get_enable_color_coding_of_entities_on_frontend( ) {

    return ( 'yes' === wl_config_get_setting( WL_CONFIG_ENABLE_COLOR_CODING_ON_FRONTEND_NAME, 'yes' ) );
}


/**
 * Check WordLift configuration. If something is missing, display an admin notice.
 *
 * @uses wl_config_validate()
 */
function wl_config_check()
{
    if (false === wl_config_validate()) {
        add_action('admin_notices', 'wl_config_admin_notices');
    }
}

/**
 * Check WordLift's configuration.
 * @return bool True if the configuration is set otherwise false.
 */
function wl_config_validate()
{

    $options = get_option(WL_OPTIONS_NAME);
    if (false === isset($options[WL_CONFIG_APPLICATION_KEY_NAME])) {
        return false;
    }
    if (false === isset($options[WL_CONFIG_DATASET_NAME])) {
        return false;
    }
    if (false === isset($options[WL_CONFIG_ANALYSIS_NAME])) {
        return false;
    }
    if (false === isset($options[WL_CONFIG_DATASET_BASE_URI_NAME])) {
        return false;
    }
    if (false === isset($options[WL_CONFIG_SITE_LANGUAGE_NAME])) {
        return false;
    }
    if (false === isset($options[WL_CONFIG_USER_ID_NAME])) {
        return false;
    }

    return true;
}

/**
 * Display admin notices.
 */
function wl_config_admin_notices()
{

    // get the settings URL.
    $settings_url = get_admin_url(null, 'options-general.php?page=wordlift');
    ?>
    <div class="error">
        <p><?php printf(__('application-key-not-set', 'wordlift'), $settings_url); ?></p>
    </div>

<?php

}

// call hooks.
add_action('admin_init', 'wl_config_check');

