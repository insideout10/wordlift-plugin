<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wordlift.io
 * @since             1.0.0
 * @package           Wordlift
 *
 * @wordpress-plugin
 * Plugin Name:       WordLift
 * Plugin URI:        https://wordlift.io
 * Description:       WordLift brings the power of AI to organize content, attract new readers and get their attention. To activate the plugin <a href="https://wordlift.io/">visit our website</a>.
 * Version:           3.20.0-dev4
 * Author:            WordLift, Insideout10
 * Author URI:        https://wordlift.io
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

// Load modules.
require_once( 'modules/core/wordlift_core.php' );

/**
 * Log to the debug.log file.
 *
 * @deprecated use Wordlift_Log_Service::get_instance()->info( $log );
 *
 * @since      3.0.0
 *
 * @param string|mixed $log The log data.
 */
function wl_write_log( $log ) {

	Wordlift_Log_Service::get_instance()->debug( $log );

}

/**
 * Hide the WordLift Key from the provided text.
 *
 * @deprecated
 *
 * @since 3.0.0
 *
 * @param string $text A text that may potentially contain a WL key.
 *
 * @return string A text with the key hidden.
 */
function wl_write_log_hide_key( $text ) {

	return str_ireplace( wl_configuration_get_key(), '<hidden>', $text );
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
		'itemid'    => array(),
	);

	foreach ( $tags as $tag ) {
		if ( isset( $allowedposttags[ $tag ] ) && is_array( $allowedposttags[ $tag ] ) ) {
			$allowedposttags[ $tag ] = array_merge( $allowedposttags[ $tag ], $new_attributes );
		}
	}
}

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

	$log = Wordlift_Log_Service::get_logger( 'wordlift_admin_enqueue_scripts' );
	$log->trace( 'Registering admin scripts...' );

	// We now register angular scripts and have dependent scripts (currently
	// only the edit post page) depend on them, to avoid potential conflicts.
	//
	// See https://github.com/insideout10/wordlift-plugin/issues/691.
	$result = wp_register_script( 'wl-angular', 'https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.3.11/angular.min.js' )
	          && wp_register_script( 'wl-angular-geolocation', plugin_dir_url( __FILE__ ) . 'bower_components/angularjs-geolocation/dist/angularjs-geolocation.min.js' )
	          && wp_register_script( 'wl-angular-touch', 'https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.3.11/angular-touch.min.js' )
	          && wp_register_script( 'wl-angular-animate', 'https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.3.11/angular-animate.min.js' );

	$log->debug( 'Registering angular scripts was ' . ( $result ? 'successful.' : 'unsuccessful.' ) );

	// Disable auto-save for custom entity posts only
	if ( Wordlift_Entity_Service::TYPE_NAME === get_post_type() ) {
		wp_dequeue_script( 'autosave' );
	}

}

add_action( 'admin_enqueue_scripts', 'wordlift_admin_enqueue_scripts' );

// We shouldn't load the wordlift-ui.min.css stylesheet when not needed.
//
// @see https://github.com/insideout10/wordlift-plugin/issues/699
//function wl_enqueue_scripts() {
//	wp_enqueue_style( 'wordlift-ui', plugin_dir_url( __FILE__ ) . 'css/wordlift-ui.min.css' );
//}
//
//add_action( 'wp_enqueue_scripts', 'wl_enqueue_scripts' );

/**
 * Hooked to *wp_kses_allowed_html* filter, adds microdata attributes.
 *
 * @param array  $allowedtags The array with the currently configured elements and attributes.
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
			'itemprop'  => true,
		),
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

	// DO NOT set latitude/longitude to 0/0 as default values. It's a specific
	// place on the globe:"The zero/zero point of this system is located in the
	// Gulf of Guinea about 625 km (390 mi) south of Tema, Ghana."
	return array(
		'latitude'  => isset( $latitude[0] ) && is_numeric( $latitude[0] ) ? $latitude[0] : '',
		'longitude' => isset( $longitude[0] ) && is_numeric( $longitude[0] ) ? $longitude[0] : '',
	);
}

/**
 * Get all the images bound to a post.
 *
 * @deprecated use Wordlift_Storage_Factory::get_instance()->post_images()->get( $post_id )
 *
 * @param int $post_id The post ID.
 *
 * @return array An array of image URLs.
 */
function wl_get_image_urls( $post_id ) {

	return Wordlift_Storage_Factory::get_instance()
	                               ->post_images()
	                               ->get( $post_id );

//	// If there is a featured image it has the priority.
//	$featured_image_id = get_post_thumbnail_id( $post_id );
//	if ( is_numeric( $featured_image_id ) ) {
//		$image_url = wp_get_attachment_url( $featured_image_id );
//
//		return array( $image_url );
//	}
//
//	$images = get_children( array(
//		'post_parent'    => $post_id,
//		'post_type'      => 'attachment',
//		'post_mime_type' => 'image',
//	) );
//
//	// Return an empty array if no image is found.
//	if ( empty( $images ) ) {
//		return array();
//	}
//
//	// Prepare the return array.
//	$image_urls = array();
//
//	// Collect the URLs.
//	foreach ( $images as $attachment_id => $attachment ) {
//		$image_url = wp_get_attachment_url( $attachment_id );
//		// Ensure the URL isn't collected already.
//		if ( ! in_array( $image_url, $image_urls ) ) {
//			array_push( $image_urls, $image_url );
//		}
//	}
//
//	// wl_write_log( "wl_get_image_urls [ post id :: $post_id ][ image urls count :: " . count( $image_urls ) . " ]" );
//
//	return $image_urls;
}

/**
 * Get an attachment with the specified parent post ID and source URL.
 *
 * @param int    $parent_post_id The parent post ID.
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
		'meta_value'     => $source_url,
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
 * @param int    $post_id The post ID.
 * @param string $source_url The source URL.
 */
function wl_set_source_url( $post_id, $source_url ) {

	delete_post_meta( $post_id, 'wl_source_url' );
	add_post_meta( $post_id, 'wl_source_url', $source_url );
}

/**
 * Sanitizes an URI path by replacing the non allowed characters with an underscore.
 * @uses       sanitize_title() to manage not ASCII chars
 * @deprecated use Wordlift_Uri_Service::get_instance()->sanitize_path();
 * @see        https://codex.wordpress.org/Function_Reference/sanitize_title
 *
 * @param string $path The path to sanitize.
 * @param string $char The replacement character (by default an underscore).
 *
 * @return string The sanitized path.
 */
function wl_sanitize_uri_path( $path, $char = '_' ) {

	return Wordlift_Uri_Service::get_instance()->sanitize_path( $path, $char );
}

///**
// * Schedule the execution of SPARQL Update queries before the WordPress look ends.
// */
//function wl_shutdown() {
//
//	// Get the filename to the temporary SPARQL file.
//	$filename = WL_TEMP_DIR . WL_REQUEST_ID . '.sparql';
//
//	// If WordLift is buffering SPARQL queries, we're admins and a buffer exists, then schedule it.
//	if ( WL_ENABLE_SPARQL_UPDATE_QUERIES_BUFFERING && is_admin() && file_exists( $filename ) ) {
//
//		// The request ID.
//		$args = array( WL_REQUEST_ID );
//
//		// Schedule the execution of the SPARQL query with the request ID.
//		wp_schedule_single_event( time(), 'wl_execute_saved_sparql_update_query', $args );
//
//		// Check that the request is scheduled.
//		$timestamp = wp_next_scheduled( 'wl_execute_saved_sparql_update_query', $args );
//
//		// Spawn the cron.
//		spawn_cron();
//
//		wl_write_log( "wl_shutdown [ request id :: " . WL_REQUEST_ID . " ][ timestamp :: $timestamp ]" );
//	}
//}
//
//add_action( 'shutdown', 'wl_shutdown' );

/**
 * Replaces the *itemid* attributes URIs with the WordLift URIs.
 *
 * @param string $content The post content.
 *
 * @return string The updated post content.
 */
function wl_replace_item_id_with_uri( $content ) {

	$log = Wordlift_Log_Service::get_logger( 'wl_replace_item_id_with_uri' );
	$log->trace( 'Replacing item IDs with URIs...' );

	// Strip slashes, see https://core.trac.wordpress.org/ticket/21767
	$content = stripslashes( $content );

	// If any match are found.
	$matches = array();
	if ( 0 < preg_match_all( '/ itemid="([^"]+)"/i', $content, $matches, PREG_SET_ORDER ) ) {

		foreach ( $matches as $match ) {

			// Get the item ID.
			$item_id = $match[1];

			// Get the post bound to that item ID (looking both in the 'official' URI and in the 'same-as' .
			$post = Wordlift_Entity_Service::get_instance()
			                               ->get_entity_post_by_uri( $item_id );

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

// add callbacks on post save to notify data changes from wp to redlink triple store
require_once( 'wordlift_to_redlink_data_push_callbacks.php' );

require_once( 'modules/configuration/wordlift_configuration_settings.php' );

// Load modules
require_once( 'modules/analyzer/wordlift_analyzer.php' );
require_once( 'modules/linked_data/wordlift_linked_data.php' );
require_once( 'modules/prefixes/wordlift_prefixes.php' );

// Shortcodes

require_once( 'modules/geo_widget/wordlift_geo_widget.php' );
require_once( 'shortcodes/wordlift_shortcode_chord.php' );
require_once( 'shortcodes/wordlift_shortcode_geomap.php' );
require_once( 'shortcodes/wordlift_shortcode_field.php' );
require_once( 'shortcodes/wordlift_shortcode_faceted_search.php' );
require_once( 'shortcodes/wordlift_shortcode_navigator.php' );

require_once( 'widgets/wordlift_widget_geo.php' );
require_once( 'widgets/class-wordlift-chord-widget.php' );
require_once( 'widgets/wordlift_widget_timeline.php' );

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

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wordlift-activator.php
 */
function activate_wordlift() {

	$log = Wordlift_Log_Service::get_logger( 'activate_wordlift' );

	$log->info( 'Activating WordLift...' );

	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wordlift-activator.php';
	Wordlift_Activator::activate();

	/**
	 * Tell the {@link Wordlift_Http_Api} class that we're activating, to let it run activation tasks.
	 *
	 * @see https://github.com/insideout10/wordlift-plugin/issues/820 related issue.
	 * @since 3.19.2
	 */
	Wordlift_Http_Api::activate();

	// Ensure the post type is registered before flushing the rewrite rules.
	Wordlift_Entity_Post_Type_Service::get_instance()->register();
	flush_rewrite_rules();

}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wordlift-deactivator.php
 */
function deactivate_wordlift() {

	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wordlift-deactivator.php';
	Wordlift_Deactivator::deactivate();
	Wordlift_Http_Api::deactivate();
	flush_rewrite_rules();

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
