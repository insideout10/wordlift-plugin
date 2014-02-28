<?php
/**
 * This file contains support functions for the tests.
 */

// Define the JSON-LD contexts.
define( 'WL_ENHANCER_NAMESPACE',    'a' );
define( 'WL_DUBLIN_CORE_NAMESPACE', 'b' );
define( 'WL_JSON_LD_CONTEXT', serialize( array(
    WL_ENHANCER_NAMESPACE    => 'http://fise.iks-project.eu/ontology/',
    WL_DUBLIN_CORE_NAMESPACE => 'http://purl.org/dc/terms/'
) ) );

/**
 * Create a new post.
 * @param string $content The post content.
 * @param string $slug    The post slug.
 * @param string $title   The post title.
 * @param string $status  The post status (e.g. draft, publish, pending, private, ...)
 * @param string $type    The post status (e.g. post, page, link, ...)
 * @return int|WP_Error The post ID or a WP_Error instance.
 */
function wl_create_post( $content, $slug, $title, $status = 'draft', $type = 'post' ) {

    $args = array(
        'post_content' => $content,
        'post_name'    => $slug,
        'post_title'   => $title,
        'post_status'  => $status,
        'post_type'    => $type
    );

    $wp_error = null;
    $post_id  = wp_insert_post( $args, $wp_error );

    if ( is_wp_error( $wp_error ) ) {
        return $wp_error;
    }

    return $post_id;
}

/**
 * Delete the post with the specified id.
 * @param int $post_id The post id.
 * @return false|WP_Post False on failure and the post object for the deleted post success.
 */
function wl_delete_post( $post_id ) {

    return wp_delete_post( $post_id );

}

/**
 * Update the content of the post with the specified ID.
 * @param int $post_id    The post ID.
 * @param string $content The post content.
 * @return int|WP_Error The post ID in case of success, a WP_Error in case of error.
 */
function wl_update_post( $post_id, $content ) {

    $wp_error = null;
    $args = array(
        'ID'           => $post_id,
        'post_content' => $content
    );

    // Return WP_Error in case of errors.
    return wp_update_post( $args, true );
}

/**
 * Get a post with the provided ID.
 * @param int $post_id The post ID.
 * @return null|WP_Post Returns a WP_Post object, or null on failure.
 */
function wl_get_post( $post_id ) {

    return get_post( $post_id );
}

/**
 * Delete permanently the provided posts.
 * @param array $posts An array of posts.
 * @return bool True if successful otherwise false.
 */
function wl_delete_posts( $posts ) {

    $success = true;

    foreach ( $posts as $post ) {
        $success &= wp_delete_post( $post->ID, true );
    }

    return $success;
}

/**
 * Analyze the post with the specified ID. The analysis will make use of the method *wordlift_ajax_analyze_action*
 * provided by the WordLift plugin.
 * @param int $post_id The post ID to analyze.
 * @return null|WP_Error|WP_Response Returns null on failure, or the WP_Error, or a WP_Response with the response.
 */
function wl_analyze_post( $post_id ) {

    // Get the post contents.
    $post     = wl_get_post( $post_id );
    if ( null === $post ) {
        return null;
    }
    $content  = $post->post_content;

    return wl_analyze_content( $content );
}

/**
 * Analyze the provided content. The analysis will make use of the method *wordlift_ajax_analyze_action*
 * provided by the WordLift plugin.
 * @param string $content The content to analyze.
 * @return null|WP_Error|WP_Response Returns null on failure, or the WP_Error, or a WP_Response with the response.
 */
function wl_analyze_content( $content ) {

    // Get the URL of the Redlink enhancer.
    $api_url  = wordlift_redlink_enhance_url();

    // Prepare the request.
    $args     = array_merge_recursive( unserialize( WL_REDLINK_API_HTTP_OPTIONS ) , array(
        'method'  => 'POST',
        'headers' => array(
            'Accept'       => 'application/json',
            'Content-Type' => 'text/plain; charset=utf-8',
        ),
        'body'    => $content
    ));

    // Send the request.
    $response = wp_remote_post( $api_url, $args );

    // If an error has been raised, return the error.
    if ( is_wp_error( $response ) ) {

        echo "wl_analyze_content ====================================\n";
        var_dump( $response );
        echo "=======================================================\n";

        return $response;
    }

    // Return the response.
    return $response;

}

/**
 * Embed the analysis results in the post content. It should match what happens client-side with the related function
 * in the app.services.EditorService.coffee file.
 * @param array $results  The analysis results.
 * @param string $content The post content.
 * @return string The content with the annotations embedded.
 */
function wl_embed_text_annotations( $results, $content ) {

    // Then get the related entities via the entity-annotations.
    foreach ( $results['text_annotations'] as $item ) {
        $id         = $item['id'];
        $sel_prefix = wl_clean_up_regex( substr( $item['sel_prefix'], -2 ) );
        $sel_suffix = wl_clean_up_regex( substr( $item['sel_suffix'], 0, 2 ) );
        $sel_text   = $item['sel_text'];

        $pattern    = "/($sel_prefix(?:<[^>]+>){0,})($sel_text)((?:<[^>]+>){0,}$sel_suffix)(?![^<]*\"[^<]*>)/i";
        $replace    = "$1<span class=\"textannotation\" id=\"$id\" typeof=\"http://fise.iks-project.eu/ontology/TextAnnotation\">$2</span>$3";

//        echo "[ id :: $id ]\n";
//        echo "[ sel_prefix :: $sel_prefix ]\n";
//        echo "[ sel_suffix :: $sel_suffix ]\n";
//        echo "[ sel_text :: $sel_text ]\n";
//        echo "[ pattern :: $pattern ]\n";
//        echo "[ replace :: $replace ]\n";
//        echo "[ content length (before) :: " . strlen( $content ) . " ]\n";

        // Update the content.
        $content    = preg_replace( $pattern, $replace, $content );

//        echo "[ content length (after) :: " . strlen( $content ) . " ]\n";
    }

    return $content;
}

/**
 * @param $results
 * @param $content
 * @return null Null in case of failure.
 */
function wl_embed_entities( $results, $content ) {

    // Prepare the regex pattern.
    $pattern = '/<span class="textannotation" id="([^"]+)"[^>]*>([^<]+)<\/span>/im';
    // This var will contain the output matches.
    $matches = array();

    // Return null if no match found.
    if ( false === preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER ) ) {
        return null;
    }

    // For each match, embed the related entity.
    foreach ( $matches as $match ) {
        $full = $match[0];
        $id   = $match[1];
        $text = $match[2];

//        echo "[ id :: $id ][ text :: $text ]\n";

        $text_annotation = $results['text_annotations'][$id];
        $entities        = $text_annotation['entities'];

        // Sort array by confidence.
        usort( $entities, function ( $a, $b ) {
            if ( $a['confidence'] == $b['confidence']) {
                return 0;
            }
            return ( $a['confidence'] > $b['confidence'] ) ? -1 : 1;
        });

//        echo "[ text annotation :: " . $text_annotation['id'] . "][ entities count :: " . count( $entities ) . " ]\n";

        // Get the entity, its ID and type.
        $entity       = $entities[0]['entity'];
        $entity_id    = $entity->{'@id'};
        $entity_type  = wl_get_entity_type( $entity );
        $entity_class = $entity_type['class'];
        $entity_type_uri = $entity_type['uri'];

        // Create the new span with the entity reference.
        $replace      = '<span class="textannotation ' . $entity_class . '" ' .
            'id="' . $id . '" ' .
            'itemid="' . $entity_id . '" ' .
            'itemscope="itemscope" ' .
            'itemtype="' . $entity_type_uri . '">' .
            '<span itemprop="name">' . htmlentities( $text ) . '</span></span>';
        $content      = str_replace( $full, $replace, $content );


//        echo "[ id :: $id ]\n";
//        echo "[ sel_prefix :: $sel_prefix ]\n";
//        echo "[ sel_suffix :: $sel_suffix ]\n";
//        echo "[ sel_text :: $sel_text ]\n";
//        echo "[ pattern :: $pattern ]\n";
//        echo "[ replace :: $replace ]\n";
//        echo "[ content length (before) :: " . strlen( $content ) . " ]\n";
//        echo "[ entity id :: $entity_id ][ entity type :: $entity_type ]\n";

    }

    return $content;
}

/**
 * Get the URI and stylesheet class associated with the provided entity.
 * @param object $entity An entity instance.
 * @return array An array containing a class and an URI element.
 */
function wl_get_entity_type( $entity ) {

    // Prepare the types array.
    $types = wl_type_to_types( $entity );

    if ( in_array( 'http://schema.org/Person', $types )
        || in_array( 'http://rdf.freebase.com/ns/people.person', $types )) {
        return array(
            'class' => 'person',
            'uri'   => 'http://schema.org/Person'
        );
    }

    if ( in_array( 'http://schema.org/Organization', $types )
        || in_array( 'http://rdf.freebase.com/ns/government.government', $types )
        || in_array( 'http://schema.org/Newspaper', $types ) ) {
        return array(
            'class' => 'organization',
            'uri'   => 'http://schema.org/Organization'
        );
    }

    if ( in_array( 'http://schema.org/Place', $types )
        || in_array( 'http://rdf.freebase.com/ns/location.location', $types ) ) {
        return array(
            'class' => 'place',
            'uri'   => 'http://schema.org/Place'
        );
    }

    if ( in_array( 'http://schema.org/Event', $types )
        || in_array( 'http://dbpedia.org/ontology/Event', $types ) ) {
        return array(
            'class' => 'event',
            'uri'   => 'http://schema.org/Event'
        );
    }

    if ( in_array( 'http://rdf.freebase.com/ns/music.artist', $types )
        || in_array( 'http://schema.org/MusicAlbum', $types ) ) {
        return array(
            'class' => 'event',
            'uri'   => 'http://schema.org/Event'
        );
    }


    if ( in_array( 'http://www.opengis.net/gml/_Feature', $types ) ) {
        return array(
            'class' => 'place',
            'uri'   => 'http://schema.org/Place'
        );
    }

    return array(
        'class' => 'thing',
        'uri'   => 'http://schema.org/Thing'
    );
}

/**
 * Clean up a string to be used for a regex fragment.
 * @param string $fragment The fragment to cleanup.
 * @return mixed The cleaned up fragment.
 */
function wl_clean_up_regex( $fragment ) {
    $fragment = str_replace( '\\', '\\\\', $fragment );
    $fragment = str_replace( '\(', '\\(', $fragment );
    $fragment = str_replace( '\)', '\\)', $fragment );
    $fragment = str_replace( '\n', '\\n?', $fragment );
    $fragment = str_replace( '-', '\\-', $fragment );
    $fragment = str_replace( '\x20', '\\s', $fragment );
    $fragment = str_replace( '\xa0', '&nbsp;', $fragment );

    return $fragment;
}

/**
 * Parse the string representation of the JSON-LD response from the analysis service.
 * @param string $json A string representation in JSON-LD format.
 * @return array|null Null in case of failure, otherwise an array with Text Annotations, Entity Annotations and
 * Entities.
 */
function wl_parse_response( $json ) {

    // Check that the provided param is an object.
    if ( !is_object( $json ) ) {
        return null;
    }

    // Define the context for compacting.
    $context = (object)unserialize( WL_JSON_LD_CONTEXT );

    // Compact the JSON-LD.
    $jsonld  = jsonld_compact( $json, $context );

    // Get the entity annotations indexed by the textannotation reference.
    $entity_annotations = array();
    // Text Annotations are index by their ID.
    $text_annotations   = array();
    // Entities are indexed by their ID.
    $entities           = array();
    foreach ( $jsonld->{'@graph'} as $item ) {
        $types = wl_type_to_types( $item );
        // Entity Annotation.
        if ( in_array( WL_ENHANCER_NAMESPACE . ':EntityAnnotation', $types ) ) {
            array_push( $entity_annotations, $item );
        }
        // Text Annotation.
        else if ( in_array( WL_ENHANCER_NAMESPACE. ':TextAnnotation', $types ) ) {

            // Skip Text Annotations that do not have the selection-prefix, -suffix and selected-text.
            if ( isset( $item->{ WL_ENHANCER_NAMESPACE . ':selection-prefix' }->{'@value'} )
                && isset( $item->{ WL_ENHANCER_NAMESPACE . ':selection-suffix' }->{'@value'} )
                && isset( $item->{ WL_ENHANCER_NAMESPACE . ':selected-text' }->{'@value'} ) ) {

                $text_annotations[$item->{'@id'}] = array(
                    '_'          => $item,
                    'id'         => $item->{ '@id' },
                    'sel_prefix' => $item->{ WL_ENHANCER_NAMESPACE . ':selection-prefix' }->{'@value'},
                    'sel_suffix' => $item->{ WL_ENHANCER_NAMESPACE . ':selection-suffix' }->{'@value'},
                    'sel_text'   => $item->{ WL_ENHANCER_NAMESPACE . ':selected-text' }->{'@value'},
                    'entities'   => array() // will hold the entities referenced by this text-annotation.
                );
            }
        }
        // Entity
        else {
            $entities[$item->{'@id'}] = $item;
        }
    }

//    echo '[ $entity_annotations :: ' . count( $entity_annotations ) . ' ]';
//    echo '[ $text_annotations :: ' . count( $text_annotations ) . ' ]';
//    echo '[ $entities :: ' . count( $entities ) . ' ]';

    // Bind the entities to each text annotation via the entity annotation.
    foreach ( $entity_annotations as $item ) {
        // The relation to a Text Annotation.
        $relation         = (string)$item->{ WL_DUBLIN_CORE_NAMESPACE . ':relation'}->{'@id'};
        // The reference to an entity.
        $entity_reference = (string)$item->{ WL_ENHANCER_NAMESPACE . ':entity-reference'}->{'@id'};
        // Get the confidence for the match.
        $confidence       = $item->{ WL_ENHANCER_NAMESPACE . ':confidence' }->{'@value'};

//        echo "[ relation :: $relation ][ reference :: $entity_reference ]\n";

        // Get the Text Annotation (by ref).
        $text_annotation  = &$text_annotations[$relation];
        // Get the Entity (by ref)
        $entity           = &$entities[$entity_reference];

//        echo "[ entity null :: " . is_null( $entity ) . " ]\n";

        // Add the entity to the text annotation entities array.
        array_push( $text_annotation['entities'], array(
            'entity'     => $entity,
            'confidence' => $confidence
        ));
    }

    return array(
        'text_annotations'   => $text_annotations,
        'entity_annotations' => $entity_annotations,
        'entities'           => $entities
    );
}

/**
 * Get a types array from an item.
 * @param object $item An item with a '@type' property (if the property doesn't exist, an empty array is returned).
 * @return array The items array (or an empty array if the '@type' property doesn't exist).
 */
function wl_type_to_types( $item ) {

    return !isset( $item->{'@type'} )
        ? array() // Set an empty array if type is not set on the item.
        : ( is_array( $item->{'@type'} ) ? $item->{'@type'} : array( $item->{'@type'} ) );
}

/**
 * Get the IDs of posts related to the specified post.
 * @param int $post_id The post ID.
 * @return array An array of posts related to the one specified.
 */
function wl_get_related_post_ids( $post_id ) {

    // Get the related array (single _must_ be true, refer to http://codex.wordpress.org/Function_Reference/get_post_meta)
    $related = get_post_meta( $post_id, 'wordlift_related_posts', true );

    // Ensure an array is returned.
    return ( is_array( $related )
        ? $related
        : array( $related ) );
}

/**
 * Get the IDs of entities related to the specified post.
 * @param int $post_id The post ID.
 * @return array An array of posts related to the one specified.
 */
function wl_get_related_entities( $post_id ) {

    // Get the related array (single _must_ be true, refer to http://codex.wordpress.org/Function_Reference/get_post_meta)
    $related = get_post_meta( $post_id, 'wordlift_related_entities', true );

    // Ensure an array is returned.
    return ( is_array( $related )
        ? $related
        : array( $related ) );
}

/**
 * Get the author URI.
 * @param int $author_id The author ID.
 * @return string The author URI.
 */
function rl_get_author_url( $author_id ) {

    // Build the entity URI.
    $url = sprintf(
        'http://data.redlink.io/%s/%s/author/%s',
        wordlift_configuration_user_id(),
        wordlift_configuration_dataset_id(),
        $author_id
    );

    return $url;
}

/**
 * Get an array of entity URIs given their post IDs.
 * @param array $post_ids The post IDs.
 * @return array An array of entity URIs.
 */
function wl_post_ids_to_entity_uris( $post_ids ) {

    $uris = array();
    foreach ( $post_ids as $id ) {
        array_push( $uris, wl_get_entity_uri( $id ) );
    }

    return $uris;
}