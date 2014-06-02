<?php
/**
 * This file contains functions related to the WordLift Admin settings page.
 */

/**
 * Register WordLift's configuration settings.
 */
function wl_settings_register()
{

    register_setting('wordlift_settings', WL_OPTIONS_NAME);

    $section_id   = 'wordlift_settings_section';
    $section_page = 'wordlift_settings_section_page';

    // 1: the unique id for the section: wordlift_settings_section
    // 2: the title or name of the section: Main Settings
    // 3: callback to display the section: wordlift_settings_text
    // 4: the page name: wordlift_settings_section_page (matching the value used in *wordlift_settings_page*)
    add_settings_section( $section_id, __('main-settings-title', 'wordlift'), 'wl_settings_text', $section_page);

    // Add the setting field for the display as default.
    add_settings_field(
        WL_CONFIG_ENTITY_DISPLAY_AS_DEFAULT_NAME,
        __( 'New Entity Posts are displayed as:', 'wordlift' ),
        'wl_settings_entity_display_as_select',
        $section_page,
        $section_id
    );

    // 1: unique id for the field: application_key
    // 2: title for the field: Application Key
    // 3: function callback, to display the input box: application_key_input_box
    // 4: page name that this is attached to: wordlift_settings_section_page
    // 5: id of the settings section: wordlift_settings_section
    add_settings_field( WL_CONFIG_APPLICATION_KEY_NAME, __('application-key', 'wordlift'), 'wl_settings_application_key_input_box', $section_page, $section_id);
    add_settings_field( WL_CONFIG_USER_ID_NAME, __('user-id', 'wordlift'), 'wl_settings_user_id_input_box', $section_page, $section_id);
    add_settings_field( WL_CONFIG_DATASET_NAME, __('dataset-name', 'wordlift'), 'wl_settings_dataset_input_box', $section_page, $section_id);
    add_settings_field( WL_CONFIG_DATASET_BASE_URI_NAME, __('dataset-base-uri', 'wordlift'), 'wl_settings_dataset_base_uri_input_box', $section_page, $section_id);
    add_settings_field( WL_CONFIG_ANALYSIS_NAME, __('analysis-name', 'wordlift'), 'wl_settings_analysis_input_box', $section_page, $section_id);
    add_settings_field( WL_CONFIG_SITE_LANGUAGE_NAME, __('site-language', 'wordlift'), 'wl_settings_site_language_input_box', $section_page, $section_id);
}
add_action('admin_init', 'wl_settings_register');

/**
 * Displays information for the section (as callback set using the *add_settings_section* method).
 */
function wl_settings_text()
{
    echo '<p>' . __('main-settings-description', 'wordlift') . '</p>';
}

/**
 * Prints out the *entity display as* Select element.
 */
function wl_settings_entity_display_as_select() {

    $options = array(
        'index' => __( 'Index', 'wordlift' ),
        'page'  => __( 'Page', 'wordlift')
    );

    wl_settings_select( WL_CONFIG_ENTITY_DISPLAY_AS_DEFAULT_NAME, $options, wl_config_get_entity_display_as_default() );
}

/**
 * Displays the application key input box (as callback set uting the *add_settings_field* method).
 */
function wl_settings_application_key_input_box()
{

    // Get the setting value.
    $value = wl_config_get_application_key();

    // Call the helper function.
    wl_settings_input_box( WL_CONFIG_APPLICATION_KEY_NAME, $value );
}

/**
 * Displays the user id input box.
 */
function wl_settings_user_id_input_box()
{

    // Get the setting value.
    $value = wl_config_get_user_id();

    // Call the helper function.
    wl_settings_input_box(WL_CONFIG_USER_ID_NAME, $value);
}

/**
 * Displays the dataset name input box.
 */
function wl_settings_dataset_input_box()
{
    // Get the setting value.
    $value = wl_config_get_dataset();

    // Call the helper function.
    wl_settings_input_box(WL_CONFIG_DATASET_NAME, $value);
}

/**
 * Displays the analysis name input box.
 */
function wl_settings_analysis_input_box()
{
    // Get the setting value.
    $value = wl_config_get_analysis();

    // Call the helper function.
    wl_settings_input_box(WL_CONFIG_ANALYSIS_NAME, $value);
}

/**
 * Displays the dataset name input box.
 */
function wl_settings_dataset_base_uri_input_box()
{
    // Get the setting value.
    $value = wl_config_get_dataset_base_uri();

    // Call the helper function.
    wl_settings_input_box(WL_CONFIG_DATASET_BASE_URI_NAME, $value);
}

/**
 * Displays the default language input box.
 */
function wl_settings_site_language_input_box()
{

    // prepare the language array.
    $langs = array();

    // set the path to the language file.
    $filename = dirname( __FILE__ ) . '/ISO-639-2_utf-8.txt';

    if ( ( $handle = fopen( $filename, 'r' ) ) !== false ) {
        while ( ( $data = fgetcsv( $handle, 1000, '|' ) ) !== false ) {
            if ( ! empty( $data[2] ) ) {
                $code  = $data[2];
                $label = htmlentities( $data[3] );
                $langs[$code] = $label;
            }
        }
        fclose( $handle );
    }

    // sort the languages;
    asort($langs);

    wl_settings_select( 'site_language', $langs, wl_config_get_site_language() );
}


/**
 * Create an input box for the specified field.
 *
 * @param string $field_name The setting field name.
 * @param string $value The setting value.
 */
function wl_settings_input_box($field_name, $value)
{

    // get the existing setting.
    $value_e = esc_html($value);
    echo "<input id='$field_name' name='wordlift_options[$field_name]' size='60' type='text' value='$value_e' />";
}

/**
 * Prints out a Select element with the provided parameters.
 *
 * @param string $field_name The field name.
 * @param array $options A hash of option values/texts.
 * @param string $current The current selected value.
 */
function wl_settings_select( $field_name, $options, $current ) {

    echo "<select id='$field_name' name='wordlift_options[$field_name]' >";
    foreach ( $options as $value => $text ) {
        $selected = ( $current === $value ? 'selected' : '');

        $value_e = esc_attr( $value );
        $text_e  = esc_html( $text );
        echo "<option $selected value=\"$value_e\">$text_e</option>";
    }
    echo '</select>';
}

/**
 * Adds the WordLift settings page in the WordPress settings menu.
 */
function wl_settings_add_page()
{

    // passing the following args:
    // 1: the page title:       WordLift
    // 2: the name of the menu: WordLift
    // 3: the required capabilities: manage_options
    // 4: the slug to the page: wordlift (i.e. /wp-admin/options-general.php?page=wordlift)
    // 5: callback to generate the page content: wordlift_settings_page
    add_options_page('WordLift', 'WordLift', 'manage_options', 'wordlift', 'wl_settings_page');
}
add_action('admin_menu', 'wl_settings_add_page');

/**
 * Generates the page content (as callback set using the *add_options_page* method.
 */
function wl_settings_page()
{

    ?>
    <div>
        <h2>WordLift</h2>

        <form action="options.php" method="post">
            <?php settings_fields('wordlift_settings'); ?>
            <?php do_settings_sections('wordlift_settings_section_page'); ?>
            <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>"/>

        </form>

        <div style="margin-top: 100px; font-size: 10px;">The entities blocks are designed by Lukasz M. Pogoda from the
            Noun Project
        </div>
    </div>

<?php

}

/**
 * Create a link to WordLift settings page.
 * @param array $links An array of links.
 * @return array An array of links including those added by the plugin.
 */
function wl_settings_links($links)
{
    array_push($links, '<a href="' . get_admin_url(null, 'options-general.php?page=wordlift') . '">Settings</a>');
    return $links;
}
// add the settings link for the plugin.
add_filter("plugin_action_links_wordlift/wordlift.php", 'wl_settings_links');