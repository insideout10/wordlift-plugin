<?php
/**
 * This file contains content-related functions.
 */

/**
 * Lift the post content with the microdata.
 * @param string $content The post content.
 * @return string The updated post content.
 */
function wl_content_embed_microdata( $content )
{

    // Apply microdata only to single pages.
    if ( ! is_single() ) {
        wl_write_log( "wl_content_embed_microdata : is not single" );
        return $content;
    }

    global $post;

    return _wl_content_embed_microdata( $post->ID, $content );
}

/**
 * Lift the post content with the microdata (skipping the is_single check).
 *
 * @param int $post_id The post ID.
 * @param string $content The post content.
 * @return string The updated post content.
 */
function _wl_content_embed_microdata( $post_id , $content) {

    $regex   = '/<(\\w+)[^<]* itemid=\"([^"]+)\"[^>]*>([^<]*)<\\/\\1>/i';

    $matches = array();

    // Return the content if not item IDs have been found.
    if ( false === preg_match_all( $regex, $content, $matches, PREG_SET_ORDER ) ) {
        return $content;
    };

    foreach ( $matches as $match ) {
        $item_id = $match[2];

        wl_write_log( "_wl_content_embed_microdata [ item ID :: $item_id ]" );

        $content = wl_content_embed_item_microdata( $content, $item_id );
    }

    return $content;
}

/**
 * Embed the entity properties as microdata in the content.
 * @param string $content A content.
 * @param string $uri An entity URI.
 * @return string The content with embedded microdata.
 */
function wl_content_embed_item_microdata( $content, $uri ) {

    $post = wl_get_entity_post_by_uri( $uri );

    // Entity not found.
    if ( null === $post ) {

        wl_write_log( "wl_content_embed_item_microdata : post not found [ uri :: $uri ]" );
        return $content;
    }

    // Get the entity URI and its escaped version for the regex.
    $entity_uri = wl_get_entity_uri( $post->ID );

    // Get the array of sameAs uris.
    $same_as_uris = wl_get_same_as( $post->ID );

    // Prepare the sameAs fragment.
    $same_as = '';
    foreach ($same_as_uris as $same_as_uri) {
        $same_as .= "<link itemprop=\"sameAs\" href=\"$same_as_uri\">";
    }

    // Get the main type.
    $main_type = wl_entity_get_type( $post->ID );

    if (null === $main_type) {
        $item_type = '';
    } else {
        $item_type = ' itemtype="' . esc_attr($main_type['uri']) . '"';

        // Append the stylesheet if the enable color coding flag is set to true.
        if ( wl_config_get_enable_color_coding_of_entities_on_frontend() ) {
            $item_type .= ' class="' . esc_attr( $main_type['css_class'] ) . '"';
        }
    }

    // Get the additional properties.
    $additional_properties = '';
    if ( isset( $main_type['custom_fields'] ) ) {
        foreach ($main_type['custom_fields'] as $key => $prop) {
            wl_write_log( "_wl_content_embed_microdata [ key :: $key ][ prop :: $prop ]" );
            $values = get_post_meta( $post->ID, $key );
            foreach ( $values as $value ) {
                $additional_properties .= '<meta itemprop="' . esc_attr( $prop ) . '" content="' . esc_attr( $value ). '" />';
            }
        }
    }

    // Get the entity URL.
    $url = '<link itemprop="url" href="' . get_permalink( $post->ID ) . '" />';

    // Replace the original tagging with the new tagging.
    $regex = '|<(\\w+)[^<]* itemid=\"' . esc_attr( $uri ) . '\"[^>]*>([^<]*)<\\/\\1>|i';
    $content = preg_replace($regex,
        '<$1 itemscope' . $item_type . ' itemid="' . esc_attr( $entity_uri ) . '">'
        . $same_as
        . $additional_properties
        . $url
        . '<span itemprop="name">$2</span></$1>',
        $content
    );

    wl_write_log( "wl_content_embed_item_microdata [ uri :: $uri ][ regex :: $regex ]" );

    return $content;
}
add_filter('the_content', 'wl_content_embed_microdata');

