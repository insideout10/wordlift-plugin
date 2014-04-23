<?php
/**
 * Test Entity functions.
 */

require_once 'functions.php';

/**
 * Class EntityTest
 */
class EntityFunctionsTest extends WP_UnitTestCase
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

    /**
     * Check entity URI building.
     */
    function testEntityURIforAPost() {

        $post_id = wl_create_post('', 'test', 'This is a test');

        $expected_uri = wl_config_get_dataset_base_uri() . '/post/This_is_a_test';
        $this->assertEquals($expected_uri, wl_build_entity_uri( $post_id ) );

    }

    /**
     * Check entity URI building.
     */
    function testEntityURIforAnEntity() {

        $post_id = wl_create_post('', 'test', 'This is a test', 'draft', 'entity');

        $expected_uri = wl_config_get_dataset_base_uri() . '/entity/This_is_a_test';
        $this->assertEquals($expected_uri, wl_build_entity_uri( $post_id ) );

    }

    /**
     * Check entity URI building.
     */
    function testEntityURIforAnAuthor() {

        $post_id = wl_create_post('', 'test', 'This is a test', 'draft', 'author');

        $expected_uri = wl_config_get_dataset_base_uri() . '/author/This_is_a_test';
        $this->assertEquals($expected_uri, wl_build_entity_uri( $post_id ) );

    }
}

