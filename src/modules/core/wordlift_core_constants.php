<?php

define( 'WL_DEFAULT_THUMBNAIL_PATH', dirname( dirname( plugin_dir_url( __FILE__ ) ) ) . '/public/images/missing-image-150x150.png' );
define( 'WL_DEFAULT_PATH', dirname( dirname( plugin_dir_url( __FILE__ ) ) ) . '/' );

// Database version 
define( 'WL_DB_VERSION', '1.0' );
// Custom table name
define( 'WL_DB_RELATION_INSTANCES_TABLE_NAME', 'wl_relation_instances' );

define( 'WL_WHAT_RELATION', 'what' );
define( 'WL_WHO_RELATION', 'who' );
define( 'WL_WHERE_RELATION', 'where' );
define( 'WL_WHEN_RELATION', 'when' );

define( 'WL_BLIND_ANNOTATION_CSS_CLASS', 'no-entity-page-link' );

// Mapping options / validations rules used by wl_core_get_posts to perform validation on args
// The array is serialized because array constants are only from php 5.6 on.
define( 'WL_CORE_GET_POSTS_VALIDATION_RULES', serialize( array(
	'get'            => array( 'posts', 'post_ids' ),
	'as'             => array( 'object', 'subject' ),
	'post_type'      => array( 'post', 'entity' ),
	'post_status'    => array( 'draft', 'trash', 'publish' ),
	'with_predicate' => array( WL_WHAT_RELATION, WL_WHEN_RELATION, WL_WHERE_RELATION, WL_WHO_RELATION ),
) ) );

// Classification boxes configuration for angularjs edit-post widget
// The array is serialized because array constants are only from php 5.6 on.

define( 'WL_CORE_POST_CLASSIFICATION_BOXES', serialize( array(
	array(
		'id'               => WL_WHAT_RELATION,
		'label'            => 'What',
		'registeredTypes'  => array( 'thing', 'creative-work' ),
		'selectedEntities' => array(),
	),
	array(
		'id'               => WL_WHO_RELATION,
		'label'            => 'Who',
		'registeredTypes'  => array( 'organization', 'person', 'local-business' ),
		'selectedEntities' => array(),
	),
	array(
		'id'               => WL_WHERE_RELATION,
		'label'            => 'Where',
		'registeredTypes'  => array( 'place' ),
		'selectedEntities' => array(),
	),
	array(
		'id'               => WL_WHEN_RELATION,
		'label'            => 'When',
		'registeredTypes'  => array( 'event' ),
		'selectedEntities' => array(),
	),
) ) );