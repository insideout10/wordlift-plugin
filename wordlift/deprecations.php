<?php

/**
 * Functions which should be relocated in OOP or removed.
 *
 * @since 3.33.9
 */

/**
 * Log to the debug.log file.
 *
 * @param string|mixed $log The log data.
 *
 * @since      3.0.0
 *
 * @deprecated use Wordlift_Log_Service::get_instance()->info( $log );
 */
function wl_write_log( $log ) {

	Wordlift_Log_Service::get_instance()->debug( $log );

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
			$allowedposttags[ $tag ] = array_merge( $allowedposttags[ $tag ], $new_attributes ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
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

	wp_enqueue_style( 'wordlift-reloaded', plugin_dir_url( __FILE__ ) . 'css/wordlift-reloaded.min.css', array(), WORDLIFT_VERSION );

	wp_enqueue_script( 'jquery-ui-autocomplete' );

	// Disable auto-save for custom entity posts only
	if ( Wordlift_Entity_Service::TYPE_NAME === get_post_type() ) {
		wp_dequeue_script( 'autosave' );
	}

}

add_action( 'admin_enqueue_scripts', 'wordlift_admin_enqueue_scripts' );

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

	return array_merge_recursive(
		$allowedtags,
		array(
			'span' => array(
				'itemscope' => true,
				'itemtype'  => true,
				'itemid'    => true,
				'itemprop'  => true,
			),
		)
	);
}

add_filter( 'wp_kses_allowed_html', 'wordlift_allowed_html', 10, 2 );

/**
 * Get all the images bound to a post.
 *
 * @param int $post_id The post ID.
 *
 * @return array An array of image URLs.
 * @deprecated use Wordlift_Storage_Factory::get_instance()->post_images()->get( $post_id )
 */
function wl_get_image_urls( $post_id ) {

	return Wordlift_Storage_Factory::get_instance()
								   ->post_images()
								   ->get( $post_id );

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

	$posts = get_posts(
		array(
			'post_type'      => 'attachment',
			'posts_per_page' => 1,
			'post_status'    => 'any',
			'post_parent'    => $parent_post_id,
			'meta_key'       => 'wl_source_url',
			'meta_value'     => $source_url,
		)
	);

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
 *
 * @param string $path The path to sanitize.
 * @param string $char The replacement character (by default an underscore).
 *
 * @return string The sanitized path.
 * @uses       sanitize_title() to manage not ASCII chars
 * @deprecated use Wordlift_Uri_Service::get_instance()->sanitize_path();
 * @see        https://codex.wordpress.org/Function_Reference/sanitize_title
 */
function wl_sanitize_uri_path( $path, $char = '_' ) {

	return Wordlift_Uri_Service::get_instance()->sanitize_path( $path, $char );
}

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
			if ( ! empty( $uri ) && $item_id !== $uri ) {
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

require_once 'wordlift-entity-functions.php';

// add editor related methods.
require_once 'wordlift-editor.php';

// add the WordLift entity custom type.
require_once 'wordlift-entity-type.php';

require_once 'modules/configuration/wordlift-configuration-settings.php';

// Load modules
require_once 'modules/analyzer/wordlift-analyzer.php';
require_once 'modules/linked_data/wordlift-linked-data.php';

// Shortcodes
require_once 'shortcodes/class-wordlift-shortcode-rest.php';
require_once 'shortcodes/wordlift-shortcode-chord.php';
require_once 'shortcodes/wordlift-shortcode-geomap.php';
require_once 'shortcodes/wordlift-shortcode-field.php';
require_once 'shortcodes/wordlift-shortcode-faceted-search.php';
require_once 'shortcodes/wordlift-shortcode-navigator.php';
require_once 'shortcodes/class-wordlift-products-navigator-shortcode-rest.php';

require_once 'widgets/class-wordlift-geo-widget.php';
require_once 'widgets/class-wordlift-chord-widget.php';
require_once 'widgets/class-wordlift-timeline-widget.php';

// Add admin functions.
// TODO: find a way to make 'admin' UI tests work.
// if ( is_admin() ) {

require_once 'admin/wordlift-admin.php';
require_once 'admin/wordlift-admin-edit-post.php';
require_once 'admin/wordlift-admin-save-post.php';

// add the entities meta box.
require_once 'admin/wordlift-admin-meta-box-entities.php';

// add the entity creation AJAX.
require_once 'admin/wordlift-admin-ajax-related-posts.php';

// Load the wl_chord TinyMCE button and configuration dialog.
require_once 'admin/wordlift-admin-shortcodes.php';
