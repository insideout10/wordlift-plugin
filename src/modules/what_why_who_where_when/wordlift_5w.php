<?php
/*
 * Module adding to the article's post metas the '5 W' used in journalism.
 */

/**
 * Return the 5W of an article.
 *
 * @param int $post_id The post ID.
 *
 * @return array Dictionary containing the 5W uris.
 */
function wl_5w_get_all_article_Ws( $post_id ) {
    
    $w4 = array(WL_4W_WHAT, WL_4W_WHO, WL_4W_WHERE, WL_4W_WHEN);
    $result = array();
    
    foreach ( $w4 as $w ) {
        $result[$w] = wl_5w_get_article_w( $post_id, $w );
    }
    
    return $result;
}

/**
 * Return a W of an article.
 *
 * @param int $post_id The post ID.
 * @param string $w The W constant name.
 *
 * @return string The uri of the entity which corresponds to the W.
 */
function wl_5w_get_article_w( $post_id, $w ) {
    $values = get_post_meta( $post_id, $w );
    $uris = array();
    
    foreach( $values as $value ) {
        if( is_numeric( $value ) ) {
            $uris[] = wl_get_entity_uri( $value );
        } else {
            $uris[] = $value;
        }
    }
    return $uris;
}

/**
 * Set the W of an article.
 *
 * @param int $post_id The entity post ID.
 * @param string $w The W constant name.
 * @param mixed $value Uri or Id of the entity to save as meta.
 *
 * @return boolean True if everything went right, False otherwise.
 */
function wl_5w_set_article_w( $post_id, $w, $values ) {
    
    // Check that post exists.
    if( get_post_status( $post_id ) == false ) {
        return false;
    }
    
    // In case of single value, force it into an array.
    if( !is_array( $values ) ) {
        $values = array( $values );
    }
    
    // save metas
    foreach( $values as $value ) {
        add_post_meta( $post_id, $w, $value );
    }
    
    return true;
}

/**
 * Get 5w values via AJAX
 *
 */
/*function wl_5w_get_article_Ws_ajax()
{
    // Get the post Id.
    if( isset( $_REQUEST['post_id'] ) ) {
        $post_id = $_REQUEST['post_id'];
    } else {
        wp_die();
    }

    ob_clean();
    header( "Content-Type: application/json" );

    echo json_encode( wl_5w_get_all_article_Ws( $post_id ) );
    wp_die();
}
add_action( 'wp_ajax_wl_5w', 'wl_5w_get_article_Ws_ajax' );
add_action( 'wp_ajax_nopriv_wl_5w', 'wl_5w_get_article_Ws_ajax' );
 */


