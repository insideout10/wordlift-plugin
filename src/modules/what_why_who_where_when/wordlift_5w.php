<?php
/*
 * Module adding to the article's post metas the '5 W' used in journalism.
 */

/**
 * Return the 5W of an article.
 *
 * @param int $post_id The post ID.
 *
 * @return array Dictionary containing the 5W.
 */
function wl_5w_get_all_article_5w( $post_id ) {
    
    $w5 = array(WL_5W_WHAT, WL_5W_WHY, WL_5W_WHO, WL_5W_WHERE, WL_5W_WHEN);
    $result = array();
    
    foreach ( $w5 as $w ) {
        $result[$w] = wl_w5_get_article_w( $post_id, $w );
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
    return get_post_meta( $post_id, $w );
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
function wl_5w_set_article_w( $post_id, $w, $value ) {
    
    // Check if entity and article exists
    //if( wl_get )
    //    return false;
    
    add_post_meta( $post_id, $w, $value );
    
    return true;
}


