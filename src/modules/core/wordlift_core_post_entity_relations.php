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
 * Reset relations between a post/entity and another ( A --> B ).
 * The complmentary relation is reset too ( B --> A ).
 *
 * @param int $subject_id The post ID.
 * @param string $relation Name of the relation.
 *
 */
function wl_core_reset_relation_between_posts_and_entities( $subject_id, $relation ) {
    
    // Return if parameters are not set
    if( empty( $subject_id ) || empty( $relation ) ) {
        return;
    }
    
    // Get related
    $related = wl_core_get_related_post_and_entities( $subject_id, $relation );
    
    // Reset all related
    delete_post_meta( $subject_id, $relation );
    
    // Get the complementary relation, if exists
    $inverseRelation = wl_core_get_complementary_relation( $relation );
    // Add the complementary relation: B1 --> A, B2 --> A, ... , BN --> A
    if ( !is_null( $inverseRelation ) && !empty( $inverseRelation ) ) {           
        foreach( $related as $rel_id ) {
            wl_write_log("Delete relation $inverseRelation from $rel_id to $subject_id");
            delete_post_meta( (int) $rel_id, $inverseRelation, $subject_id );
        }
    }
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

    // Get the complementary relation, if exists
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
 * Get the IDs of the entities related to the specified entity.
 *
 * @param int $entity_id The entity ID.
 * @param string $field_name Name of the meta
 *
 * @return array An array of entity IDs related to the one specified.
 */
function wl_get_related_entities( $entity_id, $field_name = WL_CUSTOM_FIELD_RELATED_ENTITIES ) {

    return wl_core_get_related_post_and_entities( $entity_id, $field_name );
}

/**
 * Add the related entity IDs for the specified entity ID.
 *
 * @param int $entity_id An entity ID.
 * @param int|array $new_entities_ids An array of related entity IDs.
 * @param string $field_name Name of the meta
 */
function wl_add_related_entities($entity_id, $new_entities_ids, $field_name = WL_CUSTOM_FIELD_RELATED_ENTITIES) {
    
    // TODO: check that only entities are passed (no posts)
    
    wl_core_add_relation_between_posts_and_entities( $entity_id, $field_name, $new_entities_ids );
}

/**
 * Set the related entity IDs for the specified entity ID.
 *
 * @param int $entity_id An entity ID.
 * @param int|array $new_entities_ids An array of related entity IDs.
 * @param string $field_name Name of the meta
 */
function wl_set_related_entities( $entity_id, $new_entities_ids, $field_name = WL_CUSTOM_FIELD_REFERENCED_ENTITIES ) {
    
    wl_core_reset_relation_between_posts_and_entities( $entity_id, $field_name );
    wl_add_related_entities( $entity_id, $new_entities_ids, $field_name );
}

/**
 * Get the post ids that reference the specified entity.
 *
 * @param int $entity_id The entity ID.
 * @param string $field_name Name of the meta (used for the 4W)
 *
 * @return array An array of post IDs.
 */
function wl_get_referencing_posts( $entity_id, $field_name = WL_CUSTOM_FIELD_IS_REFERENCED_BY_POSTS) {
    
    return wl_core_get_related_post_and_entities( $entity_id, $field_name );
}

/**
 * Set the referenced entity IDs for the specified post ID.
 *
 * @param int $post_id A post ID.
 * @param int|array $new_entity_ids An array of referenced entity IDs.
 * @param string $field_name Name of the meta (used for the 4W)
 */
function wl_set_referenced_entities( $post_id, $new_entity_ids, $field_name = WL_CUSTOM_FIELD_REFERENCED_ENTITIES ) {
    
    wl_core_reset_relation_between_posts_and_entities( $post_id, $field_name );
    wl_add_referenced_entities( $post_id, $new_entity_ids, $field_name );
}

/**
 * Add the referenced entity IDs for the specified post ID.
 *
 * @param int $post_id A post ID.
 * @param int|array $new_entity_ids An array of referenced entity IDs.
 * @param string $field_name Name of the meta (used for the 4W)
 */
function wl_add_referenced_entities( $post_id, $new_entity_ids, $field_name = WL_CUSTOM_FIELD_REFERENCED_ENTITIES ) {
    
    wl_core_add_relation_between_posts_and_entities( $post_id, $field_name, $new_entity_ids );
}

/**
 * Get the referenced entity IDs for the specified post ID.
 *
 * @param int $post_id A post ID.
 * @param string $field_name Name of the meta (used for the 4W)
 */
function wl_get_referenced_entities( $post_id, $field_name = WL_CUSTOM_FIELD_REFERENCED_ENTITIES ) {

    return wl_core_get_related_post_and_entities( $post_id, $field_name );
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
        WL_CUSTOM_FIELD_WHAT_ENTITIES => wl_get_referenced_entities($post_id, WL_CUSTOM_FIELD_WHAT_ENTITIES),
        WL_CUSTOM_FIELD_WHERE_ENTITIES => wl_get_referenced_entities($post_id, WL_CUSTOM_FIELD_WHERE_ENTITIES),
        WL_CUSTOM_FIELD_WHEN_ENTITIES => wl_get_referenced_entities($post_id, WL_CUSTOM_FIELD_WHEN_ENTITIES),
        WL_CUSTOM_FIELD_WHO_ENTITIES => wl_get_referenced_entities($post_id, WL_CUSTOM_FIELD_WHO_ENTITIES)
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
        WL_CUSTOM_FIELD_IS_WHAT_FOR_POSTS => wl_get_referencing_posts($entity_id, WL_CUSTOM_FIELD_IS_WHAT_FOR_POSTS),
        WL_CUSTOM_FIELD_IS_WHERE_FOR_POSTS => wl_get_referencing_posts($entity_id, WL_CUSTOM_FIELD_IS_WHERE_FOR_POSTS),
        WL_CUSTOM_FIELD_IS_WHEN_FOR_POSTS => wl_get_referencing_posts($entity_id, WL_CUSTOM_FIELD_IS_WHEN_FOR_POSTS),
        WL_CUSTOM_FIELD_IS_WHO_FOR_POSTS => wl_get_referencing_posts($entity_id, WL_CUSTOM_FIELD_IS_WHO_FOR_POSTS)
    );
}