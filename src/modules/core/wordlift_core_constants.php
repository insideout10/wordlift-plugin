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