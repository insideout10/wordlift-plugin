<?php

// for general information about settings in WordPress read through the following pages:
//  * http://ottodestruct.com/blog/2009/wordpress-settings-api-tutorial/
//  * http://codex.wordpress.org/Settings_API

// the WordLift options identifier.
define('WORDLIFT_OPTIONS', 'wordlift_options');

/**
 * Get the WordLift application key.
 */
function wordlift_configuration_application_key() {

    // get the plugin options.
    $wordlift_options = get_option(WORDLIFT_OPTIONS);

    return $wordlift_options['application_key'];
}

/**
 * Check WordLift configuration. If something is missing, display an admin notice.
 */
function wordlift_configuration_check() {

    // get the plugin options.
    $wordlift_options = get_option(WORDLIFT_OPTIONS);

    if (empty($wordlift_options['application_key'])) {
        add_action('admin_notices', 'wordlift_configuration_admin_notices');
    }
}

/**
 * Display admin notices.
 */
function wordlift_configuration_admin_notices() {

?>
    <div class="error">
            <p>Your WordLift application key is not set.</p>
    </div>

<?php

}

/**
 * Register WordLift's configuration settings.
 */
function wordlift_configuration_register_settings() {

    register_setting('wordlift_settings', WORDLIFT_OPTIONS);

    $section_id   = 'wordlift_settings_section';
    $section_page = 'wordlift_settings_section_page';

    // 1: the unique id for the section: wordlift_settings_section
    // 2: the title or name of the section: Main Settings
    // 3: callback to display the section: wordlift_settings_text
    // 4: the page name: wordlift_settings_section_page (matching the value used in *wordlift_settings_page*)
    add_settings_section($section_id, 'Main Settings', 'wordlift_settings_text', $section_page);

    // 1: unique id for the field: application_key
    // 2: title for the field: Application Key
    // 3: function callback, to display the input box: application_key_input_box
    // 4: page name that this is attached to: wordlift_settings_section_page
    // 5: id of the settings section: wordlift_settings_section
    add_settings_field('application_key', 'Application Key', 'application_key_input_box', $section_page, $section_id);
}

/**
 * Displays information for the section (as callback set using the *add_settings_section* method).
 */
function wordlift_settings_text() {
    echo '<p>Main description of this section here.</p>';
}

/**
 * Displays the application key input box (as callback set uting the *add_settings_field* method).
 */
function application_key_input_box() {

    // get the existing setting.
    $wordlift_options = get_option(WORDLIFT_OPTIONS);
    echo "<input id='application_key' name='wordlift_options[application_key]' size='40' type='text' value='{$wordlift_options['application_key']}' />";
}

/**
 * Adds the WordLift settings page in the WordPress settings menu.
 */
function wordlift_admin_add_page() {

    // passing the following args:
    // 1: the page title:       WordLift
    // 2: the name of the menu: WordLift
    // 3: the required capabilities: manage_options
    // 4: the slug to the page: wordlift (i.e. /wp-admin/options-general.php?page=wordlift)
    // 5: callback to generate the page content: wordlift_settings_page
    add_options_page('WordLift', 'WordLift', 'manage_options', 'wordlift', 'wordlift_settings_page');
}

/**
 * Generates the page content (as callback set using the *add_options_page* method.
 */
function wordlift_settings_page() {

?>
    <div>
        <h2>WordLift</h2>

        Options relating to the Custom Plugin.
        <form action="options.php" method="post">
            <?php settings_fields('wordlift_settings'); ?>
            <?php do_settings_sections('wordlift_settings_section_page'); ?>
            <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />

        </form>
    </div>

<?php

}

// call hooks.
add_action('admin_init', 'wordlift_configuration_register_settings');
add_action('admin_init', 'wordlift_configuration_check');
add_action('admin_menu', 'wordlift_admin_add_page');

