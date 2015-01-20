<?php

// Post meta for the 4W in journalism (we don't have a Why)
// post metas linking a post to the entity
define('WL_CUSTOM_FIELD_WHAT_ENTITIES', 'wl_what_entities');
define('WL_CUSTOM_FIELD_WHO_ENTITIES', 'wl_who_entities');
define('WL_CUSTOM_FIELD_WHERE_ENTITIES', 'wl_where_entities');
define('WL_CUSTOM_FIELD_WHEN_ENTITIES', 'wl_when_entities');
// entity metas linking an entity to the post
define('WL_CUSTOM_FIELD_IS_WHAT_FOR_POSTS', 'wl_is_what_for_posts');
define('WL_CUSTOM_FIELD_IS_WHO_FOR_POSTS', 'wl_is_who_for_posts');
define('WL_CUSTOM_FIELD_IS_WHERE_FOR_POSTS', 'wl_is_where_for_posts');
define('WL_CUSTOM_FIELD_IS_WHEN_FOR_POSTS', 'wl_is_when_for_posts');

// The name of the custom field that stores the IDs of entities referenced by posts.
define('WL_CUSTOM_FIELD_REFERENCED_ENTITY', 'wordlift_related_entities');
// ... and viceversa.
define('WL_CUSTOM_FIELD_IS_REFERENCED_BY', 'wordlift_is_related_entity_for');

// The name of the custom field that stores the IDs of posts referenced by posts/entities
define('WL_CUSTOM_FIELD_RELATED_POST', 'wordlift_related_posts');

// Mapping between a post/entity relation and its complementary relation.
// The array is serialized because array constants are only from php 5.6 on.
define('WL_CORE_POST_ENTITY_RELATIONS_MAPPING', serialize(array(
    WL_CUSTOM_FIELD_WHAT_ENTITIES => WL_CUSTOM_FIELD_IS_WHAT_FOR_POSTS,
    WL_CUSTOM_FIELD_WHERE_ENTITIES => WL_CUSTOM_FIELD_IS_WHERE_FOR_POSTS,
    WL_CUSTOM_FIELD_WHEN_ENTITIES => WL_CUSTOM_FIELD_IS_WHEN_FOR_POSTS,
    WL_CUSTOM_FIELD_WHO_ENTITIES => WL_CUSTOM_FIELD_IS_WHO_FOR_POSTS,
    WL_CUSTOM_FIELD_REFERENCED_ENTITY => WL_CUSTOM_FIELD_IS_REFERENCED_BY,
    WL_CUSTOM_FIELD_RELATED_POST => WL_CUSTOM_FIELD_RELATED_POST,
)));

// TODO: docs
function wl_core_get_complementary_relation($meta_name) {

    $mapping = unserialize(WL_CORE_POST_ENTITY_RELATIONS_MAPPING);
    if (isset($mapping[$meta_name])) {
        return $mapping[$meta_name];
    }

    return null;
}

// TODO: docs
function wl_core_add_relation_between_posts_and_entities( $subject_id, $relation, $object_ids ) {
    
    // Return if parameters are not set
    if( empty( $subject_id ) || empty( $object_ids ) || empty( $relation ) ) {
        return;
    }
    
    // Ensure $object_ids is an array.
    if ( !is_array( $object_ids ) ) {
        $object_ids = array( $object_ids );
    }
    
    // Add relation between subject and objects: A --> B1, A-->B2, ... , A -->BN
    wl_core_merge_old_related_with_new( $subject_id, $relation, $object_ids );

    // Get the complmeentary relation, if exists
    $inverseRelation = wl_core_get_complementary_relation( $relation );

    // Add the complementary relation: B1 --> A, B2 --> A, ... , BN --> A
    if ( !is_null( $inverseRelation ) && !empty( $inverseRelation ) ) {
        foreach ( $object_ids as $object_id ) {
            // Get the existing complementary meta values and merge them together.
            wl_core_merge_old_related_with_new( $object_id, $inverseRelation, $subject_id );
        }
    }
}

// TODO docs
function wl_core_merge_old_related_with_new( $subject_id, $relation, $new_related_ids ) {
    
    // Ensure the argument is an array.
    if ( !is_array( $new_related_ids ) ) {
        $new_related_ids = array( $new_related_ids );
    }    

    // Retrieve related already present in db.
    $related = wl_core_get_related_post_and_entities( $subject_id, $relation );

    // Merge old related with new.
    if( empty( $related ) ) {
        $related = $new_related_ids;
    } else {
        $related = array_merge( $related, $new_related_ids );
    }
    
    // Take away duplicates
    $related = array_unique( $related );
    
    // Take away stored meta values
    delete_post_meta( $subject_id, $relation );
    
    // Add new values (combined with old ones)
    foreach( $related as $rel_id ) {
        // Add meta value (convert to int if the id is a string)
        add_post_meta( $subject_id, $relation, (int)$rel_id );
    }
}

// TODO docs
function wl_core_get_related_post_and_entities( $subject_id, $relation ) {
    
    // TODO: add some checks on the arguments
    
    $objects_ids = get_post_meta( $subject_id, $relation ); 
    // get_post_meta returns an array in any case, so no need to check
    
    return $objects_ids;
}

/**
 * Get the IDs of posts related to the specified post.
 *
 * @param int $post_id The post ID.
 * @param string $field_name Name of the meta (used for the 4W)
 *
 * @return array An array of posts related to the one specified.
 */
function wl_get_related_post_ids($post_id, $field_name=WL_CUSTOM_FIELD_RELATED_POST) {

    return wl_core_get_related_post_and_entities($post_id, WL_CUSTOM_FIELD_RELATED_POST);
}

/**
 * Set the related posts IDs for the specified post ID.
 *
 * @param int $post_id A post ID.
 * @param array $related_posts An array of related post IDs.
 * @param string $field_name Name of the meta (used for the 4W)
 */
/*function wl_set_related_posts($post_id, $related_posts, $field_name = WL_CUSTOM_FIELD_RELATED_POST) {

    wl_write_log("wl_set_related_posts [ post id :: $post_id ][ related posts :: " . join(',', $related_posts) . " ]");

    delete_post_meta($post_id, $field_name);
    add_post_meta($post_id, $field_name, $related_posts);
}*/

/**
 * Set the related posts IDs for the specified post ID.
 *
 * @param int $post_id A post ID.
 * @param int|array $new_post_ids An array of related post IDs.
 * @param string $field_name Name of the meta (used for the 4W)
 */
function wl_add_related_posts($post_id, $new_post_ids, $field_name = WL_CUSTOM_FIELD_RELATED_POST) {

    wl_core_add_relation_between_posts_and_entities($post_id, WL_CUSTOM_FIELD_RELATED_POST, $new_post_ids);
}

/**
 * Set the related entity posts IDs for the specified post ID.
 *
 * @param int $post_id A post ID.
 * @param array $related_entities An array of related entity post IDs.
 * @param string $field_name Name of the meta (used for the 4W)
 */
/*function wl_set_referenced_entities($post_id, $related_entities, $field_name = WL_CUSTOM_FIELD_REFERENCED_ENTITY) {

    wl_write_log("wl_set_referenced_entities [ post id :: $post_id ][ related entities :: " . var_export($related_entities, true) . " ]");

    delete_post_meta($post_id, $field_name);

    foreach ($related_entities as $entity_post_id) {
        add_post_meta($post_id, $field_name, $entity_post_id);
    }
}*/

/**
 * Get the posts that reference the specified entity.
 *
 * @uses    wl_core_get_related_post_and_entities to get entities related to posts.
 *
 * @param int $entity_id The post ID of the entity.
 * @param string $field_name Name of the meta (used for the 4W)
 *
 * @return array An array of posts.
 */
function wl_get_referencing_posts($entity_id, $field_name=WL_CUSTOM_FIELD_IS_REFERENCED_BY) {
    
    $post_ids = wl_core_get_related_post_and_entities( $entity_id, $field_name );
    
    $posts = array();
    foreach( $post_ids as $post_id ) {
        $posts[] = get_post( $post_id );
    }
    
    return $posts;
}

/**
 * Set the related entity posts IDs for the specified post ID.
 *
 * @param int $post_id A post ID.
 * @param int|array $new_entity_post_ids An array of related entity post IDs.
 * @param string $field_name Name of the meta (used for the 4W)
 */
function wl_add_referenced_entities( $post_id, $new_entity_post_ids, $field_name=WL_CUSTOM_FIELD_REFERENCED_ENTITY ) {
    
    wl_core_add_relation_between_posts_and_entities( $post_id, $field_name, $new_entity_post_ids );
}

/**
 * Get the IDs of entities related to the specified post.
 *
 * @param int $post_id The post ID.
 * @param string $field_name Name of the meta (used for the 4W)
 *
 * @return array An array of posts related to the one specified.
 */
function wl_get_referenced_entity_ids($post_id, $field_name=WL_CUSTOM_FIELD_REFERENCED_ENTITY) {

    return wl_core_get_related_post_and_entities( $post_id, $field_name );
}

/**
 * Add related post IDs to the specified post ID, automatically choosing whether to add the related to entities or to
 * posts.
 *
 * @param int $post_id The post ID.
 * @param int|array $related_id A related post/entity ID or an array of posts/entities.
 */
function wl_add_related($post_id, $related_id) {

    // Ensure we're dealing with an array.
    $related_id_array = ( is_array($related_id) ? $related_id : array($related_id) );

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

    // TODO: check this, we're adding related posts to a post.
    if (0 < count($related_posts)) {
        wl_add_related_posts($post_id, $related_posts);
    }
}

/**
 * Get the IDs of 4W related to the specified post.
 *
 * @param int $post_id The post ID.
 *
 * @return array An array containing the 4W entitities ids. In case of non existent post, an empty array is returned.
 */
function wl_get_post_4w_entities($post_id) {

    // Return if post does not exists
    if (get_post_status($post_id) == False) {
        return array();
    }

    return array(
        WL_CUSTOM_FIELD_WHAT_ENTITIES => wl_get_referenced_entity_ids($post_id, WL_CUSTOM_FIELD_WHAT_ENTITIES),
        WL_CUSTOM_FIELD_WHERE_ENTITIES => wl_get_referenced_entity_ids($post_id, WL_CUSTOM_FIELD_WHERE_ENTITIES),
        WL_CUSTOM_FIELD_WHEN_ENTITIES => wl_get_referenced_entity_ids($post_id, WL_CUSTOM_FIELD_WHEN_ENTITIES),
        WL_CUSTOM_FIELD_WHO_ENTITIES => wl_get_referenced_entity_ids($post_id, WL_CUSTOM_FIELD_WHO_ENTITIES)
    );
}

/**
 * Get the IDs of posts for which an entity is a 4W.
 *
 * @param int $entity_id The entity ID.
 *
 * @return array An array containing the posts ids. In case of non existent entity, an empty array is returned.
 */
function wl_get_entity_is_4w_for_posts($entity_id) {

    // Return if entity does not exists
    if (get_post_status($entity_id) == False) {
        return array();
    }

    return array(
        WL_CUSTOM_FIELD_IS_WHAT_FOR_POSTS => wl_get_related_post_ids($entity_id, WL_CUSTOM_FIELD_IS_WHAT_FOR_POSTS),
        WL_CUSTOM_FIELD_IS_WHERE_FOR_POSTS => wl_get_related_post_ids($entity_id, WL_CUSTOM_FIELD_IS_WHERE_FOR_POSTS),
        WL_CUSTOM_FIELD_IS_WHEN_FOR_POSTS => wl_get_related_post_ids($entity_id, WL_CUSTOM_FIELD_IS_WHEN_FOR_POSTS),
        WL_CUSTOM_FIELD_IS_WHO_FOR_POSTS => wl_get_related_post_ids($entity_id, WL_CUSTOM_FIELD_IS_WHO_FOR_POSTS)
    );
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
