<?php

// for general information about settings in WordPress read through the following pages:
//  * http://ottodestruct.com/blog/2009/wordpress-settings-api-tutorial/
//  * http://codex.wordpress.org/Settings_API

// the WordLift options identifier.
define('WORDLIFT_OPTIONS', 'wordlift_options');

/**
 * Get the WordLift application key.
 */
function wordlift_configuration_application_key()
{

    // get the plugin options.
    $wordlift_options = get_option(WORDLIFT_OPTIONS);

    return $wordlift_options['application_key'];
}

/**
 * Get the WordLift user id.
 * @return string The user id.
 */
function wordlift_configuration_user_id()
{

    // get the plugin options.
    $wordlift_options = get_option(WORDLIFT_OPTIONS);

    return $wordlift_options['user_id'];
}

/**
 * Get the WordLift dataset name.
 * @return string the dataset name.
 */
function wordlift_configuration_dataset_id()
{

    // get the plugin options.
    $wordlift_options = get_option(WORDLIFT_OPTIONS);

    return $wordlift_options['dataset_name'];
}

/**
 * Get the WordLift analysis name (same as the dataset name).
 * @return string The analysis name.
 */
function wordlift_configuration_analysis_name()
{

    // get the plugin options.
    $wordlift_options = get_option(WORDLIFT_OPTIONS);

    return ( isset( $wordlift_options['analysis_name'] ) ? $wordlift_options['analysis_name'] : '' );
}


/**
 * Get the WordLift site language.
 * @return string The WordLift site language. By default 'en' (English) is returned.
 */
function wordlift_configuration_site_language()
{

    // get the plugin options.
    $wordlift_options = get_option(WORDLIFT_OPTIONS);

    return (empty($wordlift_options['site_language']) ? 'en' : $wordlift_options['site_language']);
}

/**
 * Get the Redlink API URL.
 * @return string The base Redlink API URL.
 */
function wordlift_configuration_redlink_api_url()
{

    return 'https://api.redlink.io/';
}

/**
 * Get the Redlink API version.
 * @return string The redlink API version.
 */
function wordlift_redlink_api_version()
{

    return '1.0-BETA';
}

/**
 * Get the Redlink SPARQL Update URL.
 */
function wordlift_redlink_sparql_update_url()
{

    // get the configuration.
    $api_version = wordlift_redlink_api_version();
    $dataset_id = wordlift_configuration_dataset_id();
    $app_key = wordlift_configuration_application_key();
    $api_base_url = wordlift_configuration_redlink_api_url();

    // construct the API URL.
    return $api_base_url . $api_version . "/data/" . $dataset_id . "/sparql/update?key=" . $app_key;
}

/**
 * Get the Redlink URL to delete a dataset data (doesn't delete the dataset itself).
 * @return string
 */
function rl_empty_dataset_url()
{

    // get the configuration.
    $api_version = wordlift_redlink_api_version();
    $dataset_id = wordlift_configuration_dataset_id();
    $app_key = wordlift_configuration_application_key();
    $api_base_url = wordlift_configuration_redlink_api_url();

    // construct the API URL.
    $url = sprintf('%s%s/data/%s?key=%s', $api_base_url, $api_version, $dataset_id, $app_key);
    return $url;
}

function rl_sparql_select_url()
{

    // get the configuration.
    $api_version = wordlift_redlink_api_version();
    $dataset_id = wordlift_configuration_dataset_id();
    $app_key = wordlift_configuration_application_key();
    $api_base_url = wordlift_configuration_redlink_api_url();

    // construct the API URL.
    $url = sprintf('%s%s/data/%s/sparql/select?key=%s', $api_base_url, $api_version, $dataset_id, $app_key);
    return $url;
}

/**
 * Empty the dataset bound to this WordPress install.
 * @return WP_Response|WP_Error A WP_Response in case of success, otherwise a WP_Error.
 */
function rl_empty_dataset()
{

    // Get the empty dataset URL.
    $url = rl_empty_dataset_url();

    // Prepare the request.
    $args = array_merge_recursive(unserialize(WL_REDLINK_API_HTTP_OPTIONS), array(
        'method' => 'DELETE'
    ));

    // Send the request.
    return wp_remote_request($url, $args);
}

/**
 * Count the number of triples in the dataset.
 * @return array|WP_Error|null An array if successful, otherwise WP_Error or NULL.
 */
function rl_count_triples()
{

    // Set the SPARQL query.
    $sparql = 'SELECT (COUNT(DISTINCT ?s) AS ?subjects) (COUNT(DISTINCT ?p) AS ?predicates) (COUNT(DISTINCT ?o) AS ?objects) ' .
        'WHERE { ?s ?p ?o }';

    // Send the request.
    $response = rl_sparql_select($sparql, 'text/csv');

    // Remove the key from the query.
    $scrambled_url = preg_replace('/key=.*$/i', 'key=<hidden>', rl_sparql_select_url());

    // Return the error in case of failure.
    if (is_wp_error($response) || 200 !== $response['response']['code']) {

        $body = (is_wp_error($response) ? $response->get_error_message() : $response['body']);

        write_log("rl_count_triples : error [ url :: $scrambled_url ][ response :: ");
        write_log("\n" . var_export($response, true));
        write_log("][ body :: ");
        write_log("\n" . $body);
        write_log("]");

        return $response;
    }

    // Get the body.
    $body = $response['body'];

    // Get the values.
    $matches = array();
    if (1 === preg_match('/(\d+),(\d+),(\d+)/im', $body, $matches) && 4 === count($matches)) {

        // Return the counts.
        return array(
            'subjects' => (int)$matches[1],
            'predicates' => (int)$matches[2],
            'objects' => (int)$matches[3]
        );
    }

    // No digits found in the response, return null.
    write_log("rl_count_triples : unrecognized response [ body :: $body ]");
    return null;
}

/**
 * Execute the provided query against the SPARQL SELECT Redlink end-point and return the response.
 * @param string $query A SPARQL query.
 * @param string $accept The mime type for the response format (default = 'text/csv').
 * @return WP_Response|WP_Error A WP_Response instance in successful otherwise a WP_Error.
 */
function rl_sparql_select($query, $accept = 'text/csv')
{

    // Get the SPARQL SELECT URL.
    $url = rl_sparql_select_url();

    // Prepare the SPARQL statement by prepending the default namespaces.
    $sparql = wordlift_get_ns_prefixes() . "\n" . $query;

    // Prepare the request.
    $args = array_merge_recursive(unserialize(WL_REDLINK_API_HTTP_OPTIONS), array(
        'headers' => array(
            'Accept' => $accept
        ),
        'body' => array(
            'query' => $sparql
        )
    ));

    // Send the request.
    return wp_remote_post($url, $args);
}

/**
 * Get the Redlink dataset reindex url.
 * @return string The Redlink dataset reindex url.
 */
function wordlift_redlink_reindex_url()
{

    // get the configuration.
    $api_version = wordlift_redlink_api_version();
    $dataset_id = wordlift_configuration_dataset_id();
    $app_key = wordlift_configuration_application_key();
    $api_base_url = wordlift_configuration_redlink_api_url();

    // construct the API URL.
    return $api_base_url . $api_version . "/data/" . $dataset_id . "/release?key=" . $app_key;
}

/**
 * Get the Redlink API enhance URL.
 * @return string The Redlink API enhance URL.
 */
function wordlift_redlink_enhance_url()
{

    // remove configuration keys from here.
    $api_version = wordlift_redlink_api_version();
    $app_key = wordlift_configuration_application_key();
    $api_base_url = wordlift_configuration_redlink_api_url();
    $analysis_name = wordlift_configuration_analysis_name();

    return $api_base_url . $api_version . '/analysis/' . $analysis_name . '/enhance?key=' . $app_key;
}

/**
 * Check WordLift configuration. If something is missing, display an admin notice.
 */
function wordlift_configuration_check()
{

    // get the plugin options.
    $wordlift_options = get_option(WORDLIFT_OPTIONS);

    if (empty($wordlift_options['application_key'])) {
        add_action('admin_notices', 'wordlift_configuration_admin_notices');
    }
}

/**
 * Display admin notices.
 */
function wordlift_configuration_admin_notices()
{

    // get the settings URL.
    $settings_url = get_admin_url(null, 'options-general.php?page=wordlift');
    ?>
    <div class="error">
        <p><?php printf(__('application-key-not-set', 'wordlift'), $settings_url); ?></p>
    </div>

<?php

}

/**
 * Register WordLift's configuration settings.
 */
function wordlift_configuration_register_settings()
{

    register_setting('wordlift_settings', WORDLIFT_OPTIONS);

    $section_id = 'wordlift_settings_section';
    $section_page = 'wordlift_settings_section_page';

    // 1: the unique id for the section: wordlift_settings_section
    // 2: the title or name of the section: Main Settings
    // 3: callback to display the section: wordlift_settings_text
    // 4: the page name: wordlift_settings_section_page (matching the value used in *wordlift_settings_page*)
    add_settings_section($section_id, __('main-settings-title', 'wordlift'), 'wordlift_settings_text', $section_page);

    // 1: unique id for the field: application_key
    // 2: title for the field: Application Key
    // 3: function callback, to display the input box: application_key_input_box
    // 4: page name that this is attached to: wordlift_settings_section_page
    // 5: id of the settings section: wordlift_settings_section
    add_settings_field('application_key', __('application-key', 'wordlift'), 'wordlift_application_key_input_box',
        $section_page, $section_id);
    add_settings_field('user_id', __('user-id', 'wordlift'), 'wordlift_user_id_input_box',
        $section_page, $section_id);
    add_settings_field('dataset_name', __('dataset-name', 'wordlift'), 'wordlift_dataset_name_input_box',
        $section_page, $section_id);
    add_settings_field('analysis_name', __('analysis-name', 'wordlift'), 'wordlift_analysis_name_input_box',
        $section_page, $section_id);
    add_settings_field('site_language', __('site-language', 'wordlift'), 'wordlift_site_language_input_box',
        $section_page, $section_id);
}

/**
 * Displays information for the section (as callback set using the *add_settings_section* method).
 */
function wordlift_settings_text()
{
    echo '<p>' . __('main-settings-description', 'wordlift') . '</p>';
}

/**
 * Displays the application key input box (as callback set uting the *add_settings_field* method).
 */
function wordlift_application_key_input_box()
{

    // get the existing setting.
    $wordlift_options = get_option(WORDLIFT_OPTIONS);
    echo "<input id='application_key' name='wordlift_options[application_key]' size='40' type='text' value='{$wordlift_options['application_key']}' />";
}

/**
 * Displays the user id input box.
 */
function wordlift_user_id_input_box()
{

    // get the existing setting.
    $wordlift_options = get_option(WORDLIFT_OPTIONS);
    echo "<input id='user_id' name='wordlift_options[user_id]' size='40' type='text' value='{$wordlift_options['user_id']}' />";
}

/**
 * Displays the dataset name input box.
 */
function wordlift_dataset_name_input_box()
{

    // get the existing setting.
    $wordlift_options = get_option(WORDLIFT_OPTIONS);
    echo "<input id='dataset_name' name='wordlift_options[dataset_name]' size='40' type='text' value='{$wordlift_options['dataset_name']}' />";
}

/**
 * Displays the analysis name input box.
 */
function wordlift_analysis_name_input_box()
{

    // get the existing setting.
    $wordlift_options = get_option(WORDLIFT_OPTIONS);
    echo "<input id='analysis_name' name='wordlift_options[analysis_name]' size='40' type='text' value='{$wordlift_options['analysis_name']}' />";
}

/**
 * Displays the default language input box.
 */
function wordlift_site_language_input_box()
{

    // get the existing setting.
    $site_lang = wordlift_configuration_site_language();

    // prepare the language array.
    $langs = array();

    // set the path to the language file.
    $filename = dirname(__FILE__) . '/ISO-639-2_utf-8.txt';

    if (($handle = fopen($filename, 'r')) !== false) {
        while (($data = fgetcsv($handle, 1000, '|')) !== false) {
            if (!empty($data[2])) {
                $code = $data[2];
                $label = htmlentities($data[3]);

                $langs[$code] = $label;

            }
        }
        fclose($handle);
    }

    // sort the languages;
    asort($langs);
    echo "<select id='site_language' name='wordlift_options[site_language]' >";
    foreach ($langs as $code => $label) {
        $selected = ($site_lang === $code ? 'selected' : '');
        echo "<option $selected value=\"$code\">$label</option>";
    }
    echo "</select>";
}

/**
 * Adds the WordLift settings page in the WordPress settings menu.
 */
function wordlift_admin_add_page()
{

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
function wordlift_settings_page()
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
 * @param $links
 * @return mixed
 */
function wordlift_plugin_settings_link($links)
{
    $links[] = '<a href="' . get_admin_url(null, 'options-general.php?page=wordlift') . '">Settings</a>';
    return $links;
}


// call hooks.
add_action('admin_init', 'wordlift_configuration_register_settings');
add_action('admin_init', 'wordlift_configuration_check');
add_action('admin_menu', 'wordlift_admin_add_page');

// add the settings link for the plugin.
add_filter("plugin_action_links_wordlift/wordlift.php", 'wordlift_plugin_settings_link');
