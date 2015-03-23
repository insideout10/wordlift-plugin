<?php

/*
 * Function in charge of diplaying the [wl-faceted-search]
 */
function wl_shortcode_faceted_search( $atts ) {
    
    $div_id = 'wordlift-faceted-entity-search-widget';
    
    wp_enqueue_style( 'wordlift-faceted-search-css', plugins_url('css/wordlift-faceted-entity-search-widget.css', __FILE__) );
 
    wp_enqueue_script( 'angularjs', plugins_url( 'bower_components/angular/angular.min.js', __FILE__ ) );

    wp_enqueue_script( 'wordlift-faceted-search', plugins_url('js/wordlift-faceted-entity-search-widget.js', __FILE__) );
    wp_localize_script( 'wordlift-faceted-search', 'wl_faceted_search_params', array(
            'ajax_url'   => admin_url('admin-ajax.php'),
            'action'     => 'wl_faceted_search',
            'entity_id'  => get_the_ID(),
            'entity_uri'  => wl_get_entity_uri( get_the_ID() ),
            'div_id'     => $div_id
        )
    );
    
    return '<div id="' . $div_id . '" style="width:100%"></div>';  
}
add_shortcode( 'wl-faceted-search', 'wl_shortcode_faceted_search' );


/*
 * Ajax call for the faceted search widget
 */
function wl_shortcode_faceted_search_ajax()
{
    // Entity ID must be defined
    if( ! isset( $_REQUEST['entity_id'] ) ) {
        echo 'No entity_id given';
        return;
    }
    $entity_id = $_REQUEST['entity_id'];
    
    // Which type was requested?
    if( isset( $_REQUEST['type'] ) ) {
        $required_type = $_REQUEST['type'];
    } else {
        $required_type = null;
    }
    
    // Extract filtering conditions
    $request_body = file_get_contents('php://input');
    $filtering_entity_uris = json_decode( $request_body );    

    // Set up data structures
    $referencing_post_ids  = wl_get_referencing_posts( $entity_id );
    $second_degree_entities = array();
    $result = array();
    
    // Get ready to fire a JSON
    header( 'Content-Type: application/json' );

    if( $required_type == 'posts' ) {
        // Required filtered posts.
        
        if( empty( $filtering_entity_uris ) ) {
            // No filter, just get referencing posts
            foreach ( $referencing_post_ids as $referencing_post_id ) {
                $post_obj = get_post( $referencing_post_id );
                $post_obj->thumbnail = wp_get_attachment_url( get_post_thumbnail_id( $referencing_post_id, 'thumbnail' ) );

                $result[] = $post_obj;
            }
        } else {

            // Add the current post as default condition
            array_push( $filtering_entity_uris, wl_get_entity_uri( $entity_id ) );
            // Search posts that reference all the filtering entities.
            
            $meta_query = array( 'relation' => 'AND' );
               
            foreach( $filtering_entity_uris as $uri) {
                
                $id = wl_get_entity_post_by_uri( $uri );
                $id = $id->ID;
                
                $meta_query[] = array(
                    'key' => WL_CUSTOM_FIELD_REFERENCED_ENTITIES,
                    'value' => $id,
                    'compare' => '=='
                );
            }

            $query = new WP_Query();
            $filtered_posts = $query->query(
                array(
                    'post_type' => 'post',
                    'posts_per_page' =>-1,
                    'meta_query' => $meta_query
                )
            );
            
            $result = $filtered_posts;
        }
        
    } else {
        // Required second degree entities (i.e. e1 --> p2 --> e4)
        foreach( $referencing_post_ids as $referencing_post_id ) {
            $referenced = wl_get_referenced_entities( $referencing_post_id );
            $second_degree_entities = array_merge( $second_degree_entities, $referenced );
        }
        
        $second_degree_entities = array_unique( $second_degree_entities );
        foreach( $second_degree_entities as $second_degree_entity ) {
            $result[] = wl_serialize_entity( get_post( $second_degree_entity ) );
        }
    }
    
    // Output JSON and exit
    echo json_encode( $result );
    wp_die();
}
add_action('wp_ajax_wl_faceted_search', 'wl_shortcode_faceted_search_ajax');
add_action('wp_ajax_nopriv_wl_faceted_search', 'wl_shortcode_faceted_search_ajax');

