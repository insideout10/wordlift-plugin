<?php

/*
 * Function in charge of diplaying the [wl-faceted-search]
 */
function wl_shortcode_faceted_search( $atts ) {
    
    $div_id = 'wordlift-faceted-entity-search-widget';
    
    wp_enqueue_script( 'wordlift-faceted-search', plugins_url('js/wordlift.ui.min.js', __FILE__) );
    wp_localize_script( 'wordlift-faceted-search', 'wl_faceted_search_params', array(
            'ajax_url'   => admin_url('admin-ajax.php'),
            'action'     => 'wl_faceted_search',
            'entity_id'  => get_the_ID(),
            'div_id'     => $div_id
        )
    );
    
    echo '<div id="' . $div_id . '" style="width:100%">
        Ciao Marcyyyyyyyyyy
    </div>';  
}
add_shortcode( 'wl-faceted-search', 'wl_shortcode_faceted_search' );

