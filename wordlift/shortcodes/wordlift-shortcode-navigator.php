<?php
/**
 * Shortcodes: Navigator Support Functions.
 *
 * @since      3.0.0
 * @package    Wordlift
 * @subpackage Wordlift/shortcodes
 */

use Wordlift\Cache\Ttl_Cache;
use Wordlift\Widgets\Navigator\Filler_Posts\Filler_Posts_Util;
use Wordlift\Widgets\Navigator\Navigator_Data;
use Wordlift\Widgets\Srcset_Util;

/**
 * The Navigator data function.
 *
 * @since 3.0.0
 */
function wl_shortcode_navigator_data() {

	// Create the cache key.
	$cache_params = array_intersect_key(
		$_REQUEST, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		array(
			'limit'      => 1,
			'offset'     => 1,
			'sort'       => 1,
			'post_types' => 1,
			'post_id'    => 1,
		)
	);

	$cache_key = array( 'request_params' => $cache_params );

	// Create the TTL cache and try to get the results.
	$cache         = new Ttl_Cache( 'navigator', 8 * 60 * 60 ); // 8 hours.
	$cache_results = $cache->get( $cache_key );

	// So that the endpoint can be used remotely
	header( 'Access-Control-Allow-Origin: *' );

	if ( isset( $cache_results ) ) {
		header( 'X-WordLift-Cache: HIT' );

		return $cache_results;
	}

	header( 'X-WordLift-Cache: MISS' );

	$results = _wl_navigator_get_data();

	// Put the result before sending the json to the client, since sending the json will terminate us.
	$cache->put( $cache_key, $results );

	return $results;
}

function _wl_navigator_get_data() {

	check_ajax_referer( 'wl_navigator' );

	// Post ID must be defined
	if ( ! isset( $_GET['post_id'] ) ) {
		wp_send_json_error( 'No post_id given' );

		return array();
	}

	// Post ID must be defined
	if ( ! isset( $_GET['uniqid'] ) ) {
		wp_send_json_error( 'No uniqid given' );

		return array();
	}

	// Limit the results (defaults to 4)
	$navigator_length    = isset( $_GET['limit'] ) ? intval( $_GET['limit'] ) : 4;
	$navigator_offset    = isset( $_GET['offset'] ) ? intval( $_GET['offset'] ) : 0;
	$order_by            = isset( $_GET['sort'] ) ? sanitize_sql_orderby( wp_unslash( $_GET['sort'] ) ) : 'ID DESC';
	$post_types          = isset( $_GET['post_types'] ) ? sanitize_text_field( wp_unslash( $_GET['post_types'] ) ) : '';
	$post_types          = explode( ',', $post_types );
	$existing_post_types = get_post_types();
	$post_types          = array_values( array_intersect( $existing_post_types, $post_types ) );
	$current_post_id     = (int) $_GET['post_id'];
	$current_post        = get_post( $current_post_id );

	$navigator_id = sanitize_text_field( wp_unslash( $_GET['uniqid'] ) );

	// Post ID has to match an existing item
	if ( null === $current_post ) {
		wp_send_json_error( 'No valid post_id given' );

		return array();
	}

	// Determine navigator type and call respective _get_results
	if ( get_post_type( $current_post_id ) === Wordlift_Entity_Service::TYPE_NAME ) {

		$referencing_posts = Navigator_Data::entity_navigator_get_results(
			$current_post_id,
			array(
				'ID',
				'post_title',
			),
			$order_by,
			$navigator_length,
			$navigator_offset,
			$post_types
		);
	} else {
		$referencing_posts = Navigator_Data::post_navigator_get_results(
			$current_post_id,
			array(
				'ID',
				'post_title',
			),
			$order_by,
			$navigator_length,
			$navigator_offset,
			$post_types
		);

	}

	// loop over them and take the first one which is not already in the $related_posts
	$results = array();
	foreach ( $referencing_posts as $referencing_post ) {
		$serialized_entity = wl_serialize_entity( $referencing_post->entity_id );

		/**
		 * Use the thumbnail.
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/825 related issue.
		 * @see https://github.com/insideout10/wordlift-plugin/issues/837
		 *
		 * @since 3.19.3 We're using the medium size image.
		 */
		$thumbnail = get_the_post_thumbnail_url( $referencing_post, 'medium' );

		$result = array(
			'post'   => array(
				'id'        => $referencing_post->ID,
				'permalink' => get_permalink( $referencing_post->ID ),
				'title'     => html_entity_decode( $referencing_post->post_title, ENT_QUOTES, 'UTF-8' ),
				'thumbnail' => $thumbnail,
				'srcset'    => Srcset_Util::get_srcset( $referencing_post->ID, Srcset_Util::NAVIGATOR_WIDGET ),
			),
			'entity' => array(
				'id'        => $referencing_post->entity_id,
				'label'     => $serialized_entity['label'],
				'mainType'  => $serialized_entity['mainType'],
				'permalink' => get_permalink( $referencing_post->entity_id ),
			),
		);

		$results[] = $result;
	}

	if ( count( $results ) < $navigator_length ) {
		$results = apply_filters( 'wl_navigator_data_placeholder', $results, $navigator_id, $navigator_offset, $navigator_length );
	}

	// Add filler posts if needed
	$filler_count = $navigator_length - count( $results );
	if ( $filler_count > 0 ) {
		$referencing_post_ids = array_map(
			function ( $p ) {
				return $p->ID;
			},
			$referencing_posts
		);
		/**
		 * @since 3.27.8
		 * Filler posts are fetched using this util.
		 */
		$filler_posts_util       = new Filler_Posts_Util( $current_post_id, $post_types );
		$post_ids_to_be_excluded = array_merge( array( $current_post_id ), $referencing_post_ids );
		$filler_posts            = $filler_posts_util->get_filler_response( $filler_count, $post_ids_to_be_excluded );
		$results                 = array_merge( $results, $filler_posts );
	}

	// Apply filters after fillers are added
	foreach ( $results as $result_index => $result ) {
		$results[ $result_index ]['post']   = apply_filters( 'wl_navigator_data_post', $result['post'], intval( $result['post']['id'] ), $navigator_id );
		$results[ $result_index ]['entity'] = apply_filters( 'wl_navigator_data_entity', $result['entity'], intval( $result['entity']['id'] ), $navigator_id );
	}

	$results = apply_filters( 'wl_navigator_results', $results, $navigator_id, $current_post_id );

	return $results;
}

/**
 * The Navigator Ajax function.
 *
 * @since 3.20.0
 */
function wl_shortcode_navigator_ajax() {

	// Temporary blocking the Navigator.
	$results = wl_shortcode_navigator_data();
	wl_core_send_json( $results );

}

add_action( 'wp_ajax_wl_navigator', 'wl_shortcode_navigator_ajax' );
add_action( 'wp_ajax_nopriv_wl_navigator', 'wl_shortcode_navigator_ajax' );

/**
 * wp-json call for the navigator widget
 */
function wl_shortcode_navigator_wp_json() {

	$results = wl_shortcode_navigator_data();
	if ( ob_get_contents() ) {
		ob_clean();
	}

	return array(
		'items' => array(
			array( 'values' => $results ),
		),
	);

}

/**
 * Adding `rest_api_init` action for amp backend of navigator
 */
add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/navigator',
			array(
				'methods'             => 'GET',
				'permission_callback' => '__return_true',
				'callback'            => 'wl_shortcode_navigator_wp_json',
			)
		);
	}
);

/**
 * Optimizations: disable unneeded plugins on Navigator AJAX call. WPSeo is slowing down the responses quite a bit.
 *
 * @since 2.2.0
 */
add_action(
	'plugins_loaded',
	function () {
		$action = array_key_exists( 'action', $_REQUEST ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['action'] ) ) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX || 'wl_navigator' !== $action ) {
			return;
		}

		remove_action( 'plugins_loaded', 'rocket_init' );
		remove_action( 'plugins_loaded', 'wpseo_premium_init', 14 );
		remove_action( 'plugins_loaded', 'wpseo_init', 14 );
	},
	0
);

add_action(
	'init',
	function () {
		$action = array_key_exists( 'action', $_REQUEST ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['action'] ) ) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX || 'wl_navigator' !== $action ) {
			return;
		}

		remove_action( 'init', 'wp_widgets_init', 1 );
		remove_action( 'init', 'gglcptch_init' );
	},
	0
);

