<?php
/**
 */

function wordlift_register_shortcode_related_posts() {
    add_shortcode('wl-related-posts', 'wordlift_shortcode_related_posts');
}

function wordlift_shortcode_related_posts() {

    // get the current post id.
    $post_id       = get_the_ID();
    // get the related posts (published).
    $related_posts = wordlift_get_related_posts( $post_id );

    $content       = '<h1>Related Posts</h1>';
    foreach ( $related_posts as $related_post ) {
        $author_link = '<a href="' . esc_url( get_the_author_meta('url', $related_post->ID) ) .
            '" title="' . esc_attr( sprintf(__("Visit %s&#8217;s website"), get_the_author_meta('display_name', $related_post->ID)) ) .
            '" rel="author external">' . get_the_author_meta('display_name', $related_post->ID) . '</a>';

        $content .= <<<EOF
<h2>$related_post->post_title</h2>
<p class="author">by $author_link</p>
<p>$related_post->post_content</p>
EOF;
    }

    return $content;
}

add_action( 'init', 'wordlift_register_shortcode_related_posts');