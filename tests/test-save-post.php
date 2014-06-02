<?php

/**
 * This file covers tests related to the save-post related routines.
 */

require_once 'functions.php';

class SavePostTest extends WP_UnitTestCase
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

    function testSavePostAndRelatedEntities() {

        // create two entities
        $entity_1_id = wl_create_post( '', 'entity-1', 'Entity 1', 'draft', 'entity' );
        $entity_2_id = wl_create_post( '', 'entity-2', 'Entity 2', 'draft', 'entity' );

        $entity_1_uri = wl_get_entity_uri( $entity_1_id );
        $entity_2_uri = wl_get_entity_uri( $entity_2_id );

        $body_1      = <<<EOF
            <span itemid="$entity_1_uri">Entity 1</span>
            <span itemid="$entity_2_uri">Entity 2</span>
EOF;


        $post_1_id = wl_create_post( $body_1, 'post-1', 'Post 1', 'draft', 'post' );
        $lines = $this->getPostTriples( $post_1_id );
        $this->assertCount( 1, $lines );

        // bind the entities
        wl_add_referenced_entities( $post_1_id, array( $entity_1_id, $entity_2_id ) );
        $this->assertCount( 2, wl_get_referenced_entity_ids( $post_1_id ) );

        wordlift_save_post_and_related_entities( $post_1_id );

        // bind the entities
        $this->assertCount( 2, wl_get_referenced_entity_ids( $post_1_id ) );
    }

    function testReferencedEntities() {

        // create two entities
        $entity_1_id = wl_create_post( '', 'entity-1', 'Entity 1', 'draft', 'entity' );
        $entity_2_id = wl_create_post( '', 'entity-2', 'Entity 2', 'draft', 'entity' );

        $entity_1_uri = wl_get_entity_uri( $entity_1_id );
        $entity_2_uri = wl_get_entity_uri( $entity_2_id );

        $body_1      = <<<EOF
            <span itemid="$entity_1_uri">Entity 1</span>
            <span itemid="$entity_2_uri">Entity 2</span>
EOF;

        $post_1_id = wl_create_post( $body_1, 'post-1', 'Post 1', 'draft', 'post' );
        $lines = $this->getPostTriples( $post_1_id );
        $this->assertCount( 1, $lines );

        // check all entities published
        $lines = $this->getPostTriples( $entity_1_id );
        $this->assertCount( 1, $lines );

        $lines = $this->getPostTriples( $entity_2_id );
        $this->assertCount( 1, $lines );

        // bind the entities
        wl_add_referenced_entities( $post_1_id, array( $entity_1_id, $entity_2_id ) );
        $this->assertCount( 2, wl_get_referenced_entity_ids( $post_1_id ) );

        // publish post 1
        wl_update_post_status( $post_1_id, 'publish' );
        $this->assertCount( 2, wl_get_referenced_entity_ids( $post_1_id ) );

    }

    function testPublishingUnpublishingPosts() {

        // create two entities
        $entity_1_id = wl_create_post( '', 'entity-1', 'Entity 1', 'draft', 'entity' );
        $entity_2_id = wl_create_post( '', 'entity-2', 'Entity 2', 'draft', 'entity' );

        $entity_1_uri = wl_get_entity_uri( $entity_1_id );
        $entity_2_uri = wl_get_entity_uri( $entity_2_id );

        $body_1      = <<<EOF
            <span itemid="$entity_1_uri">Entity 1</span>
            <span itemid="$entity_2_uri">Entity 2</span>
EOF;

        $body_2      = <<<EOF
            <span itemid="$entity_2_uri">Entity 2</span>
EOF;

        // create a post as a draft.
        $post_1_id = wl_create_post( $body_1, 'post-1', 'Post 1', 'draft', 'post' );

        // check the post is not published on Redlink.
        $lines = $this->getPostTriples( $post_1_id );
        $this->assertEquals( 1, sizeof( $lines ) );

        // publish the post.
        wl_update_post_status( $post_1_id, 'publish' );
        $this->assertCount( 2, wl_get_referenced_entity_ids( $post_1_id ) );

        // check the post is published on Redlink.
        $lines = $this->getPostTriples( $post_1_id );
        $this->assertCount( 10, $lines );

        // unpublish the post.
        wl_update_post_status( $post_1_id, 'draft' );
        $this->assertCount( 2, wl_get_referenced_entity_ids( $post_1_id ) );

        // check the post is not published on Redlink.
        $lines = $this->getPostTriples( $post_1_id );
        $this->assertCount( 1, $lines );

        // create another post
        $post_2_id = wl_create_post( $body_2, 'post-2', 'Post 2', 'draft', 'post' );

        // check all entities published
        $lines = $this->getPostTriples( $entity_1_id );
        $this->assertCount( 1, $lines );

        $lines = $this->getPostTriples( $entity_2_id );
        $this->assertCount( 1, $lines );


        // publish post 2
        wl_update_post_status( $post_2_id, 'publish' );
        $this->assertCount( 1, wl_get_referenced_entity_ids( $post_2_id ) );

        // check post 2 published
        $lines = $this->getPostTriples( $post_2_id );
        $this->assertCount( 9, $lines );

        // publish post
        wl_update_post_status( $post_1_id, 'publish' );
        $this->assertCount( 2, wl_get_referenced_entity_ids( $post_1_id ) );

        // check post 2 published
        $lines = $this->getPostTriples( $post_1_id );
        $this->assertCount( 10, $lines );

        $lines = $this->getPostTriples( $entity_1_id );
        $this->assertCount( 3, $lines );

        $lines = $this->getPostTriples( $entity_2_id );
        $this->assertCount( 3, $lines );

        // unpublish post 1
        wl_update_post_status( $post_1_id, 'draft' );
        $this->assertCount( 2, wl_get_referenced_entity_ids( $post_1_id ) );

        $lines = $this->getPostTriples( $post_1_id );
        $this->assertCount( 1, $lines );

        // check only entity 1 unpublished
        $lines = $this->getPostTriples( $entity_1_id );
        $this->assertCount( 1, $lines );

        $lines = $this->getPostTriples( $entity_2_id );
        $this->assertCount( 3, $lines );

    }

    function getPostTriples( $post_id ) {

        // Get the post Redlink URI.
        $uri      = wl_get_entity_uri( $post_id );
        $uri_esc  = wordlift_esc_sparql( $uri );

        // Prepare the SPARQL query to select label and URL.
        $sparql   = "SELECT DISTINCT ?p ?o WHERE { <$uri_esc> ?p ?o . }";

        // Send the query and get the response.
        $response = rl_sparql_select( $sparql, 'text/tab-separated-values' );

        $this->assertFalse(is_wp_error($response));

        $lines = array();
        foreach ( explode( "\n", $response['body'] ) as $line ) {
            if ( empty( $line ) ) {
                continue;
            }
            $lines[] = $line;
        }

        return $lines;

    }

    /**
     * Test saving a post without a title. Check the URI.
     */
    function testSavePostWithoutTitle() {

        $post_id = wl_create_post( 'Sample Post', 'post-1', '', 'publish' );
        $uri     = wl_get_entity_uri( $post_id );
        $expected_uri = wl_config_get_dataset_base_uri() . "/post/id/$post_id";

        $this->assertEquals( $expected_uri, $uri );
    }
}
