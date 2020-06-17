<?php
/**
 * Shortcodes: Navigator Support Functions.
 *
 * @since      3.0.0
 * @package    Wordlift
 * @subpackage Wordlift/shortcodes
 */

use Wordlift\Cache\Ttl_Cache;

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
	$cache         = new Ttl_Cache( "navigator", 24 * 60 * 60 ); // 24 hours.
	$cache_results = $cache->get( $cache_key );

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
 *
 */
function wl_network_navigator_wp_json( $request ) {

	// Create the cache key.
	$cache_key_params = $_REQUEST;
	unset( $cache_key_params['uniqid'] );
	$cache_key = array( 'request_params' => $cache_key_params );

	// Create the TTL cache and try to get the results.
	$cache         = new Ttl_Cache( "network-navigator", 24 * 60 * 60 ); // 24 hours.
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

	// Post ID must be defined
	if ( ! isset( $_GET['post_id'] ) ) {
		wp_send_json_error( 'No post_id given' );

		return array();
	}

	// Limit the results (defaults to 4)
	$navigator_length = isset( $_GET['limit'] ) ? intval( $_GET['limit'] ) : 4;
	$navigator_offset = isset( $_GET['offset'] ) ? intval( $_GET['offset'] ) : 0;
	$order_by         = isset( $_GET['sort'] ) ? sanitize_sql_orderby( $_GET['sort'] ) : 'ID DESC';

	$current_post_id = $_GET['post_id'];
	$current_post    = get_post( $current_post_id );

	$navigator_id = $_GET['uniqid'];

	// Post ID has to match an existing item
	if ( null === $current_post ) {
		wp_send_json_error( 'No valid post_id given' );

		return array();
	}

	// Determine navigator type and call respective _get_results
	if ( get_post_type( $current_post_id ) === Wordlift_Entity_Service::TYPE_NAME ) {
		$referencing_posts = _wl_entity_navigator_get_results( $current_post_id, array(
			'ID',
			'post_title',
		), $order_by, $navigator_length, $navigator_offset );
	} else {
		$referencing_posts = _wl_navigator_get_results( $current_post_id, array(
			'ID',
			'post_title',
		), $order_by, $navigator_length, $navigator_offset );
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

		$result['post']   = apply_filters( 'wl_navigator_data_post', $result['post'], intval( $referencing_post->ID ), $navigator_id );
		$result['entity'] = apply_filters( 'wl_navigator_data_entity', $result['entity'], intval( $referencing_post->entity_id ), $navigator_id );

		$results[] = $result;
	}

	if ( count( $results ) < $navigator_length ) {
		$results = apply_filters( 'wl_navigator_data_placeholder', $results, $navigator_id, $navigator_offset, $navigator_length );
	}

	return $results;
}

function _wl_network_navigator_get_data( $request ) {

	// Limit the results (defaults to 4)
	$navigator_length = isset( $request['limit'] ) ? intval( $request['limit'] ) : 4;
	$navigator_offset = isset( $request['offset'] ) ? intval( $request['offset'] ) : 0;
	$navigator_id     = $request['uniqid'];
	$order_by         = isset( $_GET['sort'] ) ? sanitize_sql_orderby( $_GET['sort'] ) : 'ID DESC';

	$entities = $request['entities'];

	// Post ID has to match an existing item
	if ( ! isset( $entities ) || empty( $entities ) ) {
		wp_send_json_error( 'No valid entities provided' );
	}

	$referencing_posts = _wl_network_navigator_get_results( $entities, array(
		'ID',
		'post_title',
	), $order_by, $navigator_length, $navigator_offset );

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

	return $results;

}


function _wl_navigator_get_results(
	$post_id, $fields = array(
	'ID',
	'post_title',
), $order_by = 'ID DESC', $limit = 10, $offset = 0
) {
	global $wpdb;

	$select = implode( ', ', array_map( function ( $item ) {
		return "p.$item AS $item";
	}, (array) $fields ) );

	$order_by = implode( ', ', array_map( function ( $item ) {
		return "p.$item";
	}, (array) $order_by ) );

	/** @noinspection SqlNoDataSourceInspection */
	return $wpdb->get_results(
		$wpdb->prepare( <<<EOF
SELECT %4\$s, p2.ID as entity_id
 FROM {$wpdb->prefix}wl_relation_instances r1
    INNER JOIN {$wpdb->prefix}wl_relation_instances r2
        ON r2.object_id = r1.object_id
            AND r2.subject_id != %1\$d
	-- get the ID of the post entity in common between the object and the subject 2. 
    INNER JOIN {$wpdb->posts} p2
        ON p2.ID = r2.object_id
            AND p2.post_status = 'publish'
    INNER JOIN {$wpdb->posts} p
        ON p.ID = r2.subject_id
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
    INNER JOIN {$wpdb->postmeta} m
        ON m.post_id = p.ID
            AND m.meta_key = '_thumbnail_id'
 WHERE r1.subject_id = %1\$d
 -- avoid duplicates.
 GROUP BY p.ID
 ORDER BY %5\$s
 LIMIT %2\$d
 OFFSET %3\$d
EOF
			, $post_id, $limit, $offset, $select, $order_by )
	);

}

function _wl_entity_navigator_get_results(
	$post_id, $fields = array(
	'ID',
	'post_title',
), $order_by = 'ID DESC', $limit = 10, $offset = 0
) {
	global $wpdb;

	$select = implode( ', ', array_map( function ( $item ) {
		return "p.$item AS $item";
	}, (array) $fields ) );

	$order_by = implode( ', ', array_map( function ( $item ) {
		return "p.$item";
	}, (array) $order_by ) );

	/** @noinspection SqlNoDataSourceInspection */
	return $wpdb->get_results(
		$wpdb->prepare( <<<EOF
SELECT %4\$s, p2.ID as entity_id
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
    INNER JOIN {$wpdb->postmeta} m
        ON m.post_id = p.ID
            AND m.meta_key = '_thumbnail_id'
 WHERE r1.object_id = %1\$d
 -- avoid duplicates.
 GROUP BY p.ID
 ORDER BY %5\$s
 LIMIT %2\$d
 OFFSET %3\$d
EOF
			, $post_id, $limit, $offset, $select, $order_by )
	);
}

function _wl_network_navigator_get_results(
	$entities, $fields = array(
	'ID',
	'post_title',
), $order_by = 'ID DESC', $limit = 10, $offset = 0
) {
	global $wpdb;

	$select = implode( ', ', array_map( function ( $item ) {
		return "p.$item AS $item";
	}, (array) $fields ) );

	$order_by = implode( ', ', array_map( function ( $item ) {
		return "p.$item";
	}, (array) $order_by ) );

	$entities_in = implode( ',', array_map( function ( $item ) {
		$entity = Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( urldecode( $item ) );
		if ( isset( $entity ) ) {
			return $entity->ID;
		}
	}, $entities ) );

	/** @noinspection SqlNoDataSourceInspection */
	return $wpdb->get_results(
		$wpdb->prepare( <<<EOF
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
EOF
			, $limit, $offset, $select, $order_by )
	);

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
add_action( 'rest_api_init', function () {
	register_rest_route( WL_REST_ROUTE_DEFAULT_NAMESPACE, '/navigator', array(
		'methods'  => 'GET',
		'callback' => 'wl_shortcode_navigator_wp_json',
	) );
} );

/**
 * Adding `rest_api_init` action for backend of network navigator
 */
add_action( 'rest_api_init', function () {
	register_rest_route( WL_REST_ROUTE_DEFAULT_NAMESPACE, '/network-navigator', array(
		'methods'  => 'GET',
		'callback' => 'wl_network_navigator_wp_json',
	) );
} );

/**
 * register_block_type for Gutenberg blocks
 */
add_action( 'init', function () {
	// Bail out if the `register_block_type` function isn't available.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	register_block_type( 'wordlift/navigator', array(
		'editor_script'   => 'wl-block-editor',
		'render_callback' => function ( $attributes ) {
			$attr_code = '';
			foreach ( $attributes as $key => $value ) {
				$attr_code .= $key . '="' . htmlentities( $value ) . '" ';
			}

			return '[wl_navigator ' . $attr_code . ']';
		},
		'attributes'      => array(
			'title'       => array(
				'type'    => 'string',
				'default' => __( 'Related articles', 'wordlift' ),
			),
			'limit'       => array(
				'type'    => 'number',
				'default' => 4,
			),
			'template_id' => array(
				'type' => 'string',
			),
			'post_id'     => array(
				'type' => 'number',
			),
			'offset'      => array(
				'type'    => 'number',
				'default' => 0,
			),
			'uniqid'      => array(
				'type'    => 'string',
				'default' => uniqid( 'wl-navigator-widget-' ),
			),
		),
	) );
} );

/**
 * Optimizations: disable unneeded plugins on Navigator AJAX call. WPSeo is slowing down the responses quite a bit.
 *
 * @since 2.2.0
 */
add_action( 'plugins_loaded', function () {

	if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX || 'wl_navigator' !== $_REQUEST['action'] ) {
		return;
	}

	remove_action( 'plugins_loaded', 'rocket_init' );
	remove_action( 'plugins_loaded', 'wpseo_premium_init', 14 );
	remove_action( 'plugins_loaded', 'wpseo_init', 14 );
}, 0 );

add_action( 'init', function () {

	if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX || 'wl_navigator' !== $_REQUEST['action'] ) {
		return;
	}

	remove_action( 'init', 'wp_widgets_init', 1 );
	remove_action( 'init', 'gglcptch_init' );
}, 0 );

