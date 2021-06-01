<?php
/**
 * This file contains methods related to entities.
 */

/**
 * Find entity posts by the entity URIs. Entity as searched by their entity URI or same as.
 *
 * @param array $uris A collection of entity URIs.
 *
 * @return array A WP_Post instance or null if not found.
 */
function wl_get_entity_post_ids_by_uris( $uris ) {

	if ( empty( $uris ) ) {
		return array();
	}

	$query = new WP_Query( array(
			'fields'      => 'ids',
			'post_status' => 'any',
			'post_type'   => Wordlift_Entity_Service::valid_entity_post_types(),
			'meta_query'  => array(
				'relation' => 'OR',
				array(
					'key'     => Wordlift_Schema_Service::FIELD_SAME_AS,
					'value'   => $uris,
					'compare' => 'IN',
				),
				array(
					'key'     => 'entity_url',
					'value'   => $uris,
					'compare' => 'IN',
				),
			),
		)
	);

	// Get the matching entity posts.
	$posts = $query->get_posts();

	// Return the array
	return $posts;
}

/**
 * Build the entity URI given the entity's post.
 *
 * @param int $post_id The post ID
 *
 * @return string The URI of the entity
 * @uses wl_sanitize_uri_path() to sanitize the post title.
 * @uses wl_configuration_get_redlink_dataset_uri() to get the dataset base URI.
 *
 */
function wl_build_entity_uri( $post_id ) {

	// Get the post.
	$post = get_post( $post_id );

	if ( null === $post ) {
		wl_write_log( "wl_build_entity_uri : error [ post ID :: $post_id ][ post :: null ]" );

		return null;
	}

	// For installations not connected to the Cloud - i.e. add_filter( 'wl_features__enable__dataset', '__return_false')
	// - we build the dataset URI base on the permalink.
	if ( ! apply_filters( 'wl_features__enable__dataset', true ) ) {
		return sprintf( '%s#%s', get_permalink( $post_id ), get_post_type( $post_id ) );
	}

	// Create an ID given the title.
	$entity_slug = wl_sanitize_uri_path( $post->post_title );
	// If the entity slug is empty, i.e. there's no title, use the post ID as path.
	if ( empty( $entity_slug ) ) {
		return sprintf( '%s/%s/%s',
			wl_configuration_get_redlink_dataset_uri(),
			$post->post_type,
			"id/$post->ID"
		);
	}

	return Wordlift_Uri_Service::get_instance()->build_uri(
		$entity_slug,
		$post->post_type );

}

/**
 * Get the entity URI of the provided post.
 *
 * @param int $post_id The post ID.
 *
 * @return string|null The URI of the entity or null if not configured.
 * @uses       wl_set_entity_uri() to set a newly create URI.
 *
 * @deprecated use Wordlift_Entity_Service::get_instance()->get_uri( $post_id )
 *
 * @uses       wl_build_entity_uri() to create a new URI if the entity doesn't have an URI yet.
 */
function wl_get_entity_uri( $post_id ) {

	return Wordlift_Entity_Service::get_instance()->get_uri( $post_id );
}

/**
 * Save the entity URI for the provided post ID.
 *
 * @param int $post_id The post ID.
 * @param string $uri The post URI.
 *
 * @return bool True if successful, otherwise false.
 */
function wl_set_entity_uri( $post_id, $uri ) {

	// wl_write_log( "wl_set_entity_uri [ post id :: $post_id ][ uri :: $uri ]" );

	return update_post_meta( $post_id, WL_ENTITY_URL_META_NAME, $uri );
}


/**
 * Get the entity type URIs associated to the specified post.
 *
 * @param int $post_id The post ID.
 *
 * @return array An array of terms.
 * @since 3.0.0
 *
 */
function wl_get_entity_rdf_types( $post_id ) {

	return get_post_meta( $post_id, Wordlift_Schema_Service::FIELD_ENTITY_TYPE );
}

/**
 * Set the types for the entity with the specified post ID.
 *
 * @param int $post_id The entity post ID.
 * @param array $type_uris An array of type URIs.
 */
function wl_set_entity_rdf_types( $post_id, $type_uris = array() ) {

	// Avoid errors because of null values.
	if ( is_null( $type_uris ) ) {
		$type_uris = array();
	}

	// Ensure there are no duplicates.
	$type_uris = array_unique( $type_uris );

	delete_post_meta( $post_id, Wordlift_Schema_Service::FIELD_ENTITY_TYPE );
	foreach ( $type_uris as $type_uri ) {
		if ( empty( $type_uri ) ) {
			continue;
		}
		add_post_meta( $post_id, Wordlift_Schema_Service::FIELD_ENTITY_TYPE, $type_uri );
	}
}

///**
// * Retrieve entity property constraints, starting from the schema.org's property name
// * or from the WL_CUSTOM_FIELD_xxx name.
// *
// * @param $property_name as defined by schema.org or WL internal constants
// *
// * @return array containing constraint(s) or null (in case of error or no constraint).
// */
//function wl_get_meta_constraints( $property_name ) {
//
//	// Property name must be defined.
//	if ( ! isset( $property_name ) || is_null( $property_name ) ) {
//		return null;
//	}
//
//	// store eventual schema name in  different variable
//	$property_schema_name = wl_build_full_schema_uri_from_schema_slug( $property_name );
//
//	// Get WL taxonomy mapping.
//	$types = wl_entity_taxonomy_get_custom_fields();
//
//	// Loop over types
//	foreach ( $types as $type ) {
//		// Loop over custom fields of this type
//		foreach ( $type as $property => $field ) {
//			if ( isset( $field['constraints'] ) && ! empty( $field['constraints'] ) ) {
//				// Is this the property we are searhing for?
//				if ( ( $property == $property_name ) || ( $field['predicate'] == $property_schema_name ) ) {
//					return $field['constraints'];
//				}
//			}
//		}
//	}
//
//	return null;
//}

/**
 * Retrieve entity type custom fields.
 *
 * @param int $entity_id id of the entity, if any.
 *
 * @return array|null if $entity_id was specified, return custom_fields for that entity's type. Otherwise returns all custom_fields
 */
function wl_entity_taxonomy_get_custom_fields( $entity_id = null ) {

	if ( is_null( $entity_id ) ) {

		// Return all custom fields.
		// Get taxonomy terms
		$terms = get_terms( Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, array( 'get' => 'all' ) );

		if ( is_wp_error( $terms ) ) {
			return null;
		}

		$custom_fields = array();
		foreach ( $terms as $term ) {
			// Get custom_fields
			$term_options = Wordlift_Schema_Service::get_instance()
			                                       ->get_schema( $term->slug );

			if ( ! isset( $term_options['uri'] ) || ! isset( $term_options['custom_fields'] ) ) {
				continue;
			}

			$custom_fields[ $term_options['uri'] ] = $term_options['custom_fields'];
		}

		return $custom_fields;

	}

	// Return custom fields for this specific entity's type.
	$types = Wordlift_Entity_Type_Service::get_instance()->get_ids( $entity_id );

	/** @var WP_Term[] $terms */
	$terms = array_filter( array_map( function ( $item ) {
		return get_term( $item );
	}, $types ), function ( $item ) {
		return isset( $item ) && is_a( $item, 'WP_Term' );
	} );

	$term_slugs = array_map( function ( $item ) {
		return $item->slug;
	}, $terms );

	$term_slugs[] = 'thing';

	$schema_service = Wordlift_Schema_Service::get_instance();

	return array_reduce( $term_slugs, function ( $carry, $item ) use ( $schema_service ) {

		$schema = $schema_service->get_schema( $item );

		if ( ! isset( $schema['custom_fields'] ) ) {
			return $carry;
		}

		return $carry + $schema['custom_fields'];
	}, array() );
}
