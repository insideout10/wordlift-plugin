<?php
/**
 */

function wordlift_indepth_article_is_single() {
    return ( is_page() || is_single() );
}

function wordlift_indepth_article_init() {

    write_log( 'wordlift_indepth_article_init' );

    if ( ! wordlift_indepth_article_is_single() ) {

        write_log( 'wordlift_indepth_article_init: not single, skip processing.' );

        // flush.
        wordlift_indepth_end_buffering();

        return;
    }

    // hook the wooter for flushing.
    add_action( 'wp_footer', 'wordlift_indepth_end_buffering', 0, 0 );
}

function wordlift_indepth_article_start() {

    if ( ! wordlift_indepth_article_is_single() ) {
        return;
    }

    ob_start( 'wordlift_indepth_article_ob_callback' );
}

function wordlift_indepth_article_ob_callback( $content ) {

    write_log( 'wordlift_indepth_article_ob_callback' );

    $post_id        = get_the_ID();
    $post_permalink = get_permalink();

    $content = preg_replace('/<html ([^>]*)>/i',
        '<html xmlns="http://www.w3.org/1999/xhtml"' .
            ' xml:lang="en" lang="en"' .
            ' xmlns:sa="http://opengraph.socialamp.com/2010/saml"' .
            ' xmlns:fb="http://www.facebook.com/2008/fbml"' .
            ' xmlns:og="http://ogp.me/ns#"' .
            ' prefix="og: http://ogp.me/ns#"' .
            ' itemscope="itemscope"' .
            ' itemtype="http://schema.org/Article"' .
            ' itemid="' . $post_permalink . '" $1>',
        $content
    );

    // add the itemprop date published to time tags.
    $content = preg_replace(
        '/<time ([^>]*)>/i',
        '<time itemprop="datePublished" $1>',
        $content
    );

    $content = preg_replace(
        '/<img (.*)alt="logo"/i',
        '<img itemprop="logo" $1alt="logo"',
        $content
    );

    $content = preg_replace(
        '/<a class="logo([^>]*)>(.*)<\/a>/i',
        '<span itemscope itemtype="http://schema.org/Organization"><a itemprop="url" class="$1>$2</a></span>',
        $content
    );

    // avamasys
    $content = preg_replace(
        '/<div class="post-top clearfix">[^<]*<h1>([^<]*)<\/h1>/i',
        '<div class="post-top clearfix"><h1 itemprop="name">$1</h1>',
        $content
    );

    $content = preg_replace(
        '/<h1 class="entry-title">/i',
        '<h1 class="entry-title" itemprop="name">',
        $content
    );

    return $content;
}

function wordlift_indepth_article_end() {

    ob_end_flush();
}


/**
 * Change the response content type.
 * @param string $type The preset content type.
 * @return string The new content type.
 */
function wordlift_override_content_type_header( $type ) {

    return 'application/xhtml+xml';
}

//add_filter( 'option_html_type', 'wordlift_override_content_type_header' );

$wordlift_indepth_buffering_started = false;

function wordlift_indepth_start_buffering( $type ) {
    global $wordlift_indepth_buffering_started;

    if ( $wordlift_indepth_buffering_started ) {
        return $type;
    }

    $wordlift_indepth_buffering_started = true;

    write_log( 'wordlift_indepth_start_buffering' );

    ob_start( 'wordlift_indepth_article_ob_callback' );

    return $type;
}

function wordlift_indepth_end_buffering() {

    write_log( 'wordlift_indepth_end_buffering' );

    ob_end_flush();
}

function wordlift_indepth_article_head() {

    // site name.
    $blog_title   = esc_attr( get_bloginfo('name') );

    // get the post ID.
    $post_id      = get_the_ID();

    // get the post.
    $post         = get_post( $post_id );

    // the author id.
    $author_id    = $post->post_author;
    $author_display_name = esc_attr( get_the_author_meta( 'display_name', $author_id ) );
    $author_url   = esc_attr( get_author_posts_url( $author_id ) );

    // get the excerpt or the content.
    $post_excerpt = esc_attr( wp_strip_all_tags( $post->post_excerpt, true ) );
    if ( empty( $post_excerpt ) ) {
        write_log( 'wordlift_indepth_article_head: using content as excerpt' );
        $post_excerpt = esc_attr( wp_trim_words( wp_strip_all_tags( $post->post_content, true ), 55, ' [...]') );
    }

    // set title, tags and URL.
    $post_title   = esc_attr( get_the_title() );
    $post_tags_array = get_the_tags( $post_id );
    $post_tags    = ( is_array( $post_tags_array ) ? esc_attr( implode( ', ', $post_tags_array ) ) : '' );
    $post_url     = esc_attr( get_permalink( $post_id ) );

    // get the image/thumbnail associated with this post.
    $post_thumbnail_id = get_post_thumbnail_id( $post_id );
    $post_image_urls = wp_get_attachment_image_src( $post_thumbnail_id, 'full' );
    $post_thumbnail_urls = wp_get_attachment_image_src( $post_thumbnail_id, 'thumbnail' );
    $post_image     = esc_attr( $post_image_urls[0] );
    $post_thumbnail = esc_attr( $post_thumbnail_urls[0] );
    write_log( "[ post_thumbnail_id :: $post_thumbnail_id ][ post_image :: $post_image ]" );

    $post_published = esc_attr( get_post_time( 'c', true, $post ) );
    $post_modified  = esc_attr( get_post_modified_time( 'c', true, $post ) );

    // we leave this to old-SEO plugins.
//    <meta name="description" content="$post_excerpt" />
//    <meta name="keywords" content="$post_tags" />
//    <meta name="author" content="$author_display_name" />

    // we shall leave this to fb/twitter related plugins.
//    <meta property="fb:app_id" content="..." />
//    <meta property="fb:page_id" content="..." />
//    <meta name="twitter:card" content="summary" />
//    <meta name="twitter:site" content="@..." />
//    <meta name="twitter:creator" content="@..." />

    // TODO: set these according to the wl-related-articles shortcode.
//    <link rel="next" href="http://.../2" />
//    <link rel="prev" href="http://..." />

    // this should be set by a URL shortener plugin.
//    <link rel="shortlink" href="http://..." />

    // TODO: provide a way for the editor to set these.
//    <meta itemprop="alternativeHeadline" content="...">
//    <meta itemprop="usageTerms" content="http://...">
//    <meta itemprop="genre" content="...">

    // this is used on the NY Times web site, but it's not part of schema.org and Google will complain about it:
//    <meta itemprop="identifier" content="$post_id">

    // set the post categories.
    $post_categories_array = wp_get_post_categories( $post_id, array('fields' => 'names') );
    $post_categories = ( is_array( $post_categories_array ) ? esc_attr( implode( ' / ', $post_categories_array ) ) : '' );

    echo <<<EOF

    <meta property="og:title" content="$post_title" />
    <meta property="og:site_name" content="$blog_title" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="$post_url" />
    <meta property="og:description" content="$post_excerpt" />

    <meta property="article:published_time" content="$post_published" />
    <meta property="article:author" content="$author_url" />
    <meta property="article:section" content="$post_categories" />
    <meta property="article:tag" content="$post_tags" />

    <link rel="canonical"   href="$post_url" />

    <meta itemprop="description" content="$post_excerpt" />
    <meta itemprop="name" content="$post_title" />
    <meta itemprop="dateModified" content="$post_modified">
    <meta itemprop="datePublished" content="$post_published">
    <meta itemprop="articleSection" content="$post_categories">

EOF;

    // output the images.
    if ( !empty( $post_image ) ) {
        echo <<<EOF
    <meta itemprop="image" content="$post_image" />
    <meta property="og:image" content="$post_image" />
    <meta itemprop="thumbnailUrl" content="$post_thumbnail">

EOF;
    }

}

function wordlift_indepth_add_schema_org_tagging_to_content( $content ) {

    // enclose the body with a span marked articleBody.
    return '<span itemprop="articleBody">' . $content . '</span>';
}

add_filter( 'option_html_type', 'wordlift_indepth_start_buffering' );
add_action( 'wp_head', 'wordlift_indepth_article_init',  -PHP_INT_MAX, 0 );
add_action( 'wp_head', 'wordlift_indepth_article_head', PHP_INT_MAX,  0 );
add_filter( 'the_content', 'wordlift_indepth_add_schema_org_tagging_to_content', PHP_INT_MAX );

