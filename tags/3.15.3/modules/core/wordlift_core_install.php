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
function wl_core_install_entity_type_data() {

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
function wl_core_install_create_relation_instance_table() {

	global $wpdb;
	// global $wl_db_version;
	$installed_version = get_option( 'wl_db_version' );

	if ( WL_DB_VERSION != $installed_version ) {
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

		update_option( 'wl_db_version', WL_DB_VERSION );
	}
}

/**
 * Upgrade the DB structure to the one expected by the 1.0 release
 *
 * @since 3.10.0
 *
 */
function wl_core_upgrade_db_to_1_0() {

	if ( ! get_option( 'wl_db_version' ) ) {
		wl_core_install_create_relation_instance_table();
	}

}

/**
 * Upgrade the DB structure to the one expected by the 3.10 release.
 *
 * Flatten the hierarchy of the entity type taxonomy terms.
 *
 * @since 3.10.0
 */
function wl_core_upgrade_db_1_0_to_3_10() {

	// If the DB version is less than 3.10, than flatten the txonomy.
	if ( version_compare( get_option( 'wl_db_version' ), '3.9', '<=' ) ) {

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

}

/**
 * Upgrade the DB structure to the one expected by the 3.12 release.
 *
 * Flush rewrite rules to support immediate integration with automattic's
 * AMP plugin.
 *
 * @since 3.12.0
 */
function wl_core_upgrade_db_3_10_3_12() {
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
function wl_core_upgrade_db_3_12_3_14() {
	$result = wp_insert_term(
		'Recipe',
		Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME,
		array(
			'slug'        => 'recipe',
			'description' => 'A Recipe.',
		)
	);

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
function wl_core_upgrade_db_3_14_3_15() {

	if ( version_compare( get_option( 'wl_db_version' ), '3.15', '<=' ) ) {
		$article = get_term_by( 'slug', 'article', Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );
		
		if ( ! $article ) {
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
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/663.
		//
		//		// An sql that will assign the article term to all posts and pages
		//		$wpdb->query( $wpdb->prepare(
		//			"
		//			INSERT INTO $wpdb->term_relationships( object_id, term_taxonomy_id )
		//			SELECT id, %d
		//			FROM $wpdb->posts
		//			WHERE post_type IN ( 'post', 'page' )
		//				AND id NOT IN
		//			(
		//				SELECT DISTINCT tr.object_id
		//				FROM $wpdb->term_relationships tr
		//				INNER JOIN $wpdb->term_taxonomy tt
		//					ON tr.term_taxonomy_id = tt.term_taxonomy_id
		//						AND tt.taxonomy = 'wl_entity_type'
		//			)
		//			",
		//			$article_id
		//		) );
	}

}

// Check db status on automated plugins updates
function wl_core_update_db_check() {

	// Ensure the custom type and the taxonomy are registered.
	Wordlift_Entity_Post_Type_Service::get_instance()->register();

	// Commenting this out because this function is already called by init
	// with priority 0 at wordlift_entity_type.php.
	wl_entity_type_taxonomy_register();

	// Ensure the custom taxonomy for dbpedia topics is registered
	Wordlift_Topic_Taxonomy_Service::get_instance()->init();

	if ( get_option( 'wl_db_version' ) !== WL_DB_VERSION ) {
		wl_core_install_entity_type_data();
		wl_core_upgrade_db_to_1_0();
		wl_core_upgrade_db_1_0_to_3_10();
		wl_core_upgrade_db_3_10_3_12();
		wl_core_upgrade_db_3_12_3_14();
		wl_core_upgrade_db_3_14_3_15();
		update_option( 'wl_db_version', WL_DB_VERSION );
	}

}

add_action( 'init', 'wl_core_update_db_check', 11 ); // need taxonomies and post type to be defined first
