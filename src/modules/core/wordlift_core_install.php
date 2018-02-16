<?php
/**
 * Provide WordLift's install functions.
 *
 * @since      3.0.0
 * @package    Wordlift
 * @subpackage Wordlfit/modules/core
 */

/**
 * Install known types in WordPress.
 */
function wl_core_install_entity_type_data( $db_version ) {

	// Bails if the db version has been already set.
	if ( ! empty( $db_version ) ) {
		return;
	}

	Wordlift_Log_Service::get_instance()->debug( 'Installing Entity Type data...' );

	// Set the taxonomy data.
	// Note: parent types must be defined before child types.
	$terms = array(
		'thing'         => array(
			'label'       => 'Thing',
			'description' => 'A generic thing (something that doesn\'t fit in the previous definitions.',
		),
		'creative-work' => array(
			'label'       => 'CreativeWork',
			'description' => 'A creative work (or a Music Album).',
		),
		'event'         => array(
			'label'       => 'Event',
			'description' => 'An event.',
		),
		'organization'  => array(
			'label'       => 'Organization',
			'description' => 'An organization, including a government or a newspaper.',
		),
		'person'        => array(
			'label'       => 'Person',
			'description' => 'A person (or a music artist).',
		),
		'place'         => array(
			'label'       => 'Place',
			'description' => 'A place.',
		),
		'localbusiness' => array(
			'label'       => 'LocalBusiness',
			'description' => 'A local business.',
		),
	);

	foreach ( $terms as $slug => $term ) {

		// Create the term if it does not exist, then get its ID
		$term_id = term_exists( $slug, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );

		if ( 0 == $term_id || is_null( $term_id ) ) {
			$result = wp_insert_term( $slug, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );
		} else {
			$term_id = $term_id['term_id'];
			$result  = get_term( $term_id, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME, ARRAY_A );
		}

		// Check for errors.
		if ( is_wp_error( $result ) ) {
			wl_write_log( 'wl_install_entity_type_data [ ' . $result->get_error_message() . ' ]' );
			continue;
		}

		// Check if 'parent' corresponds to an actual term and get its ID.
		if ( ! isset( $term['parents'] ) ) {
			$term['parents'] = array();
		}

		$parent_ids = array();
		foreach ( $term['parents'] as $parent_slug ) {
			$parent_id    = get_term_by( 'slug', $parent_slug, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );
			$parent_ids[] = intval( $parent_id->term_id );  // Note: int casting is suggested by Codex: http://codex.wordpress.org/Function_Reference/get_term_by
		}

		// Define a parent in the WP taxonomy style (not important for WL)
		if ( empty( $parent_ids ) ) {
			// No parent
			$parent_id = 0;
		} else {
			// Get first parent
			$parent_id = $parent_ids[0];
		}

		// Update term with description, slug and parent
		$term = wp_update_term( $result['term_id'], Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME, array(
			'name'        => $term['label'],
			'slug'        => $slug,
			'description' => $term['description'],
			// We give to WP taxonomy just one parent. TODO: see if can give more than one
			'parent'      => $parent_id,
		) );

		Wordlift_Log_Service::get_instance()->trace( "Entity Type $slug installed with ID {$term['term_id']}." );

	}

	Wordlift_Log_Service::get_instance()->debug( 'Entity Type data installed.' );

}

/**
 * Install known types in WordPress.
 */
function wl_core_install_create_relation_instance_table( $db_version ) {

	// Bails if the db version has been already set.
	if ( ! empty( $db_version ) ) {
		return;
	}

	global $wpdb;

	$table_name      = $wpdb->prefix . WL_DB_RELATION_INSTANCES_TABLE_NAME;
	$charset_collate = $wpdb->get_charset_collate();

	// Sql statement for the relation instances custom table
	$sql = <<<EOF
		CREATE TABLE $table_name (
				id int(11) NOT NULL AUTO_INCREMENT,
				subject_id int(11) NOT NULL,
				predicate char(10) NOT NULL,
				object_id int(11) NOT NULL,
				UNIQUE KEY id (id),
				KEY subject_id_index (subject_id),
				KEY object_id_index (object_id)
		) $charset_collate;
EOF;

	// @see: https://codex.wordpress.org/Creating_Tables_with_Plugins
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	$results = dbDelta( $sql );

	wl_write_log( $results );

}

/**
 * Upgrade the DB structure to the one expected by the 3.10 release.
 *
 * Flatten the hierarchy of the entity type taxonomy terms.
 *
 * @since 3.10.0
 */
function wl_core_upgrade_db__1_0_0__3_10_0( $db_version ) {

	// If the DB version is less than 3.10, than flatten the taxonomy.
	if ( version_compare( $db_version, '3.10', '>' ) ) {
		return;
	}

	$term_slugs = array(
		'thing',
		'creative-work',
		'event',
		'organization',
		'person',
		'place',
		'localbusiness',
	);

	foreach ( $term_slugs as $slug ) {

		$term = get_term_by( 'slug', $slug, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );

		// Set the term's parent to 0.
		if ( $term ) {
			wp_update_term( $term->term_id, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME, array(
				'parent' => 0,
			) );
		}
	}

}

/**
 * Upgrade the DB structure to the one expected by the 3.12 release.
 *
 * Flush rewrite rules to support immediate integration with automattic's
 * AMP plugin.
 *
 * @since 3.12.0
 */
function wl_core_upgrade_db__3_10_0__3_12_0( $db_version ) {

	// Bail if the version id lower than 3.12.
	if ( version_compare( $db_version, '3.12', '<' ) ) {
		return;
	}
	/*
	 * As this upgrade functionality runs on the init hook, and the AMP plugin
	 * initialization does the same, avoid possible race conditions by
	 * deferring the actual flush to a later hook.
	 */
	add_action( 'wp_loaded', function () {
		flush_rewrite_rules();
	} );
}

/**
 * Upgrade the DB structure to the one expected by the 3.14 release.
 *
 * Add Recipe entity.
 *
 * @since 3.14.0
 */
function wl_core_upgrade_db__3_12_0__3_14_0( $db_version ) {

	// Bail if the version exists and it's lower than 3.15.
	if (
		! empty( $db_version ) && // We need the `recipe` entity term for new installs too.
		version_compare( $db_version, '3.14', '<' )
	) {
		return;
	}

	// Check whether the `recipe` term exists.
	$recipe = get_term_by( 'slug', 'article', Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );

	// The recipe term doesn't exists, so create it.
	if ( empty( $recipe ) ) {
		$result = wp_insert_term(
			'Recipe',
			Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME,
			array(
				'slug'        => 'recipe',
				'description' => 'A Recipe.',
			)
		);
	}

	// Assign capabilities to manipulate entities to admins.
	$admins = get_role( 'administrator' );

	$admins->add_cap( 'edit_wordlift_entity' );
	$admins->add_cap( 'edit_wordlift_entities' );
	$admins->add_cap( 'edit_others_wordlift_entities' );
	$admins->add_cap( 'publish_wordlift_entities' );
	$admins->add_cap( 'read_private_wordlift_entities' );
	$admins->add_cap( 'delete_wordlift_entity' );
	$admins->add_cap( 'delete_wordlift_entities' );
	$admins->add_cap( 'delete_others_wordlift_entities' );
	$admins->add_cap( 'delete_published_wordlift_entities' );
	$admins->add_cap( 'delete_private_wordlift_entities' );

	// Assign capabilities to manipulate entities to editors.
	$editors = get_role( 'editor' );

	$editors->add_cap( 'edit_wordlift_entity' );
	$editors->add_cap( 'edit_wordlift_entities' );
	$editors->add_cap( 'edit_others_wordlift_entities' );
	$editors->add_cap( 'publish_wordlift_entities' );
	$editors->add_cap( 'read_private_wordlift_entities' );
	$editors->add_cap( 'delete_wordlift_entity' );
	$editors->add_cap( 'delete_wordlift_entities' );
	$editors->add_cap( 'delete_others_wordlift_entities' );
	$editors->add_cap( 'delete_published_wordlift_entities' );
	$editors->add_cap( 'delete_private_wordlift_entities' );
}

/**
 * Upgrade the DB structure to the one expected by the 3.15 release.
 *
 * Add explicit Article entity.
 *
 * @since 3.15.0
 */
function wl_core_upgrade_db__3_14_0__3_15_0( $db_version ) {

	// Bail if the version exists and it's lower than 3.15.
	if (
		! empty( $db_version ) && // We need the `article` entity term for new installs too.
		version_compare( $db_version, '3.15', '<' )
	) {
		return;
	}

	// Check whether the `article` term exists.
	$article = get_term_by( 'slug', 'article', Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );

	// The `article` term doesn't exists, so create it.
	if ( empty( $article ) ) {
		wp_insert_term(
			'Article',
			Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME,
			array(
				'slug'        => 'article',
				'description' => 'An Article.',
			)
		);
	}

	// The following is disabled because on large installations it may slow the
	// web site.
	// See: https://github.com/insideout10/wordlift-plugin/commit/fa3cfe296c60828b434897f12a01ead021045fca#diff-b6b016ed02839e76bcfe4a5491f3aa2eR280
}


// Check db status on automated plugins updates
function wl_core_update_db_check() {

	// Get the current `wl_db_version`.
	$db_version = get_option( 'wl_db_version' );

	// Bail if the db version hasn't been updated.
	if ( $db_version === WL_DB_VERSION ) {
		return;
	}

	// Run upgrade functions.
	wl_core_install_entity_type_data( $db_version );
	wl_core_install_create_relation_instance_table( $db_version );
	wl_core_upgrade_db__1_0_0__3_10_0( $db_version );
	wl_core_upgrade_db__3_10_0__3_12_0( $db_version );
	wl_core_upgrade_db__3_12_0__3_14_0( $db_version );
	wl_core_upgrade_db__3_14_0__3_15_0( $db_version );


	// Finally bump the db version.
	update_option( 'wl_db_version', WL_DB_VERSION );

}

/**
 * Ensure that for first time installsthe dataset uri will be configured.
 *
 * @since 3.18.0
 *
 * @return void
 */
function install_wl_version_1() {
	// Update to WL install level 1.
	if ( intval( get_option( 'wl_install_version' ) ) >= 1 ) {
		return;
	}

	$log = Wordlift_Log_Service::get_logger( 'wl_core_update_db_check' );

	$log->trace( 'Installing version 1...' );

	// Get the configuration service and load the key.
	$configuration_service = Wordlift_Configuration_Service::get_instance();
	$key                   = $configuration_service->get_key();

	// If the key is not empty then set the dataset URI while sending
	// the site URL.
	if ( ! empty( $key ) ) {
		$log->info( 'Updating the remote dataset URI...' );

		$configuration_service->get_remote_dataset_uri( $key );
	}

	// Check if the dataset key has been stored.
	$dataset_uri = $configuration_service->get_dataset_uri();

	// If the dataset URI is empty, do not set the install version.
	if ( empty( $dataset_uri ) ) {
		$log->info( 'Setting dataset URI filed: the dataset URI is empty.' );

		return;
	}

	// Update the `wl_install_version` option to prevent future dataset setup.
	update_option( 'wl_install_version', 1 );

	$log->info( 'Version 1 installed.' );
}

add_action( 'init', 'install_wl_version_1', 11 );
add_action( 'init', 'wl_core_update_db_check', 11 );
