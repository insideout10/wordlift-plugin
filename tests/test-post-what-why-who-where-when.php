<?php

require_once 'functions.php';

/**
 * Class Post5wTest
 */
class Post5wTest extends WP_UnitTestCase
{

    /**
     * Set up the test.
     */
    function setUp()
    {
        parent::setUp();

        // Configure WordPress with the test settings.
        wl_configure_wordpress_test();

        // Empty the blog.
        wl_empty_blog();
    }

    function test() {
        
        // Create a post.
        $post_id = wl_create_post( '', 'post', 'post', 'publish', 'post' );
        
        // Create the 5W entities.
        // WHAT
        $what_id = wl_create_post( '', 'what', 'Entity What', 'publish', 'entity' );
        $what_uri = wl_get_entity_uri( $what_id );
        // WHY
        $why_id = wl_create_post( '', 'why', 'Entity Why', 'publish', 'entity' );
        $why_uri = wl_get_entity_uri( $why_id );
        // WHO
        $who_id = wl_create_post( '', 'who', 'Entity Who', 'publish', 'entity' );
        $who_uri = wl_get_entity_uri( $who_id );
        // WHEN
        $when_id = wl_create_post( '', 'when', 'Entity When', 'publish', 'entity' );
        $when_uri = wl_get_entity_uri( $when_id );
        // WHERE
        $where_id = wl_create_post( '', 'where', 'Entity Where', 'publish', 'entity' );
        $where_uri = wl_get_entity_uri( $where_id );
        
        // Bind the all 5W to the post (can be passed by both id or uri).
        wl_5w_set_article_w( $post_id, WL_5W_WHAT, $what_id );
        wl_5w_set_article_w( $post_id, WL_5W_WHAT, $when_id );  // another entity on the same W
        wl_5w_set_article_w( $post_id, WL_5W_WHY, $why_uri );   // assign by uri
        wl_5w_set_article_w( $post_id, WL_5W_WHO, array( $who_id, $what_uri) );    // assign more than one
        wl_5w_set_article_w( $post_id, WL_5W_WHEN, $when_uri );
        wl_5w_set_article_w( $post_id, WL_5W_WHERE, $where_id );
        
        // Check associations.
        $w5 = wl_5w_get_all_article_Ws( $post_id );
        $this->assertEquals( array( $what_uri, $when_uri ), $w5[WL_5W_WHAT] );
        $this->assertEquals( array( $where_uri ), $w5[WL_5W_WHERE] );
        $this->assertEquals( array( $when_uri ), $w5[WL_5W_WHEN] );
        $this->assertEquals( array( $what_uri, $when_uri ), wl_5w_get_article_w( $post_id, WL_5W_WHAT ) );
        $this->assertEquals( array( $where_uri ), wl_5w_get_article_w( $post_id, WL_5W_WHERE ) );
        $this->assertEquals( array( $who_uri, $what_uri ), wl_5w_get_article_w( $post_id, WL_5W_WHO ) );
    }
}

