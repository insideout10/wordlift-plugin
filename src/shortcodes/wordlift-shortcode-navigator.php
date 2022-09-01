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
	$cache_key_params = $_REQUEST;
	unset( $cache_key_params['uniqid'] );
	$cache_key = array( 'request_params' => $cache_key_params );

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

/**
 *
 * Network navigator callback function used by network-navigator endpoint
 *
 * @param $request
 *
 * @return array
 * @since 3.22.6
 */
function wl_network_navigator_wp_json( $request ) {

	// Create the cache key.
	$cache_key_params = $_REQUEST;
	unset( $cache_key_params['uniqid'] );
	$cache_key = array( 'request_params' => $cache_key_params );

	// Create the TTL cache and try to get the results.
	$cache         = new Ttl_Cache( 'network-navigator', 8 * 60 * 60 ); // 8 hours.
	$cache_results = $cache->get( $cache_key );

	if ( isset( $cache_results ) ) {
		header( 'X-WordLift-Cache: HIT' );

		return $cache_results;
	}

	header( 'X-WordLift-Cache: MISS' );

	$results = _wl_network_navigator_get_data( $request );

	// Put the result before sending the json to the client, since sending the json will terminate us.
	$cache->put( $cache_key, $results );

	return $results;

}

function _wl_navigator_get_data() {

	if ( isset( $_GET['wl_navigator_nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['wl_navigator_nonce'] ) ), 'wl_navigator' ) ) {
		return array();
	}

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

function _wl_network_navigator_get_data( $request ) {

	// Limit the results (defaults to 4)
	$navigator_length = isset( $request['limit'] ) ? intval( $request['limit'] ) : 4;
	$navigator_offset = isset( $request['offset'] ) ? intval( $request['offset'] ) : 0;
	$navigator_id     = $request['uniqid'];
	$order_by         = isset( $_GET['sort'] ) ? sanitize_sql_orderby( wp_unslash( $_GET['sort'] ) ) : 'ID DESC';

	$entities = $request['entities'];

	// Post ID has to match an existing item
	if ( ! isset( $entities ) || empty( $entities ) ) {
		wp_send_json_error( 'No valid entities provided' );
	}

	$referencing_posts = _wl_network_navigator_get_results(
		$entities,
		array(
			'ID',
			'post_title',
		),
		$order_by,
		$navigator_length,
		$navigator_offset
	);

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
				'permalink' => get_permalink( $referencing_post->ID ),
				'title'     => $referencing_post->post_title,
				'thumbnail' => $thumbnail,
			),
			'entity' => array(
				'label'     => $serialized_entity['label'],
				'mainType'  => $serialized_entity['mainType'],
				'permalink' => get_permalink( $referencing_post->entity_id ),
			),
		);

		$result['post']   = apply_filters( 'wl_network_navigator_data_post', $result['post'], intval( $referencing_post->ID ), $navigator_id );
		$result['entity'] = apply_filters( 'wl_network_navigator_data_entity', $result['entity'], intval( $referencing_post->entity_id ), $navigator_id );

		$results[] = $result;

	}

	if ( count( $results ) < $navigator_length ) {
		$results = apply_filters( 'wl_network_navigator_data_placeholder', $results, $navigator_id, $navigator_offset, $navigator_length );
	}

	$results = apply_filters( 'wl_network_navigator_results', $results, $navigator_id );

	return $results;

}

function _wl_network_navigator_get_results(
	$entities, $fields = array(
		'ID',
		'post_title',
	), $order_by = 'ID DESC', $limit = 10, $offset = 0
) {
	global $wpdb;

	$select = implode(
		', ',
		array_map(
			function ( $item ) {
				return "p.$item AS $item";
			},
			(array) $fields
		)
	);

	$order_by = implode(
		', ',
		array_map(
			function ( $item ) {
				return "p.$item";
			},
			(array) $order_by
		)
	);

	$entities_in = implode(
		',',
		array_map(
			function ( $item ) {
				$entity = Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( urldecode( $item ) );
				if ( isset( $entity ) ) {
					  return $entity->ID;
				}
			},
			$entities
		)
	);
// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
	/** @noinspection SqlNoDataSourceInspection */
	return $wpdb->get_results(
		$wpdb->prepare(
			"
SELECT %3\$s, p2.ID as entity_id
 FROM {$wpdb->prefix}wl_relation_instances r1
	-- get the ID of the post entity in common between the object and the subject 2. 
    INNER JOIN {$wpdb->posts} p2
        ON p2.ID = r1.object_id
            AND p2.post_status = 'publish'
    INNER JOIN {$wpdb->posts} p
        ON p.ID = r1.subject_id
            AND p.post_status = 'publish'
    INNER JOIN {$wpdb->term_relationships} tr
     	ON tr.object_id = p.ID
    INNER JOIN {$wpdb->term_taxonomy} tt
     	ON tt.term_taxonomy_id = tr.term_taxonomy_id
      	    AND tt.taxonomy = 'wl_entity_type'
    INNER JOIN {$wpdb->terms} t
        ON t.term_id = tt.term_id
            AND t.slug = 'article'
    -- select only posts with featured images.
 WHERE r1.object_id IN ({$entities_in})
 -- avoid duplicates.
 GROUP BY p.ID
 ORDER BY %4\$s
 LIMIT %1\$d
 OFFSET %2\$d
",
			$limit,
			$offset,
			$select,
			$order_by
		)
	);
// phpcs:enable
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
 * Adding `rest_api_init` action for backend of network navigator
 */
add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/network-navigator',
			array(
				'methods'             => 'GET',
				'callback'            => 'wl_network_navigator_wp_json',
				'permission_callback' => '__return_true',
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
		$action = array_key_exists( 'action', $_REQUEST ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['action'] ) ) : '';
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
		$action = array_key_exists( 'action', $_REQUEST ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['action'] ) ) : '';
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX || 'wl_navigator' !== $action ) {
			return;
		}

		remove_action( 'init', 'wp_widgets_init', 1 );
		remove_action( 'init', 'gglcptch_init' );
	},
	0
);

