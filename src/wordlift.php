<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://wordlift.it
 * @since             1.0.0
 * @package           Wordlift
 *
 * @wordpress-plugin
 * Plugin Name:       WordLift
 * Plugin URI:        http://wordlift.it
 * Description:       Supercharge your WordPress Site with Smart Tagging and #Schemaorg support - a brand new way to write, organise and publish your contents to the Linked Data Cloud. <strong>We are currently in private beta</strong> and we're allowing a closed number of users testing the plugin. <strong>Please signup to <a href="http://join.wordlift.it">join.wordlift.it</a></strong> and we will get in contact with you anytime soon.
 * Version:           3.4.0-dev
 * Author:            WordLift, Insideout10
 * Author URI:        http://wordlift.it
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wordlift
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Include WordLift constants.
require_once( 'wordlift_constants.php' );

// Load modules
require_once( 'modules/core/wordlift_core.php' );
require_once( 'modules/configuration/wordlift_configuration.php' );

/**
 * Log to the debug.log file.
 *
 * @since 3.0.0
 *
 * @uses wl_write_log_handler() to write the log output.
 *
 * @param string|mixed $log The log data.
 */
function wl_write_log( $log ) {

	$handler = apply_filters( 'wl_write_log_handler', null );

	$callers         = debug_backtrace();
	$caller_function = $callers[1]['function'];

	if ( is_null( $handler ) ) {
		wl_write_log_handler( $log, $caller_function );

		return;
	}

	call_user_func( $handler, $log, $caller_function );
}

/**
 * The default log handler prints out the log.
 *
 * @since 3.0.0
 *
 * @param string|array $log The log data.
 * @param string $caller The calling function.
 */
function wl_write_log_handler( $log, $caller = null ) {

	global $wl_logger;

	if ( true === WP_DEBUG ) {

		$message = ( isset( $caller ) ? sprintf( '[%-40.40s] ', $caller ) : '' ) .
		           ( is_array( $log ) || is_object( $log ) ? print_r( $log, true ) : wl_write_log_hide_key( $log ) );

		if ( isset( $wl_logger ) ) {
			$wl_logger->info( $message );
		} else {
			error_log( $message );
		}

	}

}

/**
 * Hide the WordLift Key from the provided text.
 *
 * @since 3.0.0
 *
 * @param $text string A text that may potentially contain a WL key.
 *
 * @return string A text with the key hidden.
 */
function wl_write_log_hide_key( $text ) {

	return str_ireplace( wl_configuration_get_key(), '<hidden>', $text );
}

/**
 * Write the query to the buffer file.
 *
 * @since 3.0.0
 *
 * @param string $query A SPARQL query.
 */
function wl_queue_sparql_update_query( $query ) {

	$filename = WL_TEMP_DIR . WL_REQUEST_ID . '.sparql';
	file_put_contents( $filename, $query . "\n", FILE_APPEND );

	wl_write_log( "wl_queue_sparql_update_query [ filename :: $filename ]" );
}

/**
 * Execute the SPARQL query from the buffer saved for the specified request id.
 *
 * @param int $request_id The request ID.
 */
function wl_execute_saved_sparql_update_query( $request_id ) {

	$filename = WL_TEMP_DIR . $request_id . '.sparql';

	// If the file doesn't exist, exit.
	if ( ! file_exists( $filename ) ) {
		wl_write_log( "wl_execute_saved_sparql_update_query : file doesn't exist [ filename :: $filename ]" );

		return;
	}

	wl_write_log( "wl_execute_saved_sparql_update_query [ filename :: $filename ]" );

	// Get the query saved in the file.
	$query = file_get_contents( $filename );

	// Execute the SPARQL query.
	rl_execute_sparql_update_query( $query, false );

	// Reindex the triple store.
	wordlift_reindex_triple_store();

	// Delete the temporary file.
	unlink( $filename );
}

add_action( 'wl_execute_saved_sparql_update_query', 'wl_execute_saved_sparql_update_query', 10, 1 );

/**
 * Add buttons hook for the TinyMCE editor. This method is called by the WP init hook.
 */
function wordlift_buttonhooks() {

	// Only add hooks when the current user has permissions AND is in Rich Text editor mode
	if ( ( current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' ) ) && get_user_option( 'rich_editing' ) ) {
		add_filter( 'mce_external_plugins', 'wordlift_register_tinymce_javascript' );
	}
}

/**
 * Load the TinyMCE plugin. This method is called by the WP mce_external_plugins hook.
 *
 * @param array $plugin_array The existing plugins array.
 *
 * @return array The modified plugins array.
 */
function wordlift_register_tinymce_javascript( $plugin_array ) {

	// add the wordlift plugin.
	$plugin_array['wordlift'] = plugin_dir_url( __FILE__ ) . 'js/wordlift-reloaded.js';

	return $plugin_array;
}

/**
 * Enable microdata schema.org tagging.
 * see http://vip.wordpress.com/documentation/register-additional-html-attributes-for-tinymce-and-wp-kses/
 */
function wordlift_allowed_post_tags() {
	global $allowedposttags;

	$tags           = array( 'span' );
	$new_attributes = array(
		'itemscope' => array(),
		'itemtype'  => array(),
		'itemprop'  => array(),
		'itemid'    => array()
	);

	foreach ( $tags as $tag ) {
		if ( isset( $allowedposttags[ $tag ] ) && is_array( $allowedposttags[ $tag ] ) ) {
			$allowedposttags[ $tag ] = array_merge( $allowedposttags[ $tag ], $new_attributes );
		}
	}
}

// init process for button control
add_action( 'init', 'wordlift_buttonhooks' );
// add allowed post tags.
add_action( 'init', 'wordlift_allowed_post_tags' );


/**
 * Register additional scripts for the admin UI.
 */
function wordlift_admin_enqueue_scripts() {

	// Added for compatibility with WordPress 3.9 (see http://make.wordpress.org/core/2014/04/16/jquery-ui-and-wpdialogs-in-wordpress-3-9/)
	wp_enqueue_script( 'wpdialogs' );
	wp_enqueue_style( 'wp-jquery-ui-dialog' );

	wp_enqueue_style( 'wordlift-reloaded', plugin_dir_url( __FILE__ ) . 'css/wordlift-reloaded.min.css' );

	wp_enqueue_script( 'jquery-ui-autocomplete' );
	wp_enqueue_script( 'angularjs', plugin_dir_url( __FILE__ ) . 'bower_components/angular/angular.min.js' );

}

add_action( 'admin_enqueue_scripts', 'wordlift_admin_enqueue_scripts' );

function wl_enqueue_scripts() {
	wp_enqueue_style( 'wordlift-ui', plugin_dir_url( __FILE__ ) . 'css/wordlift-ui.min.css' );
}

add_action( 'wp_enqueue_scripts', 'wl_enqueue_scripts' );

/**
 * Hooked to *wp_kses_allowed_html* filter, adds microdata attributes.
 *
 * @param array $allowedtags The array with the currently configured elements and attributes.
 * @param string $context The context.
 *
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

add_filter( 'wp_kses_allowed_html', 'wordlift_allowed_html', 10, 2 );

/**
 * Get the coordinates for the specified post ID.
 *
 * @param int $post_id The post ID.
 *
 * @return array|null An array of coordinates or null.
 */
function wl_get_coordinates( $post_id ) {

	$latitude  = wl_schema_get_value( $post_id, 'latitude' );
	$longitude = wl_schema_get_value( $post_id, 'longitude' );

	// DO NOT set latitude/longitude to 0/0 as default values. It's a specific place on the globe:
	// "The zero/zero point of this system is located in the Gulf of Guinea about 625 km (390 mi) south of Tema, Ghana."
	return array(
		'latitude'  => isset( $latitude[0] ) && is_numeric( $latitude[0] ) ? $latitude[0] : '',
		'longitude' => isset( $longitude[0] ) && is_numeric( $longitude[0] ) ? $longitude[0] : ''
	);
}

/**
 * Get the modified time of the provided post. If the time is negative, return the published date.
 *
 * @param object $post A post instance.
 *
 * @return string A datetime.
 */
function wl_get_post_modified_time( $post ) {

	$date_modified = get_post_modified_time( 'c', true, $post );

	if ( '-' === substr( $date_modified, 0, 1 ) ) {
		return get_the_time( 'c', $post );
	}

	return $date_modified;
}

/**
 * Get all the images bound to a post.
 *
 * @param int $post_id The post ID.
 *
 * @return array An array of image URLs.
 */
function wl_get_image_urls( $post_id ) {

	// wl_write_log( "wl_get_image_urls [ post id :: $post_id ]" );

	$images = get_children( array(
		'post_parent'    => $post_id,
		'post_type'      => 'attachment',
		'post_mime_type' => 'image'
	) );

	// Return an empty array if no image is found.
	if ( empty( $images ) ) {
		return array();
	}

	// Prepare the return array.
	$image_urls = array();

	// Collect the URLs.
	foreach ( $images as $attachment_id => $attachment ) {
		$image_url = wp_get_attachment_url( $attachment_id );
		// Ensure the URL isn't collected already.
		if ( ! in_array( $image_url, $image_urls ) ) {
			array_push( $image_urls, $image_url );
		}
	}

	// wl_write_log( "wl_get_image_urls [ post id :: $post_id ][ image urls count :: " . count( $image_urls ) . " ]" );

	return $image_urls;
}

/**
 * Get a SPARQL fragment with schema:image predicates.
 *
 * @param string $uri The URI subject of the statements.
 * @param int $post_id The post ID.
 *
 * @return string The SPARQL fragment.
 */
function wl_get_sparql_images( $uri, $post_id ) {

	$sparql = '';

	// Get the escaped URI.
	$uri_e = esc_html( $uri );

	// Add SPARQL stmts to write the schema:image.
	$image_urls = wl_get_image_urls( $post_id );
	foreach ( $image_urls as $image_url ) {
		$image_url_esc = wl_sparql_escape_uri( $image_url );
		$sparql .= " <$uri_e> schema:image <$image_url_esc> . \n";
	}

	return $sparql;
}

/**
 * Get an attachment with the specified parent post ID and source URL.
 *
 * @param int $parent_post_id The parent post ID.
 * @param string $source_url The source URL.
 *
 * @return WP_Post|null A post instance or null if not found.
 */
function wl_get_attachment_for_source_url( $parent_post_id, $source_url ) {

	// wl_write_log( "wl_get_attachment_for_source_url [ parent post id :: $parent_post_id ][ source url :: $source_url ]" );

	$posts = get_posts( array(
		'post_type'      => 'attachment',
		'posts_per_page' => 1,
		'post_status'    => 'any',
		'post_parent'    => $parent_post_id,
		'meta_key'       => 'wl_source_url',
		'meta_value'     => $source_url
	) );

	// Return the found post.
	if ( 1 === count( $posts ) ) {
		return $posts[0];
	}

	// Return null.
	return null;
}

/**
 * Set the source URL.
 *
 * @param int $post_id The post ID.
 * @param string $source_url The source URL.
 */
function wl_set_source_url( $post_id, $source_url ) {

	delete_post_meta( $post_id, 'wl_source_url' );
	add_post_meta( $post_id, 'wl_source_url', $source_url );
}


/**
 * This function is called by the *flush_rewrite_rules_hard* hook. It recalculates the URI for all the posts.
 *
 * @since 3.0.0
 *
 * @uses rl_sparql_prefixes() to get the SPARQL prefixes.
 * @uses wordlift_esc_sparql() to escape the SPARQL query.
 * @uses wl_get_entity_uri() to get an entity URI.
 * @uses rl_execute_sparql_update_query() to post the DELETE and INSERT queries.
 *
 * @param bool $hard True if the rewrite involves configuration updates in Apache/IIS.
 */
function wl_flush_rewrite_rules_hard( $hard ) {

	// Get all published posts.
	$posts = get_posts( array(
		'posts_per_page' => - 1,
		'post_type'      => 'any',
		'post_status'    => 'publish'
	) );

	// Holds the delete part of the query.
	$delete_query = rl_sparql_prefixes();
	// Holds the insert part of the query.
	$insert_query = 'INSERT DATA { ';

	// Cycle in each post to build the query.
	foreach ( $posts as $post ) {

		// Ignore revisions.
		if ( wp_is_post_revision( $post->ID ) ) {
			continue;
		}

		// Get the entity URI.
		$uri = wl_sparql_escape_uri( wl_get_entity_uri( $post->ID ) );

		// Get the post URL.
		$url = wl_sparql_escape_uri( get_permalink( $post->ID ) );

		// Prepare the DELETE and INSERT commands.
		$delete_query .= "DELETE { <$uri> schema:url ?u . } WHERE  { <$uri> schema:url ?u . };\n";
		$insert_query .= " <$uri> schema:url <$url> . \n";

		// wl_write_log( "[ uri :: $uri ][ url :: $url ]" );
	}

	$insert_query .= ' };';

	// Execute the query.
	rl_execute_sparql_update_query( $delete_query . $insert_query );
}

add_filter( 'flush_rewrite_rules_hard', 'wl_flush_rewrite_rules_hard', 10, 1 );

/**
 * Sanitizes an URI path by replacing the non allowed characters with an underscore.
 * @uses sanitize_title() to manage not ASCII chars
 * @see https://codex.wordpress.org/Function_Reference/sanitize_title
 *
 * @param string $path The path to sanitize.
 * @param string $char The replacement character (by default an underscore).
 *
 * @return The sanitized path.
 */
function wl_sanitize_uri_path( $path, $char = '_' ) {

	// wl_write_log( "wl_sanitize_uri_path [ path :: $path ][ char :: $char ]" );

	// According to RFC2396 (http://www.ietf.org/rfc/rfc2396.txt) these characters are reserved:
	// ";" | "/" | "?" | ":" | "@" | "&" | "=" | "+" |
	// "$" | ","
	// Plus the ' ' (space).
	// TODO: We shall use the same regex used by MediaWiki (http://stackoverflow.com/questions/23114983/mediawiki-wikipedia-url-sanitization-regex)

	return sanitize_title( preg_replace( '/[;\/?:@&=+$,\s]/', $char, stripslashes( $path ) ) );
}

/**
 * Utility function to check if a variable is set and force it to be an array
 * 
 * @package mixed $value Any value
 * 
 * @return array Array containing $value (if $value was not an array) 
 */
function wl_force_to_array( $value ) {
	
	if ( ! is_array( $value ) ) {
		return array( $value );
	}
	
	return $value;
}

/**
 * Schedule the execution of SPARQL Update queries before the WordPress look ends.
 */
function wl_shutdown() {

	// Get the filename to the temporary SPARQL file.
	$filename = WL_TEMP_DIR . WL_REQUEST_ID . '.sparql';

	// If WordLift is buffering SPARQL queries, we're admins and a buffer exists, then schedule it.
	if ( WL_ENABLE_SPARQL_UPDATE_QUERIES_BUFFERING && is_admin() && file_exists( $filename ) ) {

		// The request ID.
		$args = array( WL_REQUEST_ID );

		// Schedule the execution of the SPARQL query with the request ID.
		wp_schedule_single_event( time(), 'wl_execute_saved_sparql_update_query', $args );

		// Check that the request is scheduled.
		$timestamp = wp_next_scheduled( 'wl_execute_saved_sparql_update_query', $args );

		// Spawn the cron.
		spawn_cron();

		wl_write_log( "wl_shutdown [ request id :: " . WL_REQUEST_ID . " ][ timestamp :: $timestamp ]" );
	}
}

add_action( 'shutdown', 'wl_shutdown' );

/**
 * Replaces the *itemid* attributes URIs with the WordLift URIs.
 *
 * @param string $content The post content.
 *
 * @return string The updated post content.
 */
function wl_replace_item_id_with_uri( $content ) {

	// wl_write_log( "wl_replace_item_id_with_uri" );

	// Strip slashes, see https://core.trac.wordpress.org/ticket/21767
	$content = stripslashes( $content );

	// If any match are found.
	$matches = array();
	if ( 0 < preg_match_all( '/ itemid="([^"]+)"/i', $content, $matches, PREG_SET_ORDER ) ) {

		foreach ( $matches as $match ) {

			// Get the item ID.
			$item_id = $match[1];

			// Get the post bound to that item ID (looking both in the 'official' URI and in the 'same-as' .
			$post = Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( $item_id );

			// If no entity is found, continue to the next one.
			if ( null === $post ) {
				continue;
			}

			// Get the URI for that post.
			$uri = wl_get_entity_uri( $post->ID );

			// wl_write_log( "wl_replace_item_id_with_uri [ item id :: $item_id ][ uri :: $uri ]" );

			// If the item ID and the URI differ, replace the item ID with the URI saved in WordPress.
			if ( $item_id !== $uri ) {
				$uri_e   = esc_html( $uri );
				$content = str_replace( " itemid=\"$item_id\"", " itemid=\"$uri_e\"", $content );
			}
		}
	}

	// Reapply slashes.
	$content = addslashes( $content );

	return $content;
}

add_filter( 'content_save_pre', 'wl_replace_item_id_with_uri', 1, 1 );

require_once( 'wordlift_entity_functions.php' );

// add editor related methods.
require_once( 'wordlift_editor.php' );

// add the WordLift entity custom type.
require_once( 'wordlift_entity_type.php' );
require_once( 'wordlift_entity_type_taxonomy.php' );

// filters the post content when saving posts.
require_once( 'wordlift_content_filter.php' );
// add callbacks on post save to notify data changes from wp to redlink triple store
require_once( 'wordlift_to_redlink_data_push_callbacks.php' );

// Load modules
require_once( 'modules/analyzer/wordlift_analyzer.php' );
require_once( 'modules/linked_data/wordlift_linked_data.php' );
require_once( 'modules/prefixes/wordlift_prefixes.php' );
require_once( 'modules/caching/wordlift_caching.php' );
require_once( 'modules/redirector/wordlift_redirector.php' );
require_once( 'modules/freebase_image_proxy/wordlift_freebase_image_proxy.php' );

// Shortcodes

require_once( 'modules/geo_widget/wordlift_geo_widget.php' );
require_once( 'shortcodes/wordlift_shortcode_chord.php' );
require_once( 'shortcodes/wordlift_shortcode_geomap.php' );
require_once( 'shortcodes/wordlift_shortcode_field.php' );
require_once( 'shortcodes/wordlift_shortcode_faceted_search.php' );
require_once( 'shortcodes/wordlift_shortcode_navigator.php' );
require_once( 'shortcodes/wordlift_shortcode_blog_map.php' );

// disable In-Depth Articles
//require_once('wordlift_indepth_articles.php');

require_once( 'widgets/wordlift_widget_geo.php' );
require_once( 'widgets/wordlift_widget_chord.php' );
require_once( 'widgets/wordlift_widget_timeline.php' );

require_once( 'wordlift_sparql.php' );
require_once( 'wordlift_redlink.php' );

// Add admin functions.
// TODO: find a way to make 'admin' UI tests work.
//if ( is_admin() ) {

require_once( 'admin/wordlift_admin.php' );
require_once( 'admin/wordlift_admin_edit_post.php' );
require_once( 'admin/wordlift_admin_save_post.php' );

// add the entities meta box.
require_once( 'admin/wordlift_admin_meta_box_entities.php' );

// add the entity creation AJAX.
require_once( 'admin/wordlift_admin_ajax_related_posts.php' );

// Load the wl_chord TinyMCE button and configuration dialog.
require_once( 'admin/wordlift_admin_shortcodes.php' );

// Provide syncing features.
require_once( 'admin/wordlift_admin_sync.php' );
//}

// load languages.
// TODO: the following call gives for granted that the plugin is in the wordlift directory,
//       we're currently doing this because wordlift is symbolic linked.
load_plugin_textdomain( 'wordlift', false, '/wordlift/languages' );


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wordlift-activator.php
 */
function activate_wordlift() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wordlift-activator.php';
	Wordlift_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wordlift-deactivator.php
 */
function deactivate_wordlift() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wordlift-deactivator.php';
	Wordlift_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wordlift' );
register_deactivation_hook( __FILE__, 'deactivate_wordlift' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wordlift.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wordlift() {

	$plugin = new Wordlift();
	$plugin->run();

}

run_wordlift();
