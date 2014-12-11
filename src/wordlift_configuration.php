<?php

// for general information about settings in WordPress read through the following pages:
//  * http://ottodestruct.com/blog/2009/wordpress-settings-api-tutorial/
//  * http://codex.wordpress.org/Settings_API






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
    $settings_url = get_admin_url(null, 'admin.php?page=wl_configuration_admin_menu');
    ?>
    <div class="error">
        <p><?php printf(__('application-key-not-set', 'wordlift'), $settings_url); ?></p>
    </div>

<?php

}

// call hooks.
add_action('admin_init', 'wl_config_check');

