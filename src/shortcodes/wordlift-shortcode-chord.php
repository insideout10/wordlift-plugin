<?php
/**
 * Chord Shortcode.
 *
 * @since      3.0.0
 * @package    Wordlift
 * @subpackage Wordlift/shortcodes
 */

/**
 * Get entity with more relations (only used for the global chord).
 *
 * @used-by wl_chord_widget_func
 *
 * @return mixed
 */
function wl_shortcode_chord_most_referenced_entity_id() {
	// Get the last 20 articles by post date.
	// For each article get the entities they reference.
	$post_ids = get_posts(
		array(
			'numberposts' => 20,
			'post_type'   => Wordlift_Entity_Service::valid_entity_post_types(),
			'fields'      => 'ids', // Only get post IDs.
			'post_status' => 'publish',
			'tax_query'   => array(
				'relation' => 'OR',
				array(
					'taxonomy' => Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
					'operator' => 'NOT EXISTS',
				),
				array(
					'taxonomy' => Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
					'field'    => 'slug',
					'terms'    => 'article',
				),
			),
			'orderby'     => 'post_date',
			'order'       => 'DESC',
		)
	);

	if ( empty( $post_ids ) ) {
		return null;
	}

	$entities = array();
	foreach ( $post_ids as $id ) {
		$entities = array_merge( $entities, wl_core_get_related_entity_ids( $id ) );
	}

	$famous_entities = array_count_values( $entities );
	arsort( $famous_entities );
	if ( count( $famous_entities ) >= 1 ) {
		return key( $famous_entities );
	} else {
		return $post_ids[0];
	}

}

/**
 * Recursive function used to retrieve related content starting from a post ID.
 *
 * @param int   $entity_id The entity post ID.
 * @param int   $depth Max number of nesting levels in output.
 * @param array $related An existing array of related entities.
 * @param int   $max_size Max number of items.
 *
 * @return array
 * @uses wl_core_get_related_post_ids() to get the list of post ids that reference an entity.
 */
function wl_shortcode_chord_get_relations( $entity_id, $depth = 2, $related = null, $max_size = 9 ) {

	if ( $related !== null ) {
		if ( 0 === $depth ) {
			return $related;
		}
	}

	wl_write_log( "wl_shortcode_chord_get_relations [ post id :: $entity_id ][ depth :: $depth ][ related? :: " . ( $related === null ? 'yes' : 'no' ) . ' ]' );

	// Create a related array which will hold entities and relations.
	if ( $related === null ) {
		$related = array(
			'entities'  => array( $entity_id ),
			'relations' => array(),
		);
	}

	// Get related entities
	$related_entity_ids = wl_core_get_related_entity_ids(
		$entity_id,
		array(
			'status' => 'publish',
		)
	);

	// If the current node is an entity, add related posts too
	$related_post_ids = ( Wordlift_Entity_Service::get_instance()
												 ->is_entity( $entity_id ) ) ?
		wl_core_get_related_post_ids(
			$entity_id,
			array(
				'status' => 'publish',
			)
		) :
		array();

	// Merge results and remove duplicated entries
	$related_ids = array_unique( array_merge( $related_post_ids, $related_entity_ids ) );

	// TODO: List of entities ($rel) should be ordered by interest factors.
	shuffle( $related_ids );

	// Now we have all the related IDs.
	foreach ( $related_ids as $related_id ) {

		if ( count( $related['entities'] ) >= $max_size ) {
			return $related;
		}

		$related['relations'][] = array( $entity_id, $related_id );

		if ( ! in_array( $related_id, $related['entities'], true ) ) {
			// Found new related entity!
			$related['entities'][] = $related_id;

			$related = wl_shortcode_chord_get_relations( $related_id, ( $depth - 1 ), $related, $max_size );
		}
	}

	// End condition 2: no more entities to search for.
	return $related;
}

/**
 * Optimize and convert retrieved content to JSON.
 *
 * @used-by wl_shortcode_chord_ajax
 *
 * @param $data
 *
 * @return mixed|string
 */
function wl_shortcode_chord_get_graph( $data ) {

	// Refactor the entities array in order to provide entities relevant data (uri, url, label, type, css_class).
	array_walk(
		$data['entities'],
		function ( &$item ) {
			$post = get_post( $item );

			// Skip non-existing posts.
			if ( $post === null ) {
				wl_write_log( "wl_shortcode_chord_get_graph : post not found [ post id :: $item ]" );

				return $item;
			}

			// Get the entity taxonomy bound to this post (if there's no taxonomy, no stylesheet will be set).
			$term = Wordlift_Entity_Type_Service::get_instance()->get( $item );

			// The following log may create a circular loop.
			// wl_write_log( "wl_shortcode_chord_get_graph [ post id :: $post->ID ][ term :: " . var_export( $term, true ) . " ]" );

			// TODO: get all images
			$thumbnail    = null;
			$thumbnail_id = get_post_thumbnail_id( $post->ID );
			if ( '' !== $thumbnail_id && 0 !== $thumbnail_id ) {
				$attachment = wp_get_attachment_image_src( $thumbnail_id );
				if ( false !== $attachment ) {
					$thumbnail = esc_attr( $attachment[0] );
				}
			}

			$entity = array(
				'uri'        => wl_get_entity_uri( $item ),
				'url'        => get_permalink( $item ),
				'label'      => $post->post_title,
				'type'       => $post->post_type,
				'thumbnails' => array( $thumbnail ),
				'css_class'  => ( isset( $term['css_class'] ) ? $term['css_class'] : '' ),
			);

			$item = $entity;
		}
	);

	// Refactor the relations.
	array_walk(
		$data['relations'],
		function ( &$item ) {
			$relation = array(
				's' => wl_get_entity_uri( $item[0] ),
				'o' => wl_get_entity_uri( $item[1] ),
			);

			$item = $relation;
		}
	);

	// Return the JSON representation.
	return $data;
}

/**
 * Retrieve related entities and output them in JSON.
 *
 * @uses wl_shortcode_chord_get_relations()
 * @uses wl_shortcode_chord_get_graph()
 */
function wl_shortcode_chord_ajax() {

	check_ajax_referer( 'wl_chord', 'wl_chord_nonce' );

	$post_id = isset( $_REQUEST['post_id'] ) ? (int) $_REQUEST['post_id'] : 0;
	$depth   = isset( $_REQUEST['depth'] ) ? (int) $_REQUEST['depth'] : 2;

	$relations = wl_shortcode_chord_get_relations( $post_id, $depth );
	$graph     = wl_shortcode_chord_get_graph( $relations );

	wl_core_send_json( $graph );
}

add_action( 'wp_ajax_wl_chord', 'wl_shortcode_chord_ajax' );
add_action( 'wp_ajax_nopriv_wl_chord', 'wl_shortcode_chord_ajax' );
