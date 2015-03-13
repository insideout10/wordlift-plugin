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
            'div_id'     => $div_id
        )
    );
    
    return '<div id="' . $div_id . '" style="width:100%">
        Ciao Marcyyyyyyyyyy
    </div>';  
}
add_shortcode( 'wl-faceted-search', 'wl_shortcode_faceted_search' );


/*
 * Ajax call for the faceted search widget
 */
function wl_shortcode_faceted_search_ajax()
{

    if( ! isset( $_REQUEST['entity_id'] ) ) {
        echo 'No entity_id given';
        return;
    }
    
    $entity_id = $_REQUEST['entity_id'];
    
    if( isset( $_REQUEST['type'] ) ) {
        $required_type = $_REQUEST['type'];
    } else {
        $required_type = null;
    }

    $referencing_post_ids  = wl_get_referencing_posts( $entity_id );
    $second_degree_entities = array();
    $result = array();
    
    header( 'Content-Type: application/json' );

    if( $required_type == 'posts' ) {
        foreach ( $referencing_post_ids as $referencing_post_id ) {
            $result[] = get_post( $referencing_post_id );
        }
    } else {
        foreach( $referencing_post_ids as $referencing_post_id ) {
            $referenced = wl_get_referenced_entities( $referencing_post_id );
            $second_degree_entities = array_merge( $second_degree_entities, $referenced );
        }
        
        $second_degree_entities = array_unique( $second_degree_entities );
        foreach( $second_degree_entities as $second_degree_entity ) {
            $result[] = wl_serialize_entity( get_post( $second_degree_entity ) );
        }
    }
    
    echo json_encode( $result );
    wp_die();
}
add_action('wp_ajax_wl_faceted_search', 'wl_shortcode_faceted_search_ajax');
add_action('wp_ajax_nopriv_wl_faceted_search', 'wl_shortcode_faceted_search_ajax');

