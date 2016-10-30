<?php

/**
 * Set the main type for the entity using the related taxonomy.
 *
 * @deprecated use Wordlift_Entity_Type_Service::get_instance()->set( $post_id, $type_uri )
 *
 * @param int    $post_id  The numeric post ID.
 * @param string $type_uri A type URI.
 */
function wl_set_entity_main_type( $post_id, $type_uri ) {

	Wordlift_Entity_Type_Service::get_instance()
	                            ->set( $post_id, $type_uri );

//
////	wl_write_log( "wl_set_entity_main_type [ post id :: $post_id ][ type uri :: $type_uri ]" );
//
//	// If the type URI is empty we remove the type.
//	if ( empty( $type_uri ) ) {
//		wp_set_object_terms( $post_id, NULL, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );
//
//		return;
//	}
//
//	// Get all the terms bound to the wl_entity_type taxonomy.
//	$terms = get_terms( Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME, array(
//		'hide_empty' => FALSE,
//		// Because of #334 (and the AAM plugin) we changed fields from 'id=>slug' to 'all'.
//		// An issue has been opened with the AAM plugin author as well.
//		//
//		// see https://github.com/insideout10/wordlift-plugin/issues/334
//		// see https://wordpress.org/support/topic/idslug-not-working-anymore?replies=1#post-8806863
//		'fields'     => 'all',
//	) );
//
//	// Check which term matches the specified URI.
//	foreach ( $terms as $term ) {
//
//		$term_id   = $term->term_id;
//		$term_slug = $term->slug;
//
//		// Load the type data.
//		$type = Wordlift_Schema_Service::get_instance()
//		                               ->get_schema( $term_slug );
//		// Set the related term ID.
//		if ( $type_uri === $type['uri'] || $type_uri === $type['css_class'] ) {
//
//			Wordlift_Log_Service::get_logger( 'wl_set_entity_main_type' )
//			                    ->debug( "Setting entity type [ post id :: $post_id ][ term id :: $term_id ][ term slug :: $term_slug ][ type uri :: {$type['uri']} ][ type css class :: {$type['css_class']} ]" );
//
//			wp_set_object_terms( $post_id, (int) $term_id, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );
//
//			return;
//		}
//	}
}

/**
 * Prints inline JavaScript with the entity types configuration removing duplicates.
 */
function wl_print_entity_type_inline_js() {

	$terms = get_terms( Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME, array(
		'hide_empty' => FALSE,
	) );

	echo <<<EOF
    <script type="text/javascript">
        (function() {
        var t = [];

EOF;

	// Cycle in each WordLift term and get its metadata. The metadata will be printed as a global object in JavaScript
	// to be used by the JavaScript client library.
	foreach ( $terms as $term ) {

		$term_name = $term->name;

		// Load the type data.
		$type = Wordlift_Schema_Service::get_instance()
		                               ->get_schema( $term->slug );

		// Skip types that are not defined.
		if ( ! empty( $type['uri'] ) ) {

			// Prepare the JSON output then print it to the browser.
			$json = json_encode( array(
				'label'     => $term_name,
				'uri'       => $type['uri'],
				'css'       => $type['css_class'],
				'sameAs'    => $type['same_as'],
				'templates' => ( isset( $type['templates'] ) ? $type['templates'] : array() ),
			) );

			// Output the type data.
			echo "t.push($json);\n";

		}

	}

	echo <<<EOF
            if ('undefined' == typeof window.wordlift) {
                window.wordlift = {}
            }
            window.wordlift.types = t;

        })();
    </script>
EOF;

}

add_action( 'admin_print_scripts', 'wl_print_entity_type_inline_js' );

add_action( 'init', 'wl_entity_type_taxonomy_register', 0 );
