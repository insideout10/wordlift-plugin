<?php

/**
 * Push the provided post to Redlink (not suitable for entities).
 *
 * @param object $post A post instance.
 */
function wl_push_post_to_redlink( $post ) {


	// Only handle published posts.
	if ( 'post' !== $post->post_type or 'publish' !== $post->post_status ) {
		wl_write_log( "wl_push_post_to_redlink : not a post or not published [ post type :: $post->post_type ][ post status :: $post->post_status ]" );
		return;
	}

	// Get the post URI.
	$uri = wl_get_entity_uri( $post->ID );

	// If the URI ends with a trailing slash, then we have a problem.
	if ( '/' === substr( $uri, - 1, 1 ) ) {

		wl_write_log( "wl_push_post_to_redlink : the URI is invalid [ post ID :: $post->ID ][ URI :: $uri ]" );

		return;
	}

	wl_write_log( "wl_push_post_to_redlink [ post id :: $post->ID ][ uri :: $uri ]" );

	// Get the site language in order to define the literals language.
	$site_language = wl_configuration_get_site_language();

	// save the author and get the author URI.
	$author_uri = wl_get_user_uri( $post->post_author );

	// Get other post properties.
	$date_published      = wl_get_sparql_time( get_the_time( 'c', $post ) );
	$date_modified       = wl_get_sparql_time( wl_get_post_modified_time( $post ) );
	$title               = wordlift_esc_sparql( $post->post_title );
	$permalink           = wordlift_esc_sparql( get_permalink( $post->ID ) );
	$user_comments_count = $post->comment_count;

	wl_write_log( "wl_push_post_to_redlink [ post_id :: $post->ID ][ type :: $post->post_type ][ slug :: $post->post_name ][ title :: $post->post_title ][ date modified :: $date_modified ][ date published :: $date_published ]" );

	// create the SPARQL query.
	$sparql = '';
	if ( ! empty( $title ) ) {
		$sparql .= "<$uri> rdfs:label '$title'@$site_language . \n";
	}

	$sparql .= "<$uri> a <http://schema.org/BlogPosting> . \n";
	$sparql .= "<$uri> schema:url <$permalink> . \n";
	$sparql .= "<$uri> schema:datePublished $date_published . \n";
	$sparql .= "<$uri> schema:dateModified $date_modified . \n";
	if ( ! empty( $author_uri ) ) {
		$sparql .= "<$uri> schema:author <$author_uri> . \n";
	}
	$sparql .= "<$uri> schema:interactionCount 'UserComments:$user_comments_count' . \n";


	// Add SPARQL stmts to write the schema:image.
	$sparql .= wl_get_sparql_images( $uri, $post->ID );

	// Get the SPARQL fragment with the dcterms:references statement.
	$sparql .= wl_get_sparql_post_references( $post->ID );

	// create the query:
	//  - remove existing references to entities.
	//  - set the new post information (including references).
	$query = rl_sparql_prefixes() . <<<EOF
            DELETE { <$uri> dct:references ?o . }
            WHERE  { <$uri> dct:references ?o . };
            DELETE { <$uri> schema:url ?o . }
            WHERE  { <$uri> schema:url ?o . };
            DELETE { <$uri> schema:datePublished ?o . }
            WHERE  { <$uri> schema:datePublished ?o . };
            DELETE { <$uri> schema:dateModified ?o . }
            WHERE  { <$uri> schema:dateModified ?o . };
            DELETE { <$uri> a ?o . }
            WHERE  { <$uri> a ?o . };
            DELETE { <$uri> rdfs:label ?o . }
            WHERE  { <$uri> rdfs:label ?o . };
            DELETE { <$uri> schema:image ?o . }
            WHERE  { <$uri> schema:image ?o . };
            DELETE { <$uri> schema:interactionCount ?o . }
            WHERE  { <$uri> schema:interactionCount ?o . };
            INSERT DATA { $sparql };
EOF;

	// execute the query.
	rl_execute_sparql_update_query( $query );
}

/**
 * Push the provided entity post to Redlink.
 *
 * @param object $entity_post An entity post instance.
 */
function wl_push_entity_post_to_redlink( $entity_post ) {

	// Only handle published entities.
	if ( 'entity' !== $entity_post->post_type or 'publish' !== $entity_post->post_status ) {

		wl_write_log( "wl_push_entity_post_to_redlink : not an entity or not published [ post type :: $entity_post->post_type ][ post status :: $entity_post->post_status ]" );

		return;
	}

	// get the entity URI and the SPARQL escaped version.
	$uri   = wl_get_entity_uri( $entity_post->ID );
	$uri_e = esc_html( $uri );

	// If the URI ends with a trailing slash, then we have a problem.
	if ( '/' === substr( $uri, - 1, 1 ) ) {

		wl_write_log( "wl_push_entity_post_to_redlink : the URI is invalid [ post ID :: $entity_post->ID ][ URI :: $uri ]" );

		return;
	}

	// Get the site language in order to define the literals language.
	$site_language = wl_configuration_get_site_language();

	// get the title and content as label and description.
	$label     = wordlift_esc_sparql( $entity_post->post_title );
	$descr     = wordlift_esc_sparql( $entity_post->post_content );
	$permalink = wordlift_esc_sparql( get_permalink( $entity_post->ID ) );

	wl_write_log( "wl_push_entity_post_to_redlink [ entity post id :: $entity_post->ID ][ uri :: $uri ][ label :: $label ]" );

	// create a new empty statement.
	$delete_stmt = '';
	$sparql      = '';

	// set the same as.
	$same_as = wl_get_same_as( $entity_post->ID );
	foreach ( $same_as as $same_as_uri ) {
		$same_as_uri_esc = wordlift_esc_sparql( $same_as_uri );
		$sparql .= "<$uri_e> owl:sameAs <$same_as_uri_esc> . \n";
	}

	// set the label
	$sparql .= "<$uri_e> rdfs:label \"$label\"@$site_language . \n";
	// set the URL
	$sparql .= "<$uri_e> schema:url <$permalink> . \n";

	// set the description.
	if ( ! empty( $descr ) ) {
		$sparql .= "<$uri_e> schema:description \"$descr\"@$site_language . \n";
	}

	$main_type = wl_entity_type_taxonomy_get_type( $entity_post->ID );

	if ( null != $main_type ) {
		$main_type_uri = wordlift_esc_sparql( $main_type['uri'] );
		$sparql .= " <$uri_e> a <$main_type_uri> . \n";

		// The type define custom fields that hold additional data about the entity.
		// For example Events may have start/end dates, Places may have coordinates.
		// The value in the export fields must be rewritten as triple predicates, this
		// is what we're going to do here.

		wl_write_log( 'wl_push_entity_post_to_redlink : checking if entity has export fields [ type :: ' . var_export( $main_type, true ) . ' ]' );
                
		if ( isset( $main_type['custom_fields'] ) ) {
			foreach ( $main_type['custom_fields'] as $field => $settings ) {

				wl_write_log( "wl_push_entity_post_to_redlink : entity has export fields" );

				$predicate = wordlift_esc_sparql( $settings['predicate'] );
				if ( ! isset( $settings['export_type'] ) || empty( $settings['export_type'] ) ) {
					$type = null;
				} else {
					$type = $settings['export_type'];
				}

				// add the delete statement for later execution.
				$delete_stmt .= "DELETE { <$uri_e> <$predicate> ?o } WHERE  { <$uri_e> <$predicate> ?o };\n";

				foreach ( get_post_meta( $entity_post->ID, $field ) as $value ) {
					$sparql .= " <$uri_e> <$predicate> ";
                                        
                                        // Establish triple's <object> type
					if ( is_null( $type ) ) {
                                                // No type
						$sparql .= '<' . wordlift_esc_sparql( $value ) . '>';
					} else {
						$sparql .= '"' . wordlift_esc_sparql( $value ) . '"^^';
                                                if( substr( $type, 0, 4 ) == 'http' ) {
                                                    // Type is defined by a raw uri (es. http://schema.org/PostalAddress)
                                                    $sparql .= '<' . wordlift_esc_sparql( $type ) . '>';
                                                } else {
                                                    // Type is defined in another way (es. xsd:double)
                                                    $sparql .= wordlift_esc_sparql( $type );
                                                }
					}

					$sparql .= " . \n";
				}
			}
		}
	}

	// Get the entity types.
	$type_uris = wl_get_entity_rdf_types( $entity_post->ID );

	// Support type are only schema.org ones: it could by null
	foreach ( $type_uris as $type_uri ) {
		$type_uri = esc_attr( $type_uri );
		$sparql .= "<$uri_e> a <$type_uri> . \n";
	}

	// get related entities.
	$related_entities_ids = wl_get_related_entities( $entity_post->ID );

	if ( is_array( $related_entities_ids ) ) {
		foreach ( $related_entities_ids as $entity_post_id ) {
			$related_entity_uri = wordlift_esc_sparql( wl_get_entity_uri( $entity_post_id ) );
			// create a two-way relationship.
			$sparql .= " <$uri_e> dct:relation <$related_entity_uri> . \n";
			$sparql .= " <$related_entity_uri> dct:relation <$uri_e> . \n";
		}
	}

	// Add SPARQL stmts to write the schema:image.
	$sparql .= wl_get_sparql_images( $uri, $entity_post->ID );

	$query = rl_sparql_prefixes() . <<<EOF
    $delete_stmt
    DELETE { <$uri_e> rdfs:label ?o } WHERE  { <$uri_e> rdfs:label ?o };
    DELETE { <$uri_e> owl:sameAs ?o . } WHERE  { <$uri_e> owl:sameAs ?o . };
    DELETE { <$uri_e> schema:description ?o . } WHERE  { <$uri_e> schema:description ?o . };
    DELETE { <$uri_e> schema:url ?o . } WHERE  { <$uri_e> schema:url ?o . };
    DELETE { <$uri_e> a ?o . } WHERE  { <$uri_e> a ?o . };
    DELETE { <$uri_e> dct:relation ?o . } WHERE  { <$uri_e> dct:relation ?o . };
    DELETE { <$uri_e> schema:image ?o . } WHERE  { <$uri_e> schema:image ?o . };
    DELETE { <$uri_e> geo:lat ?o . } WHERE  { <$uri_e> geo:lat ?o . };
    DELETE { <$uri_e> geo:long ?o . } WHERE  { <$uri_e> geo:long ?o . };
    INSERT DATA { $sparql };
EOF;

	rl_execute_sparql_update_query( $query );
}

/**
 * Get the SPARQL fragment to set the dc:references statements.
 *
 * @param int $post_id The post ID.
 *
 * @return string The SPARQL fragment (or an empty string).
 */
function wl_get_sparql_post_references( $post_id ) {

	// Get the post URI.
	$post_uri = wordlift_esc_sparql( wl_get_entity_uri( $post_id ) );

	// Get the related entities IDs.
	$related = wl_get_referenced_entities( $post_id );

	// Build the SPARQL fragment.
	$sparql = '';
	foreach ( $related as $id ) {
		$uri = wordlift_esc_sparql( wl_get_entity_uri( $id ) );
		$sparql .= "<$post_uri> dct:references <$uri> . ";
	}

	return $sparql;
}

/**
 * Find entity posts by the entity URI. Entity as searched by their entity URI or same as.
 *
 * @param string $uri The entity URI.
 *
 * @return WP_Post|null A WP_Post instance or null if not found.
 */
function wl_get_entity_post_by_uri( $uri ) {

	$query = new WP_Query( array(
			'posts_per_page' => 1,
			'post_status'    => 'any',
			'post_type'      => 'entity',
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => 'entity_same_as',
					'value'   => $uri,
					'compare' => '='
				),
				array(
					'key'     => 'entity_url',
					'value'   => $uri,
					'compare' => '='
				)
			)
		)
	);

	// Get the matching entity posts.
	$posts = $query->get_posts();

	wl_write_log( "wl_get_entity_post_by_uri [ uri :: $uri ][ count :: " . count( $posts ) . " ]\n" );

	// Return null if no post is found.
	if ( 0 === count( $posts ) ) {
		return null;
	}

	// Return the found post.
	return $posts[0];
}


/**
 * Get a string representing the NS prefixes for a SPARQL query.
 *
 * @return string The PREFIX lines.
 */
function rl_sparql_prefixes() {

	$prefixes = '';
	foreach ( wl_prefixes() as $prefix => $uri ) {
		$prefixes .= "PREFIX $prefix: <$uri>\n";
	}

	return $prefixes;
}

/**
 * Escape a sparql literal.
 *
 * @param string $string The string to escape.
 *
 * @return string The escaped string.
 */
function wordlift_esc_sparql( $string ) {
	// see http://www.w3.org/TR/rdf-sparql-query/
	//    '\t'	U+0009 (tab)
	//    '\n'	U+000A (line feed)
	//    '\r'	U+000D (carriage return)
	//    '\b'	U+0008 (backspace)
	//    '\f'	U+000C (form feed)
	//    '\"'	U+0022 (quotation mark, double quote mark)
	//    "\'"	U+0027 (apostrophe-quote, single quote mark)
	//    '\\'	U+005C (backslash)

	$string = str_replace( '\\', '\\\\', $string );
	$string = str_replace( '\'', '\\\'', $string );
	$string = str_replace( '"', '\\"', $string );
	$string = str_replace( "\f", '\\f', $string );
	$string = str_replace( "\b", '\\b', $string );
	$string = str_replace( "\r", '\\r', $string );
	$string = str_replace( "\n", '\\n', $string );
	$string = str_replace( "\t", '\\t', $string );

	return $string;
}

/**
 * Reindex Redlink triple store, enabling local entities to be found in future analyses.
 */
function wordlift_reindex_triple_store() {

	// Get the reindex URL.
	$url = wl_configuration_get_dataset_index_url();

	// Post the request.
	wl_write_log( "wordlift_reindex_triple_store" );

	// Prepare the request.
	$args = array_merge_recursive( unserialize( WL_REDLINK_API_HTTP_OPTIONS ), array(
		'method'  => 'POST',
		'headers' => array()
	) );

	$response = wp_remote_request( $url, $args );

	// Remove the key from the query.
	$scrambled_url = preg_replace( '/key=.*$/i', 'key=<hidden>', $url );

	// If an error has been raised, return the error.
	if ( is_wp_error( $response ) || 200 !== $response['response']['code'] ) {

		$body = ( is_wp_error( $response ) ? $response->get_error_message() : $response['body'] );

		wl_write_log( "wordlift_reindex_triple_store : error [ url :: $scrambled_url ][ args :: " );
		wl_write_log( "\n" . var_export( $args, true ) );
		wl_write_log( "[ response :: " );
		wl_write_log( "\n" . var_export( $response, true ) );
		wl_write_log( "][ body :: " );
		wl_write_log( "\n" . $body );
		wl_write_log( "]" );

		return false;
	}

	return true;
}

// hook save events.
// moved to the Linked Data module: add_action('save_post', 'wordlift_save_post');
// moved to the Linked Data module: add_action('wordlift_save_post', 'wordlift_save_post_and_related_entities');
