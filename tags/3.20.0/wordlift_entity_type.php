<?php

/**
 * Set the main type for the entity using the related taxonomy.
 *
 * @deprecated use Wordlift_Entity_Type_Service::get_instance()->set( $post_id, $type_uri )
 *
 * @param int $post_id The numeric post ID.
 * @param string $type_uri A type URI.
 */
function wl_set_entity_main_type( $post_id, $type_uri ) {

	Wordlift_Entity_Type_Service::get_instance()
	                            ->set( $post_id, $type_uri );

}

/**
 * Prints inline JavaScript with the entity types configuration removing duplicates.
 */
function wl_print_entity_type_inline_js() {

	$terms = get_terms( Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, array( 'get' => 'all', ) );

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
				'sameAs'    => isset( $type['same_as'] ) ? $type['same_as'] : array(),
				'slug'      => $term->slug,
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
