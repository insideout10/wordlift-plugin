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
    $author_uri     = wl_get_user_uri( $post->post_author );

    // Get other post properties.
    $date_published = wl_get_sparql_time( get_the_time('c', $post) );
    $date_modified  = wl_get_sparql_time( wl_get_post_modified_time( $post ) );
    $title          = wordlift_esc_sparql( $post->post_title );
    $permalink      = wordlift_esc_sparql( get_permalink( $post->ID ) );
    $user_comments_count = $post->comment_count;

    write_log( "wl_push_post_to_redlink [ post_id :: $post->ID ][ type :: $post->post_type ][ slug :: $post->post_name ][ title :: $post->post_title ][ date modified :: $date_modified ][ date published :: $date_published ]" );

    // create the SPARQL query.
    $sparql  = "<$uri> rdfs:label '$title'@$site_language . \n";
    $sparql .= "<$uri> a <http://schema.org/BlogPosting> . \n";
    $sparql .= "<$uri> schema:url <$permalink> . \n";
    $sparql .= "<$uri> schema:datePublished $date_published . \n";
    $sparql .= "<$uri> schema:dateModified $date_modified . \n";
    if ( !empty( $author_uri ) ) {
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
    rl_execute_sparql_update_query($query);
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
    $types   = wl_get_entity_types( $entity_post->ID );

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
            $related_entity_uri = wordlift_esc_sparql( wl_get_entity_uri( $entity_post_id ) );
            // create a two-way relationship.
            $sparql .= " <$uri> dct:relation <$related_entity_uri> . \n";
            $sparql .= " <$related_entity_uri> dct:relation <$uri> . \n";
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

    rl_execute_sparql_update_query($query);
}

/**
 * Save the post to the triple store. Also saves the entities locally and on the triple store.
 * @param int $post_id The post id being saved.
 */
function wordlift_save_post_and_related_entities( $post_id ) {

    // Ignore auto-saves
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // get the current post.
    $post       = get_post( $post_id );

    // Don't process auto-drafts.
    if ( 'auto-draft' === $post->post_status ) {
        write_log( "wordlift_save_post_and_related_entities [ post id :: $post_id ][ auto-draft :: yes ]" );
        return;
    }

    // Delete trashed posts/entities from Redlink.
    if ( 'trash' === $post->post_status ) {
        write_log( "wordlift_save_post_and_related_entities [ post id :: $post_id ][ trash :: yes ]" );

        rl_delete_post( $post_id );
        return;
    }

    remove_action('wordlift_save_post', 'wordlift_save_post_and_related_entities');

    write_log( "wordlift_save_post_and_related_entities [ post id :: $post_id ][ autosave :: false ][ post type :: $post->post_type ]" );

    // Save the entities coming with POST data.
    if ( isset( $_POST['wl_entities'] ) ) {
        $entities_via_post = array_values( $_POST['wl_entities'] );

        write_log( "wordlift_save_post_and_related_entities [ entities_via_post :: " );
        write_log( $entities_via_post );
        write_log( "]" );

        wl_save_entities( $entities_via_post, $post_id );
    }

    // Save entities coming as embedded in the text.
    wordlift_save_entities_embedded_as_spans( $post->post_content, $post_id );

    // Push the post to Redlink.
    wl_push_to_redlink( $post->ID );

    add_action('wordlift_save_post', 'wordlift_save_post_and_related_entities');
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
 * @param string $content      The content.
 * @param int $related_post_id The post that references (or relates) to these entities.
 * @return array An array with the saved post IDs.
 */
function wordlift_save_entities_embedded_as_spans( $content, $related_post_id = null ) {

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
            $post  = wl_save_entity( $uri, $label, $type, '', array(), $related_post_id );
            if ( !in_array( $post->ID, $post_ids ) ) {
                array_push( $post_ids, $post->ID );
            }
        }
    }

    write_log( "wordlift_save_entities_embedded_as_spans [ entities count :: $count ]" );

    return $post_ids;
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
    $id  = wl_sanitize_uri_path( $post->post_title );

    // Build the entity URI.
    $url = sprintf(
        'http://data.redlink.io/%s/%s/%s/%s',
        wordlift_configuration_user_id(),
        wordlift_configuration_dataset_id(),
        $type,
        $id
    );

    write_log( "wordlift_build_entity_uri [ post_id :: $post->ID ][ type :: $post->post_type ][ title :: $post->post_title ][ url :: $url ]" );

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
    write_log( "wordlift_reindex_triple_store" );
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

        write_log( "wordlift_reindex_triple_store : error" );
        write_log( var_export( $response, true ) );
        return false;
    }

    return true;
}

// hook save events.
add_action('save_post', 'wordlift_save_post');
add_action('wordlift_save_post', 'wordlift_save_post_and_related_entities');
