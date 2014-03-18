<?php
require_once 'functions.php';

/**
 * Class EntityImagesTest
 */
class EntityImagesTest extends WP_UnitTestCase
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

        // Check that entities and posts have been deleted.
        $this->assertEquals( 0, count( get_posts( array(
            'posts_per_page' => -1,
            'post_type'      => 'post',
            'post_status'    => 'any'
        ) ) ) );
        $this->assertEquals( 0, count( get_posts( array(
            'posts_per_page' => -1,
            'post_type'      => 'entity',
            'post_status'    => 'any'
        ) ) ) );

    }

    function testSaveOneImage() {

        $uri         = 'http://example.org/entity';
        $label       = 'Entity';
        $type        = 'thing';
        $description = 'An example entity.';
        $images      = array(
            'http://upload.wikimedia.org/wikipedia/commons/f/ff/Tim_Berners-Lee-Knight.jpg'
        );
        $same_as     = array();
        $entity_post = wl_save_entity( $uri, $label, $type, $description, $images, null, $same_as );

        // Get all the attachments for the entity post.
        $attachments = wl_get_attachments( $entity_post->ID );

        // Check that there is one attachment.
        $this->assertEquals( 1, count( $attachments ) );

        // Check that the attachments are found by source URL.
        $image_post  = wl_get_attachment_for_source_url( $entity_post->ID, $images[0] );
        $this->assertNotNull( $image_post );

        // Check that the no attachments are found if the source URL doesn't exist.
        $image_post  = wl_get_attachment_for_source_url( $entity_post->ID, 'http://example.org/non-existing-image.png' );
        $this->assertNull( $image_post );
    }

    function testSaveMultipleImages() {

        $uri         = 'http://example.org/entity';
        $label       = 'Entity';
        $type        = 'thing';
        $description = 'An example entity.';
        $images      = array(
            'http://upload.wikimedia.org/wikipedia/commons/f/ff/Tim_Berners-Lee-Knight.jpg',
            'http://upload.wikimedia.org/wikipedia/commons/3/3a/Tim_Berners-Lee_closeup.jpg',
            'http://upload.wikimedia.org/wikipedia/commons/c/c2/Tim_Berners-Lee_2012.jpg'
        );
        $same_as     = array();

        $entity_post = wl_save_entity( $uri, $label, $type, $description, $images, null, $same_as );

        // Get all the attachments for the entity post.
        $attachments = wl_get_attachments( $entity_post->ID );

        // Check that there is one attachment.
        $this->assertEquals( 3, count( $attachments ) );

        // Check that the attachments are found by source URL.
        foreach ( $images as $image ) {
            $image_post  = wl_get_attachment_for_source_url( $entity_post->ID, $image );
            $this->assertNotNull( $image_post );
        }
    }

    function testSaveExistingImages() {

        $uri         = 'http://example.org/entity';
        $label       = 'Entity';
        $type        = 'thing';
        $description = 'An example entity.';
        $images      = array(
            'http://upload.wikimedia.org/wikipedia/commons/f/ff/Tim_Berners-Lee-Knight.jpg',
            'http://upload.wikimedia.org/wikipedia/commons/3/3a/Tim_Berners-Lee_closeup.jpg',
            'http://upload.wikimedia.org/wikipedia/commons/c/c2/Tim_Berners-Lee_2012.jpg',
            'http://upload.wikimedia.org/wikipedia/commons/3/3a/Tim_Berners-Lee_closeup.jpg'
        );
        $same_as     = array();

        $entity_post = wl_save_entity( $uri, $label, $type, $description, $images, null, $same_as );

        // Get all the attachments for the entity post.
        $attachments = wl_get_attachments( $entity_post->ID );

        // Check that there is one attachment.
        $this->assertEquals( 3, count( $attachments ) );

        // Check that the attachments are found by source URL.
        foreach ( $images as $image ) {
            $image_post  = wl_get_attachment_for_source_url( $entity_post->ID, $image );
            $this->assertNotNull( $image_post );
        }
    }

}