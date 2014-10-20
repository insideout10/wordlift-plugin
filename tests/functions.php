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

// Disable buffering.
define( 'WL_BUFFER_SPARQL_UPDATE_QUERIES', false );

/**
 * Create a new post.
 *
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
        'post_status'  => $status,
        'post_type'    => $type,
        'post_author'  => wl_test_create_user()
    );

    if ( ! empty( $title) ) {
        $args['post_title'] = $title;
    }

    $wp_error = null;
    $post_id  = wp_insert_post( $args, $wp_error );

    if ( is_wp_error( $wp_error ) ) {
        return $wp_error;
    }

    return $post_id;
}

/**
 * Delete the post and related attachments with the specified id (it's basically a proxy to wp_delete_post).
 * @param int $post_id       The post id.
 * @param bool $force_delete Whether to force delete.
 * @return false|WP_Post False on failure and the post object for the deleted post success.
 */
function wl_delete_post( $post_id, $force_delete = false ) {

    // First delete the post attachments.
    wl_delete_post_attachments( $post_id );

    // Now delete the post and return the result.
    return wp_delete_post( $post_id, $force_delete );
}

/**
 * Delete the attachments related to the specified post.
 * @param $post_id
 */
function wl_delete_post_attachments( $post_id ) {

    // Get all the attachments related to the post.
    $attachments = wl_get_attachments( $post_id );

    // Delete each attachment.
    foreach ( $attachments as $attachment ) {
        if ( false === wp_delete_attachment( $attachment->ID ) ) {
            wl_write_log( "wl_delete_post_attachments : error [ post id :: $post_id ]" );
        }
    }
}

/**
 * Update the content of the post with the specified ID.
 * @param int $post_id    The post ID.
 * @param string $content The post content.
 * @return int|WP_Error The post ID in case of success, a WP_Error in case of error.
 */
function wl_update_post( $post_id, $content ) {

    wl_write_log( "wl_update_post [ post id :: $post_id ][ content ::\n $content\n ]" );

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
        $success &= wl_delete_post( $post->ID, true );
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
    if ( is_wp_error( $response )
        || 200 !== $response['response']['code'] ) {

        echo "wl_analyze_content ====================================\n";
//        echo "[ api url :: $api_url ]\n"; -- enabling this will print out the key.
        echo " request : \n";
        var_dump( $args );
        echo " response: \n";
        var_dump( $response );
        echo " response body: \n";
        echo $response['body'];
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
        $replace    = "$1<span class=\"textannotation\" id=\"$id\">$2</span>$3";
//        $replace    = "$1<span class=\"textannotation\" id=\"$id\" typeof=\"http://fise.iks-project.eu/ontology/TextAnnotation\">$2</span>$3";

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

//        echo "[ text annotation :: " . $text_annotation['id'] . "][ entities count :: " . count( $entities ) . " ]\n";

        $entity_annotation = wl_get_entity_annotation_best_match( $entities );

        // Get the entity, its ID and type.
        $entity       = $entity_annotation['entity'];
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
 * Get the entity description from the available fields.
 * @param object $entity An entity instance.
 * @return string The entity description.
 */
function wl_get_entity_description( $entity ) {

    // Return the description from the rdfs:comment field.
    if ( isset( $entity->{'http://www.w3.org/2000/01/rdf-schema#comment'} ) ) {
        return $entity->{'http://www.w3.org/2000/01/rdf-schema#comment'}->{'@value'};
    }

    // Return the description from the Freebase common.topic.description field.
    if ( isset( $entity->{'http://rdf.freebase.com/ns/common.topic.description'} ) ) {
        return $entity->{'http://rdf.freebase.com/ns/common.topic.description'}->{'@value'};
    }

    return '';
}

/**
 * Get the entity thumbnails as an array of URLs.
 * @param object $entity An entity instance.
 * @return array An array of URLs.
 */
function wl_get_entity_thumbnails( $entity ) {

    $images = array();

    // Add the images from the foaf:depiction attribute.
    if ( isset( $entity->{'http://xmlns.com/foaf/0.1/depiction'} ) ) {
        if ( is_array( $entity->{'http://xmlns.com/foaf/0.1/depiction'} ) ) {
            foreach ( $entity->{'http://xmlns.com/foaf/0.1/depiction'} as $image ) {
                array_push( $images, $image->{'http://xmlns.com/foaf/0.1/depiction'}->{'@id'} );
            }
        } else {
            array_push( $images, $entity->{'http://xmlns.com/foaf/0.1/depiction'}->{'@id'} );
        }
    }

    // Convert the URL provided by Freebase to image URLs:
    // see https://developers.google.com/freebase/v1/topic-response#references-to-image-objects
    if ( isset( $entity->{'http://rdf.freebase.com/ns/common.topic.image'} ) ) {
        if ( is_array( $entity->{'http://rdf.freebase.com/ns/common.topic.image'} ) ) {
            foreach ( $entity->{'http://rdf.freebase.com/ns/common.topic.image'} as $image ) {

                $image_url = wl_freebase_image_url( $image->{'@id'} );

                if ( !empty( $image_url ) ) {
                    array_push( $images, $image_url );
                }
            }
        } else {
            $image_url = wl_freebase_image_url( $entity->{'http://rdf.freebase.com/ns/common.topic.image'}->{'@id'} );

            if ( !empty( $image_url ) ) {
                array_push( $images, $image_url );
            }
        }
    }

    return $images;
}

/**
 * Get an image URL from a link (see https://developers.google.com/freebase/v1/topic-response#references-to-image-objects).
 * @param string $image_link An image link.
 * @return string|null The image URL or null in case of failure.
 */
function wl_freebase_image_url( $image_link ) {

    // http://rdf.freebase.com/ns/m.0kyblb5
    // https://usercontent.googleapis.com/freebase/v1/image/m/0kyblb5

    $matches = array();
    if ( 1 === preg_match( '/m\.([\w\d]+)$/i', $image_link, $matches ) ) {
        $id = $matches[1];
        return "https://usercontent.googleapis.com/freebase/v1/image/m/$id?maxwidth=4096&maxheight=4096";
    };

    return null;
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
 * Get a types array from an item.
 * @param object|array|string $item An item with a '@type' property (if the property doesn't exist, an empty array is returned).
 * @return array The items array (or an empty array if the '@type' property doesn't exist).
 */
function wl_type_to_types($item)
{

    if (is_string($item)) {
        return array($item);
    }

    if (is_array($item)) {
        return $item;
    }

    return !isset($item->{'@type'})
        ? array() // Set an empty array if type is not set on the item.
        : (is_array($item->{'@type'}) ? $item->{'@type'} : array($item->{'@type'}));
}

///**
// * Bind the specified post and entities together.
// * @param int $post_id The post ID.
// * @param array $entity_posts An array of entity posts or post IDs.
// */
//function wl_bind_post_to_entities($post_id, $entity_posts)
//{
//
//    // Get the entity IDs.
//    $entity_ids = array();
//    foreach ($entity_posts as $entity_post) {
//        // Support both an array of posts or an array of post ids.
//        $entity_post_id = (is_numeric($entity_post) ? $entity_post : $entity_post->ID);
//        array_push($entity_ids, $entity_post_id);
//
//        // Set the related posts.
//        wl_add_related_posts($entity_post_id, array($post_id));
//    }
//
//    wl_add_referenced_entities( $post_id, $entity_ids );
//}


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

/**
 * Parse the analysis result from a file.
 * @param string $filename The file containing a JSON-LD analysis response.
 * @return array|null An array with the analysis result, or null in case of failure.
 */
function wl_parse_file( $filename ) {

    $analysis = file_get_contents( $filename );

    // Decode the string response to a JSON.
    $json     = json_decode( $analysis );

    // Parse the JSON to get the analysis results.
    return wl_parse_response( $json );
}

/**
 * Get the entity annotation with the best match from the provided entity annotations array.
 * @param array $entity_annotations An array of entities.
 * @return array An entity annotation array.
 */
function wl_get_entity_annotation_best_match( $entity_annotations ) {

    // Sort array by confidence.
    usort( $entity_annotations, function ( $a, $b ) {
        if ( $a['confidence'] == $b['confidence']) {
            return 0;
        }
        return ( $a['confidence'] > $b['confidence'] ) ? -1 : 1;
    });

    return $entity_annotations[0];
}

function wl_execute_sparql_query( $query ) {

    // construct the API URL.
    $url  = wordlift_redlink_sparql_update_url();

    // Prepare the request.
    $args = array_merge_recursive( unserialize( WL_REDLINK_API_HTTP_OPTIONS ) , array(
        'method'  => 'POST',
        'headers' => array(
            'Accept'       => 'application/json',
            'Content-type' => 'application/sparql-update; charset=utf-8'
        ),
        'body'    => $query
    ));

    // Send the request.
    $response = wp_remote_post( $url, $args );

    // If an error has been raised, return the error.
    if ( is_wp_error( $response ) || 200 !== $response['response']['code'] ) {

        echo "wl_execute_sparql_query ================================\n";
//        echo "[ api url :: $api_url ]\n"; -- enabling this will print out the key.
        echo " request : \n";
        var_dump( $args );
        echo " response: \n";
        var_dump( $response );
        echo " response body: \n";
        echo $response['body'];
        echo "=======================================================\n";

        return false;
    }

    return true;
}

/**
 * Erase all posts and entity posts from the blog.
 */
function wl_empty_blog() {

    // Delete existing pages, posts and entities.
    wl_delete_posts( get_posts( array(
        'posts_per_page' => -1,
        'post_type'      => array(
            'post',
            'page',
            'entity'
        ),
        'post_status'    => 'any'
    ) ) );

    wl_delete_users();

}

/**
 * Delete all the users from the blog.
 */
function wl_delete_users() {

    $users = get_users();
    foreach ( $users as $user ) {
        wp_delete_user( $user->ID );
    }
}

/**
 * Get the attachments for the specified post ID.
 * @param int $post_id The post ID.
 * @return array An array of attachments.
 */
function wl_get_attachments( $post_id ) {

    return get_posts( array(
        'post_type'      => 'attachment',
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'post_parent'    => $post_id
    ) );
}

/**
 * Echo the log data to the terminal.
 *
 * @param $log The log data.
 */
function wl_test_write_log_handler( $log ) {

    if ( is_array( $log ) || is_object( $log ) ) {
        echo( print_r( $log, true ) . "\n");
    } else {
        echo( $log . "\n" );
    }

}

/**
 * Set the log handler function name.
 *
 * @return string
 */
function wl_test_get_write_log_handler() {

    return 'wl_test_write_log_handler';

}

/**
 * Configure WordPress with the test settings (may vary according to the local PHP and WordPress versions).
 */
function wl_configure_wordpress_test() {

    add_filter( 'wl_write_log_handler', 'wl_test_get_write_log_handler' );

    do_action( 'activate_wordlift/wordlift.php' );

    // Set the dataset name to the specified dataset or define it based on the current environment.
    $dataset_name = ( false !== getenv('REDLINK_DATASET_NAME') ? getenv('REDLINK_DATASET_NAME')
        : str_replace('.', '-',
            sprintf( '%s-php-%s.%s-wp-%s-ms-%s', 'wordlift-tests', PHP_MAJOR_VERSION, PHP_MINOR_VERSION,
                getenv('WP_VERSION'), getenv('WP_MULTISITE') ) )
    );
    
    $app_name = ( false !== getenv('REDLINK_APP_NAME') ? getenv('REDLINK_APP_NAME') : 'wordlift' );

    // Set the plugin options.
    update_option( WL_OPTIONS_NAME, array(
        'application_key' => getenv('REDLINK_APP_KEY'),
        'user_id'         => getenv('REDLINK_USER_ID'),
        'dataset_name'    => $dataset_name,
        'analysis_name'   => $app_name,
        'dataset_base_uri' => 'http://data.redlink.io/' . getenv('REDLINK_USER_ID') . '/' . $dataset_name
    ) );
}

/**
 * Create a test user.
 * @return int|WP_Error
 */
function wl_test_create_user() {

    return wp_insert_user( array(
        'user_login' => uniqid('user-'),
        'user_pass'  => 'tmppass',
        'first_name' => 'Mario',
        'last_name'  => 'Rossi'
    ) );
}