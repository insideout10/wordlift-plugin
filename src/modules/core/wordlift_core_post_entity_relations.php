<?php

/**
 * Get the name of the post meta complementary to the one given as parameter.
 * See *wordlift_core_constants.php* for more details.
 *
 * @param string $meta_name Name of the meta
 *
 * @return string The complementary meta, if any, otherwise null.
 */
function wl_core_get_complementary_relation( $meta_name ) {

    $mapping = unserialize(WL_CORE_POST_ENTITY_RELATIONS_MAPPING);
    if (isset($mapping[$meta_name])) {
        return $mapping[$meta_name];
    }

    return null;
}

/**
 * Add a relation between a post/entity and another ( A --> B ).
 * The complmentary relation is also added ( B --> A ).
 *
 * @param int $subject_id The post ID.
 * @param string $relation Name of the relation.
 * @param array $object_ids Ids of the related posts.
 *
 */
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

/**
 * Update post meta with new related ids.
 * 
 * @used by *wl_core_add_relation_between_posts_and_entities*
 *
 * @param int $subject_id The post ID.
 * @param string $relation Name of the relation.
 * @param array $new_related_ids Ids of the related posts to add.
 * 
 */
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

/**
 * Get a post/entity related ids.
 *
 * @param int $subject_id The post ID.
 * @param string $relation Name of the relation.
 * 
 * @return array Ids of the related posts/entities.
 * 
 */
function wl_core_get_related_post_and_entities( $subject_id, $relation ) {
    
    // TODO: add some checks on the arguments, for example the existence of the relation.
    
    $objects_ids = get_post_meta( $subject_id, $relation ); 
    // get_post_meta returns an array in any case, so no need to check
    
    return $objects_ids;
}

/**
 * Get the IDs of posts related to the specified post.
 *
 * @param int $post_id The post ID.
 * @param string $field_name Name of the meta
 *
 * @return array An array of posts related to the one specified.
 */
function wl_get_related_post_ids( $post_id, $field_name = WL_CUSTOM_FIELD_RELATED_POST ) {

    return wl_core_get_related_post_and_entities( $post_id, $field_name );
}

/**
 * Set the related posts IDs for the specified post ID.
 *
 * @param int $post_id A post ID.
 * @param int|array $new_post_ids An array of related post IDs.
 * @param string $field_name Name of the meta
 */
function wl_add_related_posts($post_id, $new_post_ids, $field_name = WL_CUSTOM_FIELD_RELATED_POST) {

    wl_core_add_relation_between_posts_and_entities($post_id, $field_name, $new_post_ids);
}

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
function wl_get_referencing_posts($entity_id, $field_name = WL_CUSTOM_FIELD_IS_REFERENCED_BY) {
    
    $post_ids = wl_core_get_related_post_and_entities( $entity_id, $field_name );
    
    $posts = array();
    foreach( $post_ids as $post_id ) {
        $posts[] = get_post( $post_id );
    }
    
    return $posts;
}

/**
 * Get the posts ids that reference the specified entity.
 *
 * @uses    wl_core_get_related_post_and_entities to get entities related to posts.
 *
 * @param int $entity_id The post ID of the entity.
 * @param string $field_name Name of the meta (used for the 4W)
 *
 * @return array An array of posts.
 */
function wl_get_referencing_posts_ids($entity_id, $field_name = WL_CUSTOM_FIELD_IS_REFERENCED_BY) {
    
    return wl_core_get_related_post_and_entities( $entity_id, $field_name );
}

/**
 * Set the related entity posts IDs for the specified post ID.
 *
 * @param int $post_id A post ID.
 * @param int|array $new_entity_post_ids An array of related entity post IDs.
 * @param string $field_name Name of the meta (used for the 4W)
 */
function wl_add_referenced_entities( $post_id, $new_entity_post_ids, $field_name = WL_CUSTOM_FIELD_REFERENCED_ENTITY ) {
    
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
function wl_get_referenced_entity_ids($post_id, $field_name = WL_CUSTOM_FIELD_REFERENCED_ENTITY) {

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
        WL_CUSTOM_FIELD_IS_WHAT_FOR_POSTS => wl_get_referencing_posts_ids($entity_id, WL_CUSTOM_FIELD_IS_WHAT_FOR_POSTS),
        WL_CUSTOM_FIELD_IS_WHERE_FOR_POSTS => wl_get_referencing_posts_ids($entity_id, WL_CUSTOM_FIELD_IS_WHERE_FOR_POSTS),
        WL_CUSTOM_FIELD_IS_WHEN_FOR_POSTS => wl_get_referencing_posts_ids($entity_id, WL_CUSTOM_FIELD_IS_WHEN_FOR_POSTS),
        WL_CUSTOM_FIELD_IS_WHO_FOR_POSTS => wl_get_referencing_posts_ids($entity_id, WL_CUSTOM_FIELD_IS_WHO_FOR_POSTS)
    );
}