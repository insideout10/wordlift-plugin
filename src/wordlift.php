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

// Include WordLift constants.
require_once('wordlift_constants.php');

// Create the *write_log* function to allow logging to the debug.log file.
if (!function_exists('write_log')) {
    function write_log($log)
    {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }
}

/**
 * Write the query to the buffer file.
 * @param string $query A SPARQL query.
 */
function wl_queue_sparql_update_query($query)
{

    $filename = WL_TEMP_DIR . WL_REQUEST_ID . '.sparql';
    file_put_contents($filename, $query . "\n", FILE_APPEND);

    write_log("wl_queue_sparql_update_query [ filename :: $filename ]");
}

/**
 * Execute the SPARQL query from the buffer saved for the specified request id.
 * @param int $request_id The request ID.
 */
function wl_execute_saved_sparql_update_query($request_id)
{

    $filename = WL_TEMP_DIR . $request_id . '.sparql';

    // If the file doesn't exist, exit.
    if (!file_exists($filename)) {
        write_log("wl_execute_saved_sparql_update_query : file doesn't exist [ filename :: $filename ]");
        return;
    }

    write_log("wl_execute_saved_sparql_update_query [ filename :: $filename ]");

    // Get the query saved in the file.
    $query = file_get_contents($filename);

    // Execute the SPARQL query.
    rl_execute_sparql_update_query($query, false);

    // Reindex the triple store.
    wordlift_reindex_triple_store();

    // Delete the temporary file.
    unlink($filename);
}

add_action('wl_execute_saved_sparql_update_query', 'wl_execute_saved_sparql_update_query', 10, 1);

/**
 * Add buttons hook for the TinyMCE editor. This method is called by the WP init hook.
 */
function wordlift_buttonhooks()
{

    // Only add hooks when the current user has permissions AND is in Rich Text editor mode
    if ((current_user_can('edit_posts') || current_user_can('edit_pages')) && get_user_option('rich_editing')) {
        add_filter('mce_external_plugins', 'wordlift_register_tinymce_javascript');
        add_filter('mce_buttons', 'wordlift_register_buttons');
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
    $plugin_array['wordlift'] = plugins_url('js/wordlift.js', __FILE__);
    return $plugin_array;
}

/**
 * Enable microdata schema.org tagging.
 * see http://vip.wordpress.com/documentation/register-additional-html-attributes-for-tinymce-and-wp-kses/
 */
function wordlift_allowed_post_tags()
{
    global $allowedposttags;

    $tags = array('span');
    $new_attributes = array(
        'itemscope' => array(),
        'itemtype' => array(),
        'itemprop' => array(),
        'itemid' => array()
    );

    foreach ($tags as $tag) {
        if (isset($allowedposttags[$tag]) && is_array($allowedposttags[$tag]))
            $allowedposttags[$tag] = array_merge($allowedposttags[$tag], $new_attributes);
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

    // Get the Redlink enhance URL.
    $url = wordlift_redlink_enhance_url();

    // Prepare the request.
    $args = array_merge_recursive(unserialize(WL_REDLINK_API_HTTP_OPTIONS), array(
        'method' => 'POST',
        'headers' => array(
            'Accept' => 'application/json',
            'Content-type' => 'text/plain'
        ),
        'body' => file_get_contents("php://input"),
    ));

    $response = wp_remote_post($url, $args);

    // Remove the key from the query.
    $scrambled_url = preg_replace('/key=.*$/i', 'key=<hidden>', $url);

    // If an error has been raised, return the error.
    if (is_wp_error($response) || 200 !== (int)$response['response']['code']) {

        $body = (is_wp_error($response) ? $response->get_error_message() : $response['body']);

        write_log("wordlift_ajax_analyze_action : error [ response :: ");
        write_log("\n" . var_export($response, true));
        write_log("][ body :: ");
        write_log("\n" . $body);
        write_log("]");

        echo 'An error occurred while request an analysis to the remote service. Please try again later.';

        die();
    }

    write_log("wordlift_ajax_analyze_action [ url :: $scrambled_url ][ response code :: " . $response['response']['code'] . " ]");

    // Reprint the headers, mostly for debugging purposes.
    foreach ($response['headers'] as $header => $value) {
        if (strpos(strtolower($header), 'x-redlink-') === 0) {
            header("$header: $value");
        }
    }

    echo $response['body'];
    die();
}

/**
 * Register additional scripts for the admin UI.
 */
function wordlift_admin_enqueue_scripts()
{

    // Added for compatibility with WordPress 3.9 (see http://make.wordpress.org/core/2014/04/16/jquery-ui-and-wpdialogs-in-wordpress-3-9/)
    wp_enqueue_script('wpdialogs');
    wp_enqueue_style('wp-jquery-ui-dialog');

    wp_register_style('wordlift_css', plugins_url('css/wordlift.css', __FILE__));
    wp_enqueue_style('wordlift_css');

    wp_enqueue_script('jquery-ui-autocomplete');
    wp_enqueue_script('angularjs', plugins_url('bower_components/angular/angular.min.js', __FILE__));

}

add_action('admin_enqueue_scripts', 'wordlift_admin_enqueue_scripts');

/**
 * Hooked to *wp_kses_allowed_html* filter, adds microdata attributes.
 * @param array $allowedtags The array with the currently configured elements and attributes.
 * @param string $context The context.
 * @return array An array which contains allowed microdata attributes.
 */
function wordlift_allowed_html($allowedtags, $context)
{

    if ('post' !== $context) {
        return $allowedtags;
    }

    return array_merge_recursive($allowedtags, array(
        'span' => array(
            'itemscope' => true,
            'itemtype' => true,
            'itemid' => true,
            'itemprop' => true
        )
    ));
}

add_filter('wp_kses_allowed_html', 'wordlift_allowed_html', 10, 2);

///**
// * Save the coordinates for the specified post ID.
// * @param int $post_id The post ID.
// * @param double $latitude The latitude.
// * @param double $longitude The longitude.
// */
//function wl_set_coordinates($post_id, $latitude = null, $longitude = null)
//{
//
//    write_log("wl_set_coordinates [ post id :: $post_id ][ latitude :: $latitude ][ longitude :: $longitude ]");
//
//    delete_post_meta($post_id, 'wl_latitude');
//    delete_post_meta($post_id, 'wl_longitude');
//
//    // If the coordinates are not empty, add them.
//    if (!(empty($latitude) || empty($longitude))) {
//        add_post_meta($post_id, 'wl_latitude', $latitude);
//        add_post_meta($post_id, 'wl_longitude', $longitude);
//    }
//}

/**
 * Get the coordinates for the specified post ID.
 * @param int $post_id The post ID.
 * @return array|null An array of coordinates or null.
 */
function wl_get_coordinates($post_id)
{

    $latitude = get_post_meta($post_id, WL_CUSTOM_FIELD_GEO_LATITUDE, true);
    $longitude = get_post_meta($post_id, WL_CUSTOM_FIELD_GEO_LONGITUDE, true);

    if (empty($latitude) || empty($longitude)) {
        return null;
    }

    return array(
        'latitude' => $latitude,
        'longitude' => $longitude
    );
}

/**
 * Save the image with the specified URL locally.
 * @param string $url The image remote URL.
 * @return array An array with information about the saved image (*path*: the local path to the image, *url*: the local
 * url, *content_type*: the image content type)
 */
function wl_save_image($url)
{

    $parts = parse_url($url);
    $path = $parts['path'];

    // Get the bare filename (filename w/o the extension).
    $basename = pathinfo($path, PATHINFO_FILENAME);

    // Chunk the bare name to get a subpath.
    $chunks = chunk_split(strtolower($basename), 3, DIRECTORY_SEPARATOR);

    // Get the base dir.
    $wp_upload_dir = wp_upload_dir();
    $base_dir = $wp_upload_dir['basedir'];
    $base_url = $wp_upload_dir['baseurl'];

    // Get the full path to the local filename.
    $image_path = '/' . $chunks;
    $image_full_path = $base_dir . $image_path;
    $image_full_url = $base_url . $image_path;

    // Create the folders.
    if (!(file_exists($image_full_path) && is_dir($image_full_path))) {
        if (false === mkdir($image_full_path, 0777, true)) {
            write_log("wl_save_image : failed creating dir [ image full path :: $image_full_path ]\n");
        }
    };

    // Request the remote file.
    $response = wp_remote_get($url);
    $content_type = wp_remote_retrieve_header($response, 'content-type');

    switch ($content_type) {
        case 'image/jpeg':
        case 'image/jpg':
            $extension = ".jpg";
            break;
        case 'image/svg+xml':
            $extension = ".svg";
            break;
        case 'image/gif':
            $extension = ".gif";
            break;
        case 'image/png':
            $extension = ".png";
            break;
        default:
            $extension = '';
    }

    // Complete the local filename.
    $image_full_path .= $basename . $extension;
    $image_full_url .= $basename . $extension;

    // Store the data locally.
    file_put_contents($image_full_path, wp_remote_retrieve_body($response));

    write_log("wl_save_image [ url :: $url ][ content type :: $content_type ][ image full path :: $image_full_path ][ image full url :: $image_full_url ]\n");

    // Return the path.
    return array(
        'path' => $image_full_path,
        'url' => $image_full_url,
        'content_type' => $content_type
    );
}


/**
 * Set the related posts IDs for the specified post ID.
 *
 * @param int $post_id A post ID.
 * @param array $related_posts An array of related post IDs.
 */
function wl_set_related_posts($post_id, $related_posts)
{

    write_log("wl_set_related_posts [ post id :: $post_id ][ related posts :: " . join(',', $related_posts) . " ]");

    delete_post_meta($post_id, 'wordlift_related_posts');
    add_post_meta($post_id, 'wordlift_related_posts', $related_posts, true);
}

/**
 * Set the related posts IDs for the specified post ID.
 *
 * @param int $post_id A post ID.
 * @param int|array $new_post_ids An array of related post IDs.
 */
function wl_add_related_posts($post_id, $new_post_ids)
{

    // Convert the parameter to an array.
    $new_post_ids = (is_array($new_post_ids) ? $new_post_ids : array($new_post_ids));

    write_log("wl_add_related_posts [ post id :: $post_id ][ new post ids :: " . join(',', $new_post_ids) . " ]");

    // Get the existing post IDs and merge them together.
    $related = wl_get_related_post_ids($post_id);
    $related = array_unique(array_merge($related, $new_post_ids));

    wl_set_related_posts($post_id, $related);
}


/**
 * Set the related entity posts IDs for the specified post ID.
 *
 * @param int $post_id A post ID.
 * @param array $related_entities An array of related entity post IDs.
 */
function wl_set_related_entities($post_id, $related_entities)
{

    write_log("wl_set_related_entities [ post id :: $post_id ][ related entities :: " . join(',', $related_entities) . " ]");

    delete_post_meta($post_id, WL_CUSTOM_FIELD_REFERENCED_ENTITY);

    foreach ( $related_entities as $entity_post_id ) {
        add_post_meta( $post_id, WL_CUSTOM_FIELD_REFERENCED_ENTITY, $entity_post_id );
    }
}

/**
 * Get the posts that reference the specified entity.
 *
 * @uses wl_get_referenced_entities to get entities related to posts.
 * @used-by wl_ajax_related_entities
 *
 * @param int $entity_id The post ID of the entity.
 * @return array An array of posts.
 */
function wl_get_referencing_posts($entity_id) {

    $args = array(
        'posts_per_page' => -1,
        'post_type'    => 'any',
        'post_status'  => 'any',
        'meta_key'     => WL_CUSTOM_FIELD_REFERENCED_ENTITY,
        'meta_value'   => $entity_id
    );

    $posts = get_posts( $args );
    write_log("wl_get_referencing_posts [ entity id :: $entity_id ][ posts count :: " . count($posts) . " ]");

    return $posts;
}

/**
 * Set the sameAs URIs for the specified post ID.
 * @param int $post_id A post ID.
 * @param array|string $same_as An array of same as URIs or a single URI string.
 */
function wl_set_same_as($post_id, $same_as)
{

    // Prepare the same as array.
    $same_as_array = array_unique(is_array($same_as) ? $same_as : array($same_as));

    write_log("wl_set_same_as [ post id :: $post_id ][ same as :: " . join(',', $same_as_array) . " ]");

    // Replace the existing same as with the new one.
    delete_post_meta($post_id, 'entity_same_as');

    foreach ($same_as_array as $item) {
        if (!empty($item)) {
            add_post_meta($post_id, 'entity_same_as', $item, false);
        }
    }
}

/**
 * Get the sameAs URIs for the specified post ID.
 * @param int $post_id A post ID.
 * @return array An array of sameAs URIs.
 */
function wl_get_same_as($post_id)
{

    // Get the related array (single _must_ be true, refer to http://codex.wordpress.org/Function_Reference/get_post_meta)
    $same_as = get_post_meta($post_id, 'entity_same_as', false);

    if (empty($same_as)) {
        return array();
    }

    // Ensure an array is returned.
    return (is_array($same_as) ? $same_as : array($same_as));
}


/**
 * Set the related entity posts IDs for the specified post ID.
 *
 * @param int $post_id A post ID.
 * @param int|array $new_entity_post_ids An array of related entity post IDs.
 */
function wl_add_referenced_entities($post_id, $new_entity_post_ids)
{

    // Convert the parameter to an array.
    $new_entity_post_ids = (is_array($new_entity_post_ids) ? $new_entity_post_ids : array($new_entity_post_ids));

    write_log("wl_add_referenced_entities [ post id :: $post_id ][ related entities :: " . join(',', $new_entity_post_ids) . " ]");

    // Get the existing post IDs and merge them together.
    $related = wl_get_referenced_entities($post_id);
    $related = array_unique(array_merge($related, $new_entity_post_ids));

    wl_set_related_entities($post_id, $related);
}

/**
 * Get the IDs of posts related to the specified post.
 * @param int $post_id The post ID.
 * @return array An array of posts related to the one specified.
 */
function wl_get_related_post_ids($post_id)
{

    // Get the related array (single _must_ be true, refer to http://codex.wordpress.org/Function_Reference/get_post_meta)
    $related = get_post_meta($post_id, 'wordlift_related_posts', true);

    write_log("wl_get_related_post_ids [ post id :: $post_id ][ empty related :: " . (empty($related) ? 'true' : 'false') . "  ]");

    if (empty($related)) {
        return array();
    }

    // Ensure an array is returned.
    return (is_array($related)
        ? $related
        : array($related));
}

/**
 * Get the IDs of entities related to the specified post.
 * @param int $post_id The post ID.
 * @return array An array of posts related to the one specified.
 */
function wl_get_referenced_entities($post_id)
{

    // Get the related array (single _must_ be true, refer to http://codex.wordpress.org/Function_Reference/get_post_meta)
    return get_post_meta( $post_id, WL_CUSTOM_FIELD_REFERENCED_ENTITY );
}

/**
 * Get the modified time of the provided post. If the time is negative, return the published date.
 * @param object $post A post instance.
 * @return string A datetime.
 */
function wl_get_post_modified_time($post)
{

    $date_modified = get_post_modified_time('c', true, $post);

    if ('-' === substr($date_modified, 0, 1)) {
        return get_the_time('c', $post);
    }

    return $date_modified;
}

/**
 * Unbind post and entities.
 * @param int $post_id The post ID.
 */
function wl_unbind_post_from_entities($post_id)
{

    write_log("wl_unbind_post_from_entities [ post id :: $post_id ]");

    $entities = wl_get_referenced_entities($post_id);
    foreach ($entities as $entity_post_id) {

        // Remove the specified post id from the list of related posts.
        $related_posts = wl_get_related_post_ids($entity_post_id);
        if (false !== ($key = array_search($post_id, $related_posts))) {
            unset($related_posts[$key]);
        }

        wl_set_related_posts($entity_post_id, $related_posts);
    }

    // Reset the related entities for the post.
    wl_set_related_entities($post_id, array());
}

/**
 * Get all the images bound to a post.
 * @param int $post_id The post ID.
 * @return array An array of image URLs.
 */
function wl_get_image_urls($post_id)
{

    write_log("wl_get_image_urls [ post id :: $post_id ]");

    $images = get_children(array(
        'post_parent' => $post_id,
        'post_type' => 'attachment',
        'post_mime_type' => 'image'
    ));

    // Return an empty array if no image is found.
    if (empty($images)) {
        return array();
    }

    // Prepare the return array.
    $image_urls = array();

    // Collect the URLs.
    foreach ($images as $attachment_id => $attachment) {
        $image_url = wp_get_attachment_url($attachment_id);
        // Ensure the URL isn't collected already.
        if (!in_array($image_url, $image_urls)) {
            array_push($image_urls, $image_url);
        }
    }

    write_log("wl_get_image_urls [ post id :: $post_id ][ image urls count :: " . count($image_urls) . " ]");

    return $image_urls;
}

/**
 * Get a SPARQL fragment with schema:image predicates.
 * @param string $uri The URI subject of the statements.
 * @param int $post_id The post ID.
 * @return string The SPARQL fragment.
 */
function wl_get_sparql_images($uri, $post_id)
{

    $sparql = '';

    // Add SPARQL stmts to write the schema:image.
    $image_urls = wl_get_image_urls($post_id);
    foreach ($image_urls as $image_url) {
        $image_url_esc = wordlift_esc_sparql($image_url);
        $sparql .= " <$uri> schema:image <$image_url_esc> . \n";
    }

    return $sparql;
}

/**
 * Get an attachment with the specified parent post ID and source URL.
 * @param int $parent_post_id The parent post ID.
 * @param string $source_url The source URL.
 * @return WP_Post|null A post instance or null if not found.
 */
function wl_get_attachment_for_source_url($parent_post_id, $source_url)
{

    write_log("wl_get_attachment_for_source_url [ parent post id :: $parent_post_id ][ source url :: $source_url ]");

    $posts = get_posts(array(
        'post_type' => 'attachment',
        'posts_per_page' => 1,
        'post_status' => 'any',
        'post_parent' => $parent_post_id,
        'meta_key' => 'wl_source_url',
        'meta_value' => $source_url
    ));

    // Return the found post.
    if (1 === count($posts)) {
        return $posts[0];
    }

    // Return null.
    return null;
}

/**
 * Add related post IDs to the specified post ID, automatically choosing whether to add the related to entities or to
 * posts.
 * @param int $post_id The post ID.
 * @param int|array $related_id A related post/entity ID or an array of posts/entities.
 */
function wl_add_related($post_id, $related_id)
{

    // Ensure we're dealing with an array.
    $related_id_array = (is_array($related_id) ? $related_id : array($related_id));

    // Prepare the related arrays.
    $related_entities = array();
    $related_posts = array();

    foreach ($related_id_array as $id) {

        // If it's an entity add the entity to the related entities.
        if ('entity' === get_post_type($id)) {
            array_push($related_entities, $id);
        } else {
            // Else add it to the related posts.
            array_push($related_posts, $id);
        }
    }

    if (0 < count($related_entities)) {
        wl_add_referenced_entities($post_id, $related_entities);
    }
    if (0 < count($related_posts)) {
        wl_add_related_posts($post_id, $related_posts);
    }
}

/**
 * Set the source URL.
 * @param int $post_id The post ID.
 * @param string $source_url The source URL.
 */
function wl_set_source_url($post_id, $source_url)
{

    delete_post_meta($post_id, 'wl_source_url');
    add_post_meta($post_id, 'wl_source_url', $source_url);
}


function wl_flush_rewrite_rules_hard($hard)
{

    write_log("wl_flush_rewrite_rules_hard [ hard :: $hard ]");

    // Get all published posts.
    $posts = get_posts(array(
        'posts_per_page' => -1,
        'post_type' => 'any',
        'post_status' => 'publish'
    ));

    // Holds the delete part of the query.
    $delete_query = wordlift_get_ns_prefixes();
    // Holds the insert part of the query.
    $insert_query = 'INSERT DATA { ';

    // Cycle in each post to build the query.
    foreach ($posts as $post) {

        // Ignore revisions.
        if (wp_is_post_revision($post->ID)) {
            continue;
        }

        $uri = wordlift_esc_sparql(wl_get_entity_uri($post->ID));
        $url = wordlift_esc_sparql(get_permalink($post->ID));

        $delete_query .= "DELETE { <$uri> schema:url ?u . } WHERE  { <$uri> schema:url ?u . };\n";
        $insert_query .= " <$uri> schema:url <$url> . \n";

        write_log("wl_flush_rewrite_rules_hard [ uri :: $uri ][ url :: $url ]");
    }

    $insert_query .= ' };';

    // Execute the query.
    rl_execute_sparql_update_query($delete_query . $insert_query);
}

add_filter('flush_rewrite_rules_hard', 'wl_flush_rewrite_rules_hard', 10, 1);

/**
 * Sanitizes an URI path by replacing the non allowed characters with an underscore.
 * @param string $path The path to sanitize.
 * @param string $char The replacement character (by default an underscore).
 * @return The sanitized path.
 */
function wl_sanitize_uri_path($path, $char = '_')
{

    write_log("wl_sanitize_uri_path [ path :: $path ][ char :: $char ]");

    // According to RFC2396 (http://www.ietf.org/rfc/rfc2396.txt) these characters are reserved:
    // ";" | "/" | "?" | ":" | "@" | "&" | "=" | "+" |
    // "$" | ","
    // Plus the ' ' (space).
    // TODO: We shall use the same regex used by MediaWiki (http://stackoverflow.com/questions/23114983/mediawiki-wikipedia-url-sanitization-regex)

    return preg_replace('/[;\/?:@&=+$,\s]/', $char, $path);
}

/**
 * Schedule the execution of SPARQL Update queries before the WordPress look ends.
 */
function wl_shutdown()
{

    // Get the filename to the temporary SPARQL file.
    $filename = WL_TEMP_DIR . WL_REQUEST_ID . '.sparql';

    // If WordLift is buffering SPARQL queries, we're admins and a buffer exists, then schedule it.
    if (WL_ENABLE_SPARQL_UPDATE_QUERIES_BUFFERING && is_admin() && file_exists($filename)) {

        // The request ID.
        $args = array(WL_REQUEST_ID);

        // Schedule the execution of the SPARQL query with the request ID.
        wp_schedule_single_event(time(), 'wl_execute_saved_sparql_update_query', $args);

        // Check that the request is scheduled.
        $timestamp = wp_next_scheduled('wl_execute_saved_sparql_update_query', $args);

        // Spawn the cron.
        spawn_cron();

        write_log("wl_shutdown [ request id :: " . WL_REQUEST_ID . " ][ timestamp :: $timestamp ]");
    }
}

add_action('shutdown', 'wl_shutdown');

/**
 * Replaces the *itemid* attributes URIs with the WordLift URIs.
 * @param string $content The post content.
 * @return string The updated post content.
 */
function wl_replace_item_id_with_uri($content)
{

    write_log("wl_replace_item_id_with_uri");

    // Strip slashes, see https://core.trac.wordpress.org/ticket/21767
    $content = stripslashes($content);

    // If any match are found.
    $matches = array();
    if (0 < preg_match_all('/ itemid="([^"]+)"/i', $content, $matches, PREG_SET_ORDER)) {

        foreach ($matches as $match) {

            // Get the item ID.
            $item_id = $match[1];

            // Get the post bound to that item ID (looking both in the 'official' URI and in the 'same-as' .
            $post = wl_get_entity_post_by_uri($item_id);

            // If no entity is found, continue to the next one.
            if (null === $post) {
                continue;
            }

            // Get the URI for that post.
            $uri = wl_get_entity_uri($post->ID);

            write_log("wl_replace_item_id_with_uri [ item id :: $item_id ][ uri :: $uri ]");

            // If the item ID and the URI differ, replace the item ID with the URI saved in WordPress.
            if ($item_id !== $uri) {
                $content = str_replace(" itemid=\"$item_id\"", " itemid=\"$uri\"", $content);
            }
        }
    }

    // Reapply slashes.
    $content = addslashes($content);

    return $content;
}

add_filter('content_save_pre', 'wl_replace_item_id_with_uri', 1, 1);


/**
 * Install known types in WordPress.
 */
function wl_install_entity_type_data()
{

    write_log('wl_install_entity_type_data');

    // Ensure the custom type and the taxonomy are registered.
    wl_entity_type_register();
    wl_entity_type_taxonomy_register();

    // Set the taxonomy data.
    $terms = array(
        'creative-work' => array(
            'label' => 'Creative Work',
            'description' => 'A creative work (or a Music Album).',
            'css' => 'wl-creative-work',
            'uri' => 'http://schema.org/CreativeWork',
            'same_as' => array(
                'http://schema.org/MusicAlbum',
                'http://schema.org/Product'
            ),
            'custom_fields' => array()
        ),
        'event' => array(
            'label' => 'Event',
            'description' => 'An event.',
            'css' => 'wl-event',
            'uri' => 'http://schema.org/Event',
            'same_as' => array('http://dbpedia.org/ontology/Event'),
            'custom_fields' => array(
                WL_CUSTOM_FIELD_CAL_DATE_START => 'startDate',
                WL_CUSTOM_FIELD_CAL_DATE_END   => 'endDate'
            )
        ),
        'organization' => array(
            'label' => 'Organization',
            'description' => 'An organization, including a government or a newspaper.',
            'css' => 'wl-organization',
            'uri' => 'http://schema.org/Organization',
            'same_as' => array(
                'http://rdf.freebase.com/ns/organization.organization',
                'http://rdf.freebase.com/ns/government.government',
                'http://schema.org/Newspaper'
            ),
            'custom_fields' => array()
        ),
        'person' => array(
            'label' => 'Person',
            'description' => 'A person (or a music artist).',
            'css' => 'wl-person',
            'uri' => 'http://schema.org/Person',
            'same_as' => array(
                'http://rdf.freebase.com/ns/people.person',
                'http://rdf.freebase.com/ns/music.artist'
            ),
            'custom_fields' => array()
        ),
        'place' => array(
            'label' => 'Place',
            'description' => 'A place.',
            'css' => 'wl-place',
            'uri' => 'http://schema.org/Place',
            'same_as' => array(
                'http://rdf.freebase.com/ns/location.location',
                'http://www.opengis.net/gml/_Feature'
            ),
            'custom_fields' => array()
        ),
        'thing' => array(
            'label' => 'Thing',
            'description' => 'A generic thing (something that doesn\'t fit in the previous definitions.',
            'css' => 'wl-thing',
            'uri' => 'http://schema.org/Thing',
            'same_as' => array('*'), // set as default.
            'custom_fields' => array()
        )
    );

    foreach ($terms as $slug => $term) {

        // Create the term.
        $result = wp_insert_term($term['label'], WL_ENTITY_TYPE_TAXONOMY_NAME, array(
            'description' => $term['description'],
            'slug' => $slug
        ));

        if (is_wp_error($result)) {
            write_log('wl_install_entity_type_data [ ' . $result->get_error_message() . ' ]');
            continue;
        }
        // Add custom metadata to the term.
        wl_entity_type_taxonomy_update_term($result['term_id'], $term['css'], $term['uri'], $term['same_as'], $term['custom_fields']);
    }
}

/**
 * Change *plugins_url* response to return the correct path of WordLift files when working in development mode.
 * @param $url The URL as set by the plugins_url method.
 * @param $path The request path.
 * @param $plugin The plugin folder.
 * @return string The URL.
 */
function wl_plugins_url($url, $path, $plugin)
{

    write_log("wl_plugins_url [ url :: $url ][ path :: $path ][ plugin :: $plugin ]");

    // Check if it's our pages calling the plugins_url.
    if (1 !== preg_match('/\/wordlift[^.]*.php$/i', $plugin)) {
        return $url;
    };

    // Set the URL to plugins URL + wordlift, in order to support the plugin being symbolic linked.
    $plugin_url = plugins_url() . '/wordlift/' . $path;

    write_log("wl_plugins_url [ match :: yes ][ plugin url :: $plugin_url ][ url :: $url ][ path :: $path ][ plugin :: $plugin ]");

    return $plugin_url;
}

add_filter('plugins_url', 'wl_plugins_url', 10, 3);

add_action('activate_wordlift/wordlift.php', 'wl_install_entity_type_data');

require_once('libs/php-json-ld/jsonld.php');

require_once('wordlift_entity_functions.php');
// add editor related methods.
require_once('wordlift_editor.php');
// add configuration-related methods.
require_once('wordlift_configuration.php');
// add the WordLift entity custom type.
require_once('wordlift_entity_type.php');
require_once('wordlift_entity_type_taxonomy.php');
// filters the post content when saving posts.
require_once('wordlift_content_filter.php');
// add callbacks on post save to notify data changes from wp to redlink triple store
require_once('wordlift_to_redlink_data_push_callbacks.php');


// Shortcodes
require_once('shortcodes/wordlift_shortcode_related_posts.php');
require_once('shortcodes/wordlift_shortcode_chord.php');

require_once('wordlift_indepth_articles.php');

require_once('wordlift_freebase_image_proxy.php');

require_once('wordlift_user.php');

require_once('wordlift_geo_widget.php');
require_once('wordlift_chord_widget.php');

require_once('wordlift_sparql.php');
require_once('wordlift_redlink.php');

// Add admin functions.
// TODO: find a way to make 'admin' UI tests work.
//if ( is_admin() ) {

    require_once('admin/wordlift_admin.php');
    // add the WordLift admin bar.
    require_once('admin/wordlift_admin_bar.php');
    require_once('admin/wordlift_settings_page.php');
    // add the entities meta box.
    require_once('admin/wordlift_admin_meta_box_entities.php');
    require_once('admin/wordlift_admin_meta_box_related_posts.php');
    require_once('admin/wordlift_admin_entity_type_taxonomy.php');
    require_once('admin/wordlift_admin_entity_props.php');
    // add the search entity AJAX.
    require_once('admin/wordlift_admin_ajax_search.php');
    // add the entity creation AJAX.
    require_once('admin/wordlift_admin_ajax_add_entity.php');

    // Load the wl-chord TinyMCE button and configuration dialog.
    require_once('admin/wordlift_admin_shortcode_chord.php');

//}

// load languages.
// TODO: the following call gives for granted that the plugin is in the wordlift directory,
//       we're currently doing this because wordlift is symbolic linked.
load_plugin_textdomain('wordlift', false, '/wordlift/languages');

