<?php

/**
 * Registers the entity custom post type (from the *init* hook).
 */
function wl_entity_type_register() {

	$labels = array(
		'name'               => _x( 'Vocabulary', 'post type general name', 'wordlift' ),
		'singular_name'      => _x( 'Entity', 'post type singular name', 'wordlift' ),
		'add_new'            => _x( 'Add New Entity', 'entity', 'wordlift' ),
		'add_new_item'       => __( 'Add New Entity', 'wordlift' ),
		'edit_item'          => __( 'Edit Entity', 'wordlift' ),
		'new_item'           => __( 'New Entity', 'wordlift' ),
		'all_items'          => __( 'All Entities', 'wordlift' ),
		'view_item'          => __( 'View Entity', 'wordlift' ),
		'search_items'       => __( 'Search in Vocabulary', 'wordlift' ),
		'not_found'          => __( 'No entities found', 'wordlift' ),
		'not_found_in_trash' => __( 'No entities found in the Trash', 'wordlift' ),
		'parent_item_colon'  => '',
		'menu_name'          => __( 'Vocabulary', 'wordlift' )
	);

	$args = array(
		'labels'        => $labels,
		'description'   => 'Holds our vocabulary (set of entities) and entity specific data',
		'public'        => true,
		'menu_position' => 20, // after the pages menu.
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
		'has_archive'   => true/*,
        'taxonomies' => array('category')*/
	);

	register_post_type( Wordlift_Entity_Service::TYPE_NAME, $args );
}

add_action( 'init', 'wl_entity_type_register' );


/**
 * Adds the Entity URL box and the Entity SameAs box (from the hook *add_meta_boxes*).
 */
function wl_entity_type_meta_boxes() {
	add_meta_box(
		'wordlift_entity_box',
		__( 'Entity URL', 'wordlift' ),
		'wl_entity_type_meta_boxes_content',
		'entity',
		'normal',
		'high'
	);
}

add_action( 'add_meta_boxes', 'wl_entity_type_meta_boxes' );

/**
 * Displays the content of the entity URL box (called from the *entity_url* method).
 *
 * @param WP_Post $post The post.
 */
function wl_entity_type_meta_boxes_content( $post ) {

	wp_nonce_field( 'wordlift_entity_box', 'wordlift_entity_box_nonce' );

	$value = wl_get_entity_uri( $post->ID );

	echo '<label for="entity_url">' . __( 'entity-url-label', 'wordlift' ) . '</label>';
	echo '<input type="text" id="entity_url" name="entity_url" placeholder="enter a URL" value="' . esc_attr( $value ) . '" style="width: 100%;" />';

	/*
$entity_types = implode( "\n", wl_get_entity_rdf_types( $post->ID ) );

echo '<label for="entity_types">' . __( 'entity-types-label', 'wordlift' ) . '</label>';
echo '<textarea style="width: 100%;" id="entity_types" name="entity_types" placeholder="Entity Types URIs">' . esc_attr( $entity_types ) . '</textarea>';
	*/
}

/**
 * Saves the entity URL for the specified post ID (set via the *save_post* hook).
 *
 * @param int $post_id The post ID.
 *
 * @return int|null
 */
function wl_entity_type_save_custom_fields( $post_id ) {

	// Check if our nonce is set.
	if ( ! isset( $_POST['wordlift_entity_box_nonce'] ) ) {
		return $post_id;
	}

	$nonce = $_POST['wordlift_entity_box_nonce'];

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $nonce, 'wordlift_entity_box' ) ) {
		return $post_id;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	// Check the user's permissions.
	if ( 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}
	}

	// save the entity URL.
	wl_set_entity_uri(
		$post_id,
		$_POST['entity_url']
	);

	/*
// save the rdf:type values.
wl_set_entity_rdf_types(
	$post_id,
	explode( "\r\n", $_POST['entity_types'] )
);
	*/

}

add_action( 'save_post', 'wl_entity_type_save_custom_fields' );

/**
 * Set the main type for the entity using the related taxonomy.
 *
 * @param int $post_id The numeric post ID.
 * @param string $type_uri A type URI.
 */
function wl_set_entity_main_type( $post_id, $type_uri ) {

//	wl_write_log( "wl_set_entity_main_type [ post id :: $post_id ][ type uri :: $type_uri ]" );

	// If the type URI is empty we remove the type.
	if ( empty( $type_uri ) ) {
		wp_set_object_terms( $post_id, null, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );

		return;
	}

	// Get all the terms bound to the wl_entity_type taxonomy.
	$terms = get_terms( Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME, array(
		'hide_empty' => false,
		'fields'     => 'id=>slug'
	) );

	// Check which term matches the specified URI.
	foreach ( $terms as $term_id => $term_slug ) {
		// Load the type data.
		$type = Wordlift_Schema_Service::get_instance()->get_schema( $term_slug );
		// Set the related term ID.
		if ( $type_uri === $type['uri'] || $type_uri === $type['css_class'] ) {
			wp_set_object_terms( $post_id, (int) $term_id, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );

			return;
		}
	}
}

/**
 * Prints inline JavaScript with the entity types configuration removing duplicates.
 */
function wl_print_entity_type_inline_js() {

	$terms = get_terms( Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME, array(
		'hide_empty' => false
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
		$type = Wordlift_Schema_Service::get_instance()->get_schema( $term->slug );

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
