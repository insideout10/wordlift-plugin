<?php
require_once 'functions.php';

/**
 * Class EntityTest
 */
class EntityTest extends WP_UnitTestCase
{

    /**
     * Set up the test.
     */
    function setUp()
    {
        parent::setUp();

        // Configure WordPress with the test settings.
        wl_configure_wordpress_test();

        // Reset data on the remote dataset.
        rl_empty_dataset();

        // Check that the dataset is empty.
        $this->assertEquals( array(
            'subjects'   => 0,
            'predicates' => 0,
            'objects'    => 0
        ), rl_count_triples() );

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

    function testSaveEntity1() {

        $uri         = 'http://dbpedia.org/resource/Tim_Berners-Lee';
        $label       = 'Tim Berners-Lee';
        $type        = array(
            'class' => 'person'
        );
        $description = file_get_contents( dirname(__FILE__) . '/assets/tim_berners-lee.txt' );
        $images      = array(
            'http://upload.wikimedia.org/wikipedia/commons/f/ff/Tim_Berners-Lee-Knight.jpg'
        );
        $same_as     = array();
        $entity_post = wl_save_entity( $uri, $label, $type, $description, $images, null, $same_as );

        $this->assertNotNull( $entity_post );

        // Check that the type is set correctly.
        $types = wl_get_entity_types( $entity_post->ID );
        $this->assertEquals( 1, count( $types ) );
        $this->assertEquals( 'person', $types[0]->slug );

        // Create related resources.
        $world_wide_web_id = $this->create_World_Wide_Web_Foundation( $entity_post->ID );
        $mit_id            = $this->create_MIT_Center_for_Collective_Intelligence( $entity_post->ID );

        // Check that entities are related to this resource.
        $related_entities = wl_get_related_entities( $entity_post->ID );
        $this->assertEquals( 2, count( $related_entities ) );
        $this->assertEquals( true, in_array( $world_wide_web_id, $related_entities ) );
        $this->assertEquals( true, in_array( $mit_id, $related_entities ) );
    }

    function create_World_Wide_Web_Foundation( $related_post_id ) {

        $uri         = 'http://dbpedia.org/resource/World_Wide_Web_Foundation';
        $label       = 'World Wide Web Foundation';
        $type        = array(
            'class' => 'organization'
        );
        $description = file_get_contents( dirname(__FILE__) . '/assets/world_wide_web_foundation.txt' );
        $images      = array();
        $same_as     = array(
            'http://rdf.freebase.com/ns/m.04myd3k',
            'http://yago-knowledge.org/resource/World_Wide_Web_Foundation'
        );
        $entity_post = wl_save_entity( $uri, $label, $type, $description, $images, $related_post_id, $same_as );

        $this->assertNotNull( $entity_post );

        // Check that the type is set correctly.
        $types = wl_get_entity_types( $entity_post->ID );
        $this->assertEquals( 1, count( $types ) );
        $this->assertEquals( 'organization', $types[0]->slug );

        // Check that Tim Berners-Lee is related to this resource.
        $related_entities = wl_get_related_entities( $entity_post->ID );
        $this->assertEquals( 1, count( $related_entities ) );
        $this->assertEquals( $related_post_id, $related_entities[0] );

        return $entity_post->ID;
    }

    function create_MIT_Center_for_Collective_Intelligence( $related_post_id ) {

        $uri         = 'http://dbpedia.org/resource/MIT_Center_for_Collective_Intelligence';
        $label       = 'MIT Center for Collective Intelligence';
        $type        = array(
            'class' => 'organization'
        );
        $description = file_get_contents( dirname(__FILE__) . '/assets/mit_center_for_cognitive_intelligence.txt' );
        $images      = array();
        $same_as     = array(
            'http://rdf.freebase.com/ns/m.04n2n64'
        );
        $entity_post = wl_save_entity( $uri, $label, $type, $description, $images, $related_post_id, $same_as );

        // Check that the type is set correctly.
        $types = wl_get_entity_types( $entity_post->ID );
        $this->assertEquals( 1, count( $types ) );
        $this->assertEquals( 'organization', $types[0]->slug );

        // Check that Tim Berners-Lee is related to this resource.
        $related_entities = wl_get_related_entities( $entity_post->ID );
        $this->assertEquals( 1, count( $related_entities ) );
        $this->assertEquals( $related_post_id, $related_entities[0] );

        return $entity_post->ID;
    }

}