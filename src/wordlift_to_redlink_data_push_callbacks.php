<?php

/**
 * Push the post with the specified ID to Redlink.
 * @param int $post_id The post ID.
 */
function wl_push_to_redlink( $post_id ) {

    // Get the post.
    $post = get_post( $post_id );

    write_log( "wl_push_to_redlink [ post id :: $post_id ][ post type :: $post->post_type ]" );

    // Call the method on behalf of the post type.
    switch ( $post->post_type ) {
        case 'entity':
            wl_push_entity_post_to_redlink( $post );
            break;
        default:
            wl_push_post_to_redlink( $post );
    }

    // TODO: reindex.
}

/**
 * Push the provided post to Redlink (not suitable for entities).
 * @param object $post A post instance.
 */
function wl_push_post_to_redlink( $post ) {

    // Don't deal with entities here.
    if ( 'entity' === $post->post_type ) {
        return;
    }

    // Get the post URI.
    $uri = wl_get_entity_uri( $post->ID );

    write_log( "wl_push_post_to_redlink [ post id :: $post->ID ][ uri :: $uri ]" );

    // Get the site language in order to define the literals language.
    $site_language  = wordlift_configuration_site_language();

    // save the author and get the author URI.
    $author_uri     = wordlift_save_author( $post->post_author );

    // Get other post properties.
    $date_published = wl_get_sparql_time( get_the_time('c', $post) );
    $date_modified  = wl_get_sparql_time( wl_get_post_modified_time( $post ) );
    $title          = wordlift_esc_sparql( $post->post_title );
    $permalink      = wordlift_esc_sparql( get_permalink( $post->ID ) );
    $user_comments_count = $post->comment_count;

    write_log( "wordlift_save_post_and_related_entities [ post_id :: $post->ID ][ type :: $post->post_type ][ slug :: $post->post_name ][ title :: $post->post_title ][ date modified :: $date_modified ][ date published :: $date_published ]" );

    // create the SPARQL query.
    $sparql  = "<$uri> rdfs:label '$title'@$site_language . \n";
    $sparql .= "<$uri> a <http://schema.org/BlogPosting> . \n";
    $sparql .= "<$uri> schema:url <$permalink> . \n";
    $sparql .= "<$uri> schema:datePublished $date_published . \n";
    $sparql .= "<$uri> schema:dateModified $date_modified . \n";
    $sparql .= "<$uri> schema:author <$author_uri> . \n";
    $sparql .= "<$uri> schema:interactionCount 'UserComments:$user_comments_count' . \n";


    // Add SPARQL stmts to write the schema:image.
    $sparql .= wl_get_sparql_images( $uri, $post->ID );

    // Get the SPARQL fragment with the dcterms:references statement.
    $sparql .= wl_get_sparql_post_references( $post->ID );

    // create the query:
    //  - remove existing references to entities.
    //  - set the new post information (including references).
    $query = wordlift_get_ns_prefixes() . <<<EOF
            DELETE { <$uri> dcterms:references ?o . }
            WHERE  { <$uri> dcterms:references ?o . };
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
            INSERT DATA { $sparql }
EOF;

    // execute the query.
    wordlift_push_data_triple_store($query);
}

/**
 * Push the provided entity post to Redlink.
 * @param object $entity_post An entity post instance.
 */
function wl_push_entity_post_to_redlink( $entity_post ) {

    // Deal only with entity posts.
    if ( 'entity' !== $entity_post->post_type ) {
        return;
    }

    // Get the site language in order to define the literals language.
    $site_language = wordlift_configuration_site_language();

    // get the title and content as label and description.
    $label   = wordlift_esc_sparql( $entity_post->post_title );
    $descr   = wordlift_esc_sparql( $entity_post->post_content );
    $permalink = wordlift_esc_sparql( get_permalink( $entity_post->ID ) );

    // get the entity URI.
    $uri     = wl_get_entity_uri( $entity_post->ID );

    write_log( "wl_push_entity_post_to_redlink [ entity post id :: $entity_post->ID ][ uri :: $uri ][ label :: $label ]" );

    // create a new empty statement.
    $sparql  = '';

    // set the same as.
    $same_as = wl_get_same_as( $entity_post->ID );
    foreach ( $same_as as $same_as_uri ) {
        $same_as_uri_esc = wordlift_esc_sparql( $same_as_uri );
        $sparql  .= "<$uri> owl:sameAs <$same_as_uri_esc> . \n";
    }

    // set the label
    $sparql  .= "<$uri> rdfs:label \"$label\"@$site_language . \n";
    // set the URL
    $sparql  .= "<$uri> schema:url <$permalink> . \n";

    // set the description.
    if (!empty($descr)) {
        $sparql  .= "<$uri> schema:description \"$descr\"@$site_language . \n";
    }

    // Get the entity types.
    $types   = wordlift_get_entity_types( $entity_post->ID );

    // Support type are only schema.org ones: it could by null
    foreach ($types as $type) {
        // Capitalize the first letter.
        // TODO: we shouldn't do this here, we should take the 'original' type.
        $type = ucwords( $type->name );
        $sparql .= "<$uri> a <http://schema.org/$type> . \n";
    }

    // get related entities.
    $related_entities_ids = wl_get_related_entities( $entity_post->ID );

    if ( is_array( $related_entities_ids ) ) {
        foreach ( $related_entities_ids as $entity_post_id ) {
            $entity_uri = wordlift_esc_sparql( wl_get_entity_uri( $entity_post->ID ) );
            // create a two-way relationship.
            $sparql .= " <$uri> dct:relation <$entity_uri> . \n";
            $sparql .= " <$entity_uri> dct:relation <$uri> . \n";
        }
    }

    // Add SPARQL stmts to write the schema:image.
    $sparql .= wl_get_sparql_images( $uri, $entity_post->ID );

    $query = wordlift_get_ns_prefixes() . <<<EOF
    DELETE { <$uri> rdfs:label ?o }
    WHERE  { <$uri> rdfs:label ?o };
    DELETE { <$uri> owl:sameAs ?o . }
    WHERE  { <$uri> owl:sameAs ?o . };
    DELETE { <$uri> schema:description ?o . }
    WHERE  { <$uri> schema:description ?o . };
    DELETE { <$uri> schema:url ?o . }
    WHERE  { <$uri> schema:url ?o . };
    DELETE { <$uri> a ?o . }
    WHERE  { <$uri> a ?o . };
    DELETE { <$uri> dct:relation ?o . }
    WHERE  { <$uri> dct:relation ?o . };
    DELETE { <$uri> schema:image ?o . }
    WHERE  { <$uri> schema:image ?o . };
    INSERT DATA { $sparql }
EOF;

    wordlift_push_data_triple_store($query);
}

/**
 * Save the post to the triple store. Also saves the entities locally and on the triple store.
 * @param int $post_id The post id being saved.
 */
function wordlift_save_post_and_related_entities( $post_id ) {

    // Ignore auto-saves
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;

    // get the current post.
    $post       = get_post( $post_id );

    write_log( "wordlift_save_post_and_related_entities [ post id :: $post_id ][ autosave :: false ][ post type :: $post->post_type ]" );

    // Do not save entity posts here.
    if ( 'entity' === $post->post_type ) {
        return;
    }

    // Remove existing bindings between the post and related entities.
    // They will be recreated afterwards.
    wl_unbind_post_from_entities( $post_id );

    // Save the entities coming with POST data.
    $entity_posts_ids = array();
    if ( isset( $_POST['wl_entities'] ) ) {
        $entities_via_post = array_values( $_POST['wl_entities'] );
        write_log( "wordlift_save_post_and_related_entities [ entities_via_post :: " );
        write_log( $entities_via_post );
        write_log( "]" );

        $entity_posts = wl_save_entities( $entities_via_post );
        foreach ( $entity_posts as $entity_post ) {
            array_push( $entity_posts_ids, $entity_post->ID );
        }
    }

    // Save entities coming as embedded in the text.
    $entity_post_ids = array_unique( array_merge(
        $entity_posts_ids,
        wordlift_save_entities_embedded_as_spans( $post->post_content )
    ) );

    // Bind the entities to the post.
    wl_bind_post_to_entities( $post_id, $entity_post_ids );

    // Push the post to Redlink.
    wl_push_post_to_redlink( $post );

    // Reindex Redlink triple store.
    wordlift_reindex_triple_store();
}

/**
 * Get the SPARQL fragment to set the dc:references statements.
 * @param int $post_id The post ID.
 * @return string The SPARQL fragment (or an empty string).
 */
function wl_get_sparql_post_references( $post_id ) {

    // Get the post URI.
    $post_uri = wordlift_esc_sparql( wl_get_entity_uri( $post_id ) );

    // Get the related entities IDs.
    $related = wl_get_related_entities( $post_id );

    // Build the SPARQL fragment.
    $sparql = '';
    foreach ( $related as $id ) {
        $uri = wordlift_esc_sparql( wl_get_entity_uri( $id ) );
        $sparql .= "<$post_uri> dcterms:references <$uri> . ";
    }

    return $sparql;
}

/**
 * Save entities embedded in the content as spans.
 * @param string $content The content.
 * @return array An array with the saved post IDs.
 */
function wordlift_save_entities_embedded_as_spans( $content ) {

    // Save the post ids.
    $post_ids = array();

    // Initialize the matches array.
    $matches  = array();

    // Create the pattern.
    $pattern  = '/<span class="[^"]+" id="[^"]+" itemid="([^\"]+)" itemscope="itemscope" itemtype="([^"]+)"><span itemprop="name">([^<]+)<\/span><\/span>/im';

    // Look for the spans and the embedded data.
    if ( 0 < ( $count = preg_match_all( $pattern , $content, $matches ) ) ) {
        for ( $i = 0; $i < $count; $i++ ) {
            $uri   = $matches[1][$i];
            $type  = wl_get_entity_type( $matches[2][$i] );
            $label = $matches[3][$i];

            write_log( "wordlift_save_entities_embedded_as_spans [ uri :: $uri ][ type :: " . $type['class'] . " ][ label :: $label ]" );

            // Save the entity in the local storage.
            $post  = wl_save_entity( $uri, $label, $type, '' );
            if ( !in_array( $post->ID, $post_ids ) ) {
                array_push( $post_ids, $post->ID );
            }
        }
    }

    write_log( "wordlift_save_entities_embedded_as_spans [ entities count :: $count ]\n" );

    return $post_ids;
}

/**
 * Save the specified author to the triple store.
 * @param $author_id
 * @return The author URI.
 */
function wordlift_save_author( $author_id ) {

    // read the user id and dataset name from the options.
    $user_id    = wordlift_configuration_user_id();
    $dataset_id = wordlift_configuration_dataset_id();
    $author_uri = "http://data.redlink.io/$user_id/$dataset_id/author/$author_id";

    $name        = wordlift_esc_sparql( get_the_author_meta( 'display_name', $author_id ) );
    $email       = wordlift_esc_sparql( get_the_author_meta( 'email', $author_id ) );
    $given_name  = wordlift_esc_sparql( get_the_author_meta( 'first_name', $author_id ) );
    $family_name = wordlift_esc_sparql( get_the_author_meta( 'last_name', $author_id ) );
    $description = wordlift_esc_sparql( get_the_author_meta( 'description', $author_id ) );
    $url         = wordlift_esc_sparql( get_author_posts_url( 'user_url' ) );

    // Get the site language in order to define the literals language.
    $site_language = wordlift_configuration_site_language();

    $sparql = "<$author_uri> a <http://schema.org/Person> . ";
    if ( !empty( $name ) ) {
        $sparql .= "<$author_uri> schema:name '$name'@$site_language . ";
    }
    if ( !empty( $given_name ) ) {
        $sparql .= "<$author_uri> schema:givenName '$given_name'@$site_language . ";
    }
    if ( !empty( $family_name ) ) {
        $sparql .= "<$author_uri> schema:familyName '$family_name'@$site_language . ";
    }
    if ( !empty( $email ) ) {
        $sparql .= "<$author_uri> schema:email '$email' . ";
    }
    if ( !empty( $description ) ) {
        $sparql .= "<$author_uri> schema:description \"$description\"@$site_language . ";
    }
    if ( !empty( $url ) ) {
        $sparql .= "<$author_uri> schema:url <$url> . ";
    }

    $query = wordlift_get_ns_prefixes() . <<<EOF
            DELETE { <$author_uri> a ?o . }
            WHERE  { <$author_uri> a ?o . };
            DELETE { <$author_uri> schema:name ?o . }
            WHERE  { <$author_uri> schema:name ?o . };
            DELETE { <$author_uri> schema:givenName  ?o . }
            WHERE  { <$author_uri> schema:givenName  ?o . };
            DELETE { <$author_uri> schema:familyName ?o . }
            WHERE  { <$author_uri> schema:familyName ?o . };
            DELETE { <$author_uri> schema:email ?o . }
            WHERE  { <$author_uri> schema:email ?o . };
            DELETE { <$author_uri> schema:description ?o . }
            WHERE  { <$author_uri> schema:description ?o . };
            DELETE { <$author_uri> schema:url ?o . }
            WHERE  { <$author_uri> schema:url ?o . };
            INSERT DATA { $sparql }
EOF;

    // execute the query.
    wordlift_push_data_triple_store($query);

    return $author_uri;
}

/**
 * Create an URI on the custom dataset based on an existing URI.
 * @param string $uri
 * @return string The dataset URI.
 */
function wordlift_get_custom_dataset_entity_uri( $uri ) {

    // TODO: check for naming collision.

    // read the user id and dataset name from the options.
    $user_id    = wordlift_configuration_user_id();
    $dataset_id = wordlift_configuration_dataset_id();

    $fragments  = explode('/', $uri);
    $name       = end($fragments);

    // set the post URI in the triple store.
    return "http://data.redlink.io/$user_id/$dataset_id/resource/$name";
}

/**
 * Find entity posts by the entity URI. Entity as searched by their entity URI or same as.
 * @param string $uri The entity URI.
 * @return array mixed An array of posts.
 */
function wordlift_get_entity_post_by_uri( $uri ) {

    $query = new WP_Query( array(
            'posts_per_page' => 1,
            'post_status' => 'any',
            'post_type'   => 'entity',
            'meta_query'  => array(
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

    // Get the matching eneity posts.
    $posts = $query->get_posts();

    write_log( "wordlift_get_entity_posts_by_uri [ uri :: $uri ][ count :: " . count( $posts ) . " ]\n" );

    // Return null if no post is found.
    if ( 0 === count( $posts ) ) {
        return null;
    }

    // Return the found post.
    return $posts[0];
}

/**
 * Execute the query against the triple store.
 * @param string $query A SPARQL query.
 * @return bool
 */
function wordlift_push_data_triple_store($query) {

    // construct the API URL.
    $api_url = wordlift_redlink_sparql_update_url();

    // post the request.
    $response = wp_remote_post($api_url, array(
            'method'      => 'POST',
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.1',
            'blocking'    => true, // switched to not blocking.
            'headers'     => array(
                'Content-type' => 'application/sparql-update; charset=utf-8',
            ),
            'body' => $query,
            'sslverify'   => false,
            'cookies'     => array()
        )
    );

    write_log("== QUERY ====================================================\n");
//    write_log("API URL: $api_url\n");
    write_log("$query\n");
    write_log("=============================================================\n");

    // TODO: handle errors.
    if ( is_wp_error( $response ) || 200 !== $response['response']['code'] ) {

        write_log( "== ERROR        =============================================\n" );
        write_log( var_export( $response, true ) );
        write_log( "=============================================================\n" );

        return false;
    }

    return true;
}

/**
 * Receive events from post saves, and split them according to the post type.
 * @param int $post_id The post id.
 */
function wordlift_save_post( $post_id ) {

    // If it's not numeric exit from here.
    if ( !is_numeric( $post_id )
        || is_numeric( wp_is_post_revision( $post_id ) ) ) {
        return;
    }

    // unhook this function so it doesn't loop infinitely
    remove_action('save_post', 'wordlift_save_post');

    // get the post.
    $post = get_post( $post_id );

    // if it's an entity, raise the *wordlift_save_entity* event.
    if ('entity' === $post->post_type) {
        do_action('wordlift_save_entity', $post_id);
    }

    // raise the *wordlift_save_post* event.
    do_action( 'wordlift_save_post', $post_id );

    // re-hook this function
    add_action('save_post', 'wordlift_save_post');
}

/**
 * Build the entity URI given the entity's post.
 * @param int $post_id The post ID.
 * @return string The URI of the entity.
 */
function wordlift_build_entity_uri( $post_id ) {

    // Get the post.
    $post = get_post( $post_id );
    $type = $post->post_type;

    // Create an ID given the title.
    $id  = preg_replace( '/[^\w|\d]/im', '_', $post->post_title );


    // Build the entity URI.
    $url = sprintf(
        'http://data.redlink.io/%s/%s/%s/%s',
        wordlift_configuration_user_id(),
        wordlift_configuration_dataset_id(),
        $type,
        $id
    );

    write_log( "wordlift_build_entity_uri [ post_id :: $post->ID ][ type :: $post->post_type ][ title :: $post->post_title ][ url :: $url ]\n" );

    return $url;
}

/**
 * Get a string representing the NS prefixes for a SPARQL query.
 * @return string The PREFIX lines.
 */
function wordlift_get_ns_prefixes() {

    return <<<EOF
PREFIX dcterms: <http://purl.org/dc/terms/>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX owl: <http://www.w3.org/2002/07/owl#>
PREFIX schema: <http://schema.org/>
PREFIX dct: <http://purl.org/dc/terms/>

EOF;

}

/**
 * Escape a sparql literal.
 * @param string $string The string to escape.
 * @return string The escaped string.
 */
function wordlift_esc_sparql($string) {
    // see http://www.w3.org/TR/rdf-sparql-query/
    //    '\t'	U+0009 (tab)
    //    '\n'	U+000A (line feed)
    //    '\r'	U+000D (carriage return)
    //    '\b'	U+0008 (backspace)
    //    '\f'	U+000C (form feed)
    //    '\"'	U+0022 (quotation mark, double quote mark)
    //    "\'"	U+0027 (apostrophe-quote, single quote mark)
    //    '\\'	U+005C (backslash)

    $string = str_replace('\\', '\\\\', $string);
    $string = str_replace('\'', '\\\'', $string);
    $string = str_replace('"', '\\"', $string);
    $string = str_replace("\f", '\\f', $string);
    $string = str_replace("\b", '\\b', $string);
    $string = str_replace("\r", '\\r', $string);
    $string = str_replace("\n", '\\n', $string);
    $string = str_replace("\t", '\\t', $string);

    return $string;
}

/**
 * Reindex Redlink triple store, enabling local entities to be found in future analyses.
 */
function wordlift_reindex_triple_store() {

    // Get the reindex URL.
    $url      = wordlift_redlink_reindex_url();

    // Post the request.
    write_log( "wordlift_reindex_triple_store\n" );
    $response = wp_remote_get( $url, array(
            'method'      => 'POST',
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.1',
            'blocking'    => true, // switched to not blocking.
            'sslverify'   => false,
            'cookies'     => array()
        )
    );

    // TODO: handle errors.
    if ( is_wp_error( $response ) || 200 !== $response['response']['code'] ) {

        write_log( "wordlift_reindex_triple_store: error\n" );
        write_log( var_export( $response, true ) );
        return false;
    }

    return true;
}

// hook save events.
add_action('save_post', 'wordlift_save_post');
add_action('wordlift_save_post', 'wordlift_save_post_and_related_entities');
add_action('wordlift_save_entity', 'wl_push_to_redlink');
