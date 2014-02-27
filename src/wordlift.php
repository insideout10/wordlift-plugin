<?php
/*
Plugin Name: WordLift
Plugin URI: http://wordlift.it
Description: Supercharge your WordPress Site with Smart Tagging and #Schemaorg support - a brand new way to write, organise and publish your contents to the Linked Data Cloud.
Version: 3.0.0-SNAPSHOT
Author: InSideOut10
Author URI: http://www.insideout.io
License: APL
*/

if (!function_exists('write_log')) {
    function write_log ( $log )  {
        if ( true === WP_DEBUG ) {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
            error_log( $log );
            }
        }
    }
}

// Define the basic options for HTTP calls to REDLINK.
define( 'WL_REDLINK_API_HTTP_OPTIONS', serialize( array(
    'timeout'     => 60,
    'redirection' => 5,
    'httpversion' => '1.1',
    'blocking'    => true,
    'cookies'     => array()
) ) );


/**
 * Get the URL of the specified physical file.
 * @param string $file The path to the file from the plugin root folder.
 * @return string The URL to the file.
 */
function wordlift_get_url($file)
{

    // if WordLift is set into development mode, then provide a static URL, as development is done with symbolic link.
    if (defined('WORDLIFT_DEVELOPMENT')) {
        return '/wp-content/plugins/wordlift' . $file;
    }

    // use standard WP methods in production mode.
    return plugins_url($file, __FILE__);
}

/**
 * Add buttons hook for the TinyMCE editor. This method is called by the WP init hook.
 */
function wordlift_buttonhooks()
{

    // Only add hooks when the current user has permissions AND is in Rich Text editor mode
    if ((current_user_can('edit_posts') || current_user_can('edit_pages')) && get_user_option('rich_editing')) {
        add_filter('mce_external_plugins', 'wordlift_register_tinymce_javascript');
        add_filter('mce_buttons',          'wordlift_register_buttons');
    }
}

/**
 * Register the TinyMCE buttons. This method is called by the WP mce_buttons hook.
 * @param array $buttons The existing buttons array.
 * @return array The modified buttons array.
 */
function wordlift_register_buttons($buttons)
{
    // push the wordlift button the array.
    array_push($buttons, 'wordlift');
    return $buttons;
}

/**
 * Load the TinyMCE plugin. This method is called by the WP mce_external_plugins hook.
 * @param array $plugin_array The existing plugins array.
 * @return array The modified plugins array.
 */
function wordlift_register_tinymce_javascript($plugin_array)
{
    // add the wordlift plugin.
    $plugin_array['wordlift'] = wordlift_get_url('/js/wordlift-tinymce-plugin.min.js');
    return $plugin_array;
}

/**
 * Enable microdata schema.org tagging.
 * see http://vip.wordpress.com/documentation/register-additional-html-attributes-for-tinymce-and-wp-kses/
 */
function wordlift_allowed_post_tags() {
    global $allowedposttags;

    $tags = array( 'span' );
    $new_attributes = array(
        'itemscope' => array(),
        'itemtype'  => array(),
        'itemprop'  => array(),
        'itemid'    => array()
    );

    foreach ( $tags as $tag ) {
        if ( isset( $allowedposttags[ $tag ] ) && is_array( $allowedposttags[ $tag ] ) )
            $allowedposttags[ $tag ] = array_merge( $allowedposttags[ $tag ], $new_attributes );
    }
}

// init process for button control
add_action('init', 'wordlift_buttonhooks');
// add allowed post tags.
add_action('init', 'wordlift_allowed_post_tags');


// Ajax Admin Section

add_action('wp_ajax_wordlift_analyze', 'wordlift_ajax_analyze_action');

// Analyze a text
function wordlift_ajax_analyze_action()
{
    if ((current_user_can('edit_posts') || current_user_can('edit_pages')) && get_user_option('rich_editing')) {

        // Get the Redlink enhance URL.
        $api_url  = wordlift_redlink_enhance_url();

        $response = wp_remote_post($api_url, array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(
                    'Accept' => 'application/json',
                    'Content-type' => 'text/plain',
                ),
                'body' => file_get_contents("php://input"),
                'cookies' => array()
            )
        );

        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            echo "Something went wrong: $error_message";
            die();
        } else {

            // Reprint the headers, mostly for debugging purposes.
            foreach ($response['headers'] as $header => $value) {
                if ( strpos( strtolower( $header ), 'x-redlink-') === 0 ) {
                    header( "$header: $value" );
                }
            }

            echo $response['body'];
            die();
        }
    }
}

/**
 * Register additional scripts for the admin UI.
 */
function wordlift_admin_enqueue_scripts() {
    global $post;

    wp_register_style('wordlift_wp_admin_css', wordlift_get_url('/css/wordlift-admin.min.css'), false, '1.0.0');
    wp_enqueue_style('wordlift_wp_admin_css');
    wp_enqueue_style('jquery-ui-autocomplete', '', array('jquery-ui-widget', 'jquery-ui-position'));


    wp_enqueue_script( 'jquery-ui-autocomplete', '', array('jquery-ui-widget', 'jquery-ui-position') );
    wp_enqueue_script( 'angularjs', wordlift_get_url('/bower_components/angular/angular.min.js') );
    wp_localize_script('angularjs', 'thePost', get_post($post->id, ARRAY_A));
}
add_action('admin_enqueue_scripts', 'wordlift_admin_enqueue_scripts');

/**
 * Hooked to *wp_kses_allowed_html* filter, adds microdata attributes.
 * @param array $allowedtags The array with the currently configured elements and attributes.
 * @param string $context    The context.
 * @return array An array which contains allowed microdata attributes.
 */
function wordlift_allowed_html( $allowedtags, $context ) {

    if ( 'post' !== $context ) {
        return $allowedtags;
    }

    return array_merge_recursive( $allowedtags, array(
        'span' => array(
            'itemscope' => true,
            'itemtype'  => true,
            'itemid'    => true,
            'itemprop'  => true
        )
    ) );
}
add_filter('wp_kses_allowed_html', 'wordlift_allowed_html', 10, 2 );

require_once('libs/php-json-ld/jsonld.php');

// add editor related methods.
require_once('wordlift_editor.php');
// add configuratiokn-related methods.
require_once('wordlift_configuration.php');
// add the WordLift admin bar.
require_once('wordlift_admin_bar.php');
// add the WordLift admin menu. - the entity admin menu is handled as a custom post type.
//require_once('wordlift_admin_menu.php');
// add the WordLift entity custom type.
require_once('wordlift_entity_custom_type.php');
// filters the post content when saving posts.
require_once('wordlift_content_filter.php');
// add the entities meta box.
require_once('wordlift_admin_meta_box_entities.php');
require_once('wordlift_admin_meta_box_related_posts.php');
// add callbacks on post save to notify data changes from wp to redlink triple store
require_once('wordlift_to_redlink_data_push_callbacks.php');

require_once('wordlift_shortcode_related_posts.php');

require_once('wordlift_indepth_articles.php');

require_once('wordlift_freebase_image_proxy.php');
require_once('wordlift_selected_entities_meta_box.php');

// add the search entity AJAX.
require_once('wordlift_ajax_search_entities.php');

// load languages.
// TODO: the following call gives for granted that the plugin is in the wordlift directory,
//       we're currently doing this because wordlift is symbolic linked.
load_plugin_textdomain('wordlift', false, '/wordlift/languages' );