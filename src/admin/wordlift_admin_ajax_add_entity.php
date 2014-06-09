<?php

function wordlift_ajax_add_entity() {
    ob_clean();

    // Retrieve entity obj
    $entity = json_decode( file_get_contents('php://input'), true );
    
    $label = $entity['label'];
    $type =  $entity['type'];
    // Get the uri for this entity
    $uri = ''; // wl_sanitize_uri_path($label);
    wl_write_log( "wordlift_ajax_add_entity : go to create entity [ entity uri :: $uri ]" );
        
    // Set a blank description
    $description = '';

    // Create the entity on Wp / Redlink side
    // Notice: the $related_post_id is null here: 
    // the entity is created but not related to current post yet
    $post = wl_save_entity($uri, $label, $type, $description);

    // Serialize the entity post
    $post = wl_serialize_entity($post);
    // Return the new entity json object
    echo json_encode( $post );

    die();
}

if ( is_admin() ) {
    add_action( 'wp_ajax_wordlift_add_entity', 'wordlift_ajax_add_entity' );
}