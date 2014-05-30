<?php

/**
 * This file covers tests related to the entity display as setting.
 */

require_once 'functions.php';

class EntityPublishTest extends WP_UnitTestCase
{

    /**
     * Set up the test.
     */
    function setUp()
    {
        parent::setUp();

        wl_configure_wordpress_test();
        rl_empty_dataset();
    }

    function testDisplayAsIndex() {

        global $wp_query, $post;

        $post_id = wl_create_post( '', 'entity-1', 'Entity 1', 'publish', 'entity' );
        wl_set_entity_display_as( $post_id, 'index' );

        $wp_query = new WP_Query( 'post_in=' . $post_id );
        $post = get_post( $post_id );

        $this->assertEquals( '/tmp/wordpress/wp-content/themes/twentyfourteen/index.php', get_single_template() );

    }

    function testDisplayAsPage() {

        global $wp_query, $post;

        $post_id = wl_create_post( '', 'entity-1', 'Entity 1', 'publish', 'entity' );
        wl_set_entity_display_as( $post_id, 'page' );

        $wp_query = new WP_Query( 'post_in=' . $post_id );
        $post = get_post( $post_id );

        $this->assertEquals( '/tmp/wordpress/wp-content/themes/twentyfourteen/single.php', get_single_template() );

    }

}