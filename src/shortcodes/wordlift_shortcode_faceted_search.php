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
add_shortcode( 'wl_faceted_search', 'wl_shortcode_faceted_search' );


/*
 * Ajax call for the faceted search widget
 */
function wl_shortcode_faceted_search_ajax()
{
    // Entity ID must be defined
    if( ! isset( $_GET['entity_id'] ) ) {
        echo 'No entity_id given';
        return;
    }
    $entity_id = $_GET['entity_id'];
    
    // Which type was requested?
    if( isset( $_GET['type'] ) ) {
        $required_type = $_GET['type'];
    } else {
        $required_type = null;
    }
    
    // Extract filtering conditions
    $request_body = file_get_contents('php://input');
    $filtering_entity_uris = json_decode( $request_body );    

    // Set up data structures
    $referencing_post_ids  = wl_get_referencing_posts( $entity_id );
    $result = array();
    
    // Get ready to fire a JSON
    header( 'Content-Type: application/json' );

    if ( 'posts' == $required_type ) {
        // Required filtered posts.
        wl_write_log( "Going to find related posts for the current entity [ entity ID :: $entity_id ]" );

        if ( empty( $filtering_entity_uris ) ) {
            // No filter, just get referencing posts
            foreach ( $referencing_post_ids as $referencing_post_id ) {
                
                $post_obj = get_post( $referencing_post_id );
                
                $thumbnail = wp_get_attachment_url( get_post_thumbnail_id( $referencing_post_id, 'thumbnail' ) );
                $post_obj->thumbnail = ( $thumbnail ) ? 
                    $thumbnail : plugins_url( 'js-client/slick/missing-image-150x150.png', __FILE__ );

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
            
            foreach ( $filtered_posts as $post_obj ) {
                
                $thumbnail = wp_get_attachment_url( get_post_thumbnail_id( $post_obj->ID, 'thumbnail' ) );
                $post_obj->thumbnail = ( $thumbnail ) ? 
                    $thumbnail : plugins_url( 'js-client/slick/missing-image-150x150.png', __FILE__ );

                $result[] = $post_obj;
            }
            $result = $filtered_posts;
        }
        
    } else {
        
        global $wpdb;

        wl_write_log( "Going to find related entities for the current entity [ entity ID :: $entity_id ]" );

        $meta_key_name = 'wordlift_related_entities';
        $ids = implode(',', $referencing_post_ids);

        $query = <<<EOF
            SELECT meta_value as ID, count(meta_value) as counter FROM $wpdb->postmeta 
                where meta_key = %s 
                and post_id IN ($ids) 
                group by meta_value;
EOF;
        wl_write_log( "Going to find related entities for the current entity [ entity ID :: $entity_id ] [ query :: $query ]" );        

        $query = $wpdb->prepare( $query, $meta_key_name );
        $entities = $wpdb->get_results( $query, OBJECT );

        wl_write_log( "Entities found " . count( $entities ) );        

        foreach( $entities as $obj ) {
            
            $entity = get_post( $obj->ID );
            $entity = wl_serialize_entity( $entity );
            $entity['counter'] = $obj->counter;
            $result[] = $entity;

        }

    }
    
    // Output JSON and exit
    echo json_encode( $result );
    wp_die();
}
add_action('wp_ajax_wl_faceted_search', 'wl_shortcode_faceted_search_ajax');
add_action('wp_ajax_nopriv_wl_faceted_search', 'wl_shortcode_faceted_search_ajax');

