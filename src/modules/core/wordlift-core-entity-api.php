<?php
/**
 * This file provides functions to get entities.
 *
 * @since 3.0.0
 */

/**
 * Get a list of entities with the specified title.
 *
 * @param      $title string The title to look for.
 *
 * @param bool                               $autocomplete
 * @param bool                               $include_alias
 * @param int                                $limit Limit the results
 *
 * @return array An array of WP_Post instances.
 * @since 3.0.0
 */
function wl_entity_get_by_title( $title, $autocomplete = false, $include_alias = true, $limit = false, $schema_types = array() ) {

	global $wpdb;

	$schema_type_query = '';

	if ( $schema_types ) {
		$schema_type_query = ' AND t.name IN (' . join(
			',',
			array_map(
				function ( $schema_type ) {
					return "'" . esc_sql( $schema_type ) . "'";
				},
				$schema_types
			)
		) . ')';
	}

	// Search by substring
	if ( $autocomplete ) {
		$title = '%' . $title . '%';
	}

	// The title is a LIKE query.
	$query = 'SELECT DISTINCT p.ID AS id, p.post_title AS title, t.name AS schema_type_name, t.slug AS type_slug'
			 . " FROM $wpdb->posts p, $wpdb->term_taxonomy tt, $wpdb->term_relationships tr, $wpdb->terms t"
			 . '  WHERE p.post_title LIKE %s'
			 . '   AND t.term_id = tt.term_id'
			 . '   AND tt.taxonomy = %s'
			 . '   AND tt.term_taxonomy_id = tr.term_taxonomy_id'
			 . '   AND tr.object_id = p.ID'
			 // Ensure we don't load entities from the trash, see https://github.com/insideout10/wordlift-plugin/issues/278.
			 . "   AND p.post_status != 'trash'"
			 . $schema_type_query;

	$params = array(
		$title,
		Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
	);

	if ( $include_alias ) {

		$query .= ' UNION'
				  . "  SELECT DISTINCT p.ID AS id, CONCAT( m.meta_value, ' (', p.post_title, ')' ) AS title, t.name AS schema_type_name, t.slug AS type_slug"
				  . "  FROM $wpdb->posts p, $wpdb->term_taxonomy tt, $wpdb->term_relationships tr, $wpdb->terms t, $wpdb->postmeta m"
				  . '   WHERE m.meta_key = %s AND m.meta_value LIKE %s'
				  . '    AND m.post_id = p.ID'
				  . '    AND t.term_id = tt.term_id'
				  . '    AND tt.taxonomy = %s'
				  . '    AND tt.term_taxonomy_id = tr.term_taxonomy_id'
				  . '    AND tr.object_id = p.ID'
				  // Ensure we don't load entities from the trash, see https://github.com/insideout10/wordlift-plugin/issues/278.
				  . "    AND p.post_status != 'trash'"
				  . $schema_type_query;

		$params = array_merge(
			$params,
			array(
				Wordlift_Entity_Service::ALTERNATIVE_LABEL_META_KEY,
				$title,
				Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
			)
		);

	}

	if ( $limit ) {
		$query   .= ' LIMIT %d';
		$params[] = $limit;
	}

	return $wpdb->get_results(
		$wpdb->prepare(
			$query, // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$params
		)
	);
}

/**
 * Execute the {@link wl_entity_get_by_title} function via AJAX.
 *
 * @since 3.0.0
 */
function wl_entity_ajax_get_by_title() {

	// `wl_entity_metaboxes_utilities.js` still uses `GET`.
	//
	// See https://github.com/insideout10/wordlift-plugin/issues/438.
	// Get the title to search.
	if ( empty( $_POST['title'] ) && empty( $_GET['title'] ) ) {  //phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.NonceVerification.Missing
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		@ob_clean();
		wp_send_json_error( 'The title parameter is required.' );
	}

	// `wl_entity_metaboxes_utilities.js` still uses `GET`.
	//
	// See https://github.com/insideout10/wordlift-plugin/issues/438.
	$title = sanitize_text_field( wp_unslash( $_POST['title'] ? $_POST['title'] : $_GET['title'] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.NonceVerification.Missing

	// Are we searching for a specific title or for a containing title?
	$autocomplete = isset( $_GET['autocomplete'] ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended

	// Are we searching also for the aliases?
	$include_alias = isset( $_GET['alias'] ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended

	// Get the edit link.
	$post_type_object = get_post_type_object( Wordlift_Entity_Service::TYPE_NAME );
	$edit_link        = $post_type_object->_edit_link . '&action=edit';

	// Prepare the response with the edit link.
	$response = array(
		'edit_link' => $edit_link,
		'results'   => wl_entity_get_by_title( $title, $autocomplete, $include_alias ),
	);

	// Clean any buffer.
	// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	@ob_clean();

	// Send the success response.
	wp_send_json_success( $response );

}

add_action( 'wp_ajax_entity_by_title', 'wl_entity_ajax_get_by_title' );
