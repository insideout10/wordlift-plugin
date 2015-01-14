<?php

/**
 * Get the IDs of posts related to the specified post.
 *
 * @param int $post_id The post ID.
 *
 * @return array An array of posts related to the one specified.
 */
function wl_get_related_post_ids( $post_id ) {

	// Get the related array ( the field was serialized before WL3 ).
	$related = get_post_meta( $post_id, WL_CUSTOM_FIELD_RELATED_POST );

	wl_write_log( "wl_get_related_post_ids [ post id :: $post_id ][ empty related :: " . ( empty( $related ) ? 'true' : 'false' ) . "  ]" );

	if ( empty( $related ) ) {
		return array();
	}

	// Ensure an array is returned.
	return ( is_array( $related )
		? $related
		: array( $related ) );
}

/**
 * Set the related posts IDs for the specified post ID.
 *
 * @param int $post_id A post ID.
 * @param array $related_posts An array of related post IDs.
 */
function wl_set_related_posts( $post_id, $related_posts ) {

	wl_write_log( "wl_set_related_posts [ post id :: $post_id ][ related posts :: " . join( ',', $related_posts ) . " ]" );

	delete_post_meta( $post_id, WL_CUSTOM_FIELD_RELATED_POST );
	add_post_meta( $post_id, WL_CUSTOM_FIELD_RELATED_POST, $related_posts );
}

/**
 * Set the related posts IDs for the specified post ID.
 *
 * @param int $post_id A post ID.
 * @param int|array $new_post_ids An array of related post IDs.
 */
function wl_add_related_posts( $post_id, $new_post_ids ) {

	// Convert the parameter to an array.
	$new_post_ids = ( is_array( $new_post_ids ) ? $new_post_ids : array( $new_post_ids ) );

	wl_write_log( "wl_add_related_posts [ post id :: $post_id ][ new post ids :: " . join( ',', $new_post_ids ) . " ]" );

	// Get the existing post IDs and merge them together.
	$related = wl_get_related_post_ids( $post_id );
	$related = array_unique( array_merge( $related, $new_post_ids ) );

	wl_set_related_posts( $post_id, $related );
}


/**
 * Set the related entity posts IDs for the specified post ID.
 *
 * @param int $post_id A post ID.
 * @param array $related_entities An array of related entity post IDs.
 */
function wl_set_referenced_entities( $post_id, $related_entities ) {

	wl_write_log( "wl_set_referenced_entities [ post id :: $post_id ][ related entities :: " . join( ',', $related_entities ) . " ]" );

	delete_post_meta( $post_id, WL_CUSTOM_FIELD_REFERENCED_ENTITY );

	foreach ( $related_entities as $entity_post_id ) {
		add_post_meta( $post_id, WL_CUSTOM_FIELD_REFERENCED_ENTITY, $entity_post_id );
	}
}

/**
 * Get the posts that reference the specified entity.
 *
 * @uses    wl_get_referenced_entity_ids to get entities related to posts.
 * @used-by wl_shortcode_chord_get_relations
 *
 * @param int $entity_id The post ID of the entity.
 *
 * @return array An array of posts.
 */
function wl_get_referencing_posts( $entity_id ) {

	$args = array(
		'posts_per_page' => - 1,
		'post_type'      => 'any',
		'post_status'    => 'any',
		'meta_key'       => WL_CUSTOM_FIELD_REFERENCED_ENTITY,
		'meta_value'     => $entity_id
	);

	$posts = array_filter( get_posts( $args ), function ( $item ) {
		return ( WL_ENTITY_TYPE_NAME !== $item->post_type );
	} );

	wl_write_log( "wl_get_referencing_posts [ entity id :: $entity_id ][ posts count :: " . count( $posts ) . " ]" );

	return $posts;
}

/**
 * Set the related entity posts IDs for the specified post ID.
 *
 * @param int $post_id A post ID.
 * @param int|array $new_entity_post_ids An array of related entity post IDs.
 */
function wl_add_referenced_entities( $post_id, $new_entity_post_ids ) {

	// Convert the parameter to an array.
	$new_entity_post_ids = ( is_array( $new_entity_post_ids ) ? $new_entity_post_ids : array( $new_entity_post_ids ) );

	wl_write_log( "wl_add_referenced_entities [ post id :: $post_id ][ related entities :: " . join( ',', $new_entity_post_ids ) . " ]" );

	// Get the existing post IDs and merge them together.
	$related = wl_get_referenced_entity_ids( $post_id );
	$related = array_unique( array_merge( $related, $new_entity_post_ids ) );

	wl_set_referenced_entities( $post_id, $related );
}

/**
 * Get the IDs of entities related to the specified post.
 *
 * @param int $post_id The post ID.
 *
 * @return array An array of posts related to the one specified.
 */
function wl_get_referenced_entity_ids( $post_id ) {

	// Get the related array.
	$result = get_post_meta( $post_id, WL_CUSTOM_FIELD_REFERENCED_ENTITY );

	// The following is necessary to maintain compatibility with the previous way of storing this data, i.e. as an
	// array as single key.
	if ( isset( $result[0] ) && is_array( $result[0] ) ) {
		$result = $result[0];
	}

	wl_write_log( "wl_get_referenced_entity_ids [ post id :: $post_id ][ result :: " . var_export( $result, true ) . " ]" );

	return $result;
}

/**
 * Add related post IDs to the specified post ID, automatically choosing whether to add the related to entities or to
 * posts.
 *
 * @param int $post_id The post ID.
 * @param int|array $related_id A related post/entity ID or an array of posts/entities.
 */
function wl_add_related( $post_id, $related_id ) {

	// Ensure we're dealing with an array.
	$related_id_array = ( is_array( $related_id ) ? $related_id : array( $related_id ) );

	// Prepare the related arrays.
	$related_entities = array();
	$related_posts    = array();

	foreach ( $related_id_array as $id ) {

		// If it's an entity add the entity to the related entities.
		if ( 'entity' === get_post_type( $id ) ) {
			array_push( $related_entities, $id );
		} else {
			// Else add it to the related posts.
			array_push( $related_posts, $id );
		}
	}

	if ( 0 < count( $related_entities ) ) {
		wl_add_referenced_entities( $post_id, $related_entities );
	}

	// TODO: check this, we're adding related posts to a post.
	if ( 0 < count( $related_posts ) ) {
		wl_add_related_posts( $post_id, $related_posts );
	}
}

///**
// * Unbind post and entities.
// * @param int $post_id The post ID.
// */
//function wl_unbind_post_from_entities($post_id)
//{
//
//    wl_write_log("wl_unbind_post_from_entities [ post id :: $post_id ]");
//
//    $entities = wl_get_referenced_entity_ids($post_id);
//    foreach ($entities as $entity_post_id) {
//
//        // Remove the specified post id from the list of related posts.
//        $related_posts = wl_get_related_post_ids($entity_post_id);
//        if (false !== ($key = array_search($post_id, $related_posts))) {
//            unset($related_posts[$key]);
//        }
//
//        wl_set_related_posts($entity_post_id, $related_posts);
//    }
//
//    // Reset the related entities for the post.
//    wl_set_referenced_entities($post_id, array());
//}


/**
 * Get 5w values via AJAX
 *
 */
/*function wl_5w_get_article_Ws_ajax()
{
    // Get the post Id.
    if( isset( $_REQUEST['post_id'] ) ) {
        $post_id = $_REQUEST['post_id'];
    } else {
        wp_die();
    }

    ob_clean();
    header( "Content-Type: application/json" );

    echo json_encode( wl_5w_get_all_article_Ws( $post_id ) );
    wp_die();
}
add_action( 'wp_ajax_wl_5w', 'wl_5w_get_article_Ws_ajax' );
add_action( 'wp_ajax_nopriv_wl_5w', 'wl_5w_get_article_Ws_ajax' );
 */
