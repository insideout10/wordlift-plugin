<?php

/**
 * Set the main type for the entity using the related taxonomy.
 *
 * @param int    $post_id The numeric post ID.
 * @param string $type_uri A type URI.
 *
 * @deprecated use Wordlift_Entity_Type_Service::get_instance()->set( $post_id, $type_uri )
 */
function wl_set_entity_main_type( $post_id, $type_uri ) {

	Wordlift_Entity_Type_Service::get_instance()
								->set( $post_id, $type_uri );

}

/**
 * Prints inline JavaScript with the entity types configuration removing duplicates.
 */
function wl_print_entity_type_inline_js() {

	$terms = get_terms( Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, array( 'get' => 'all' ) );

	// Load the type data.
	$schema_service = Wordlift_Schema_Service::get_instance();
	$entity_types   = array_reduce(
		$terms,
		function ( $carry, $term ) use ( $schema_service ) {
			$type = $schema_service->get_schema( $term->slug );

			// Skip if no `uri`.
			if ( empty( $type['uri'] ) ) {
				return $carry;
			}

			$carry[] = array(
				'label'     => $term->name,
				'uri'       => $type['uri'],
				'css'       => $type['css_class'],
				'sameAs'    => isset( $type['same_as'] ) ? $type['same_as'] : array(),
				'slug'      => $term->slug,
				'templates' => ( isset( $type['templates'] ) ? $type['templates'] : array() ),
			);

			return $carry;
		},
		array()
	);

	// Hook to the Block Editor script.
	wp_localize_script( 'wl-block-editor', '_wlEntityTypes', $entity_types );

	// Hook to the Classic Editor script, see Wordlift_Admin_Post_Edit_Page.
	wp_localize_script( 'wl-classic-editor', '_wlEntityTypes', $entity_types );

}

// Allow Classic and Block Editor scripts to register first.
add_action( 'admin_print_scripts-post.php', 'wl_print_entity_type_inline_js', 11 );
add_action( 'admin_print_scripts-post-new.php', 'wl_print_entity_type_inline_js', 11 );
