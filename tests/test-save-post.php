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
        wl_empty_blog();
    }

    function testSavePostAndReferencedEntities() {

        // create two entities
        $entity_1_id = wl_create_post( '', 'entity-1', uniqid( 'entity', true ), 'draft', 'entity' );
        $entity_2_id = wl_create_post( '', 'entity-2', uniqid( 'entity', true ), 'draft', 'entity' );

        $entity_1_uri = wl_get_entity_uri( $entity_1_id );
        $entity_2_uri = wl_get_entity_uri( $entity_2_id );

        $body_1      = <<<EOF
            <span itemid="$entity_1_uri">Entity 1</span>
            <span itemid="$entity_2_uri">Entity 2</span>
EOF;
        
        // Create a post in draft
        $post_1_id = wl_create_post( $body_1, 'post-1', uniqid('post', true), 'draft', 'post' );
        // Post in draft: should be not pushed on Redlink 
        $lines = $this->getPostTriples( $post_1_id );

        // Just 1 line returned means the entity was not found on Redlink
        $this->assertCount( 1, $lines );
        // Check that no entity is referenced
        $this->assertCount( 0, wl_get_referenced_entities( $post_1_id ) );
        // Force post status to publish: this triggers the save_post hook
        wl_update_post_status( $post_1_id, 'publish' );
        // Check entities are properly related once the post is published
        $this->assertCount( 2, wl_get_referenced_entities( $post_1_id ) );
    }

    function testSaveEntityPostAndRelatedEntities() {

        // create two entities
        $entity_1_id = wl_create_post( '', 'entity-1', uniqid( 'entity', true ), 'draft', 'entity' );
        $entity_2_id = wl_create_post( '', 'entity-2', uniqid( 'entity', true ), 'draft', 'entity' );

        $entity_1_uri = wl_get_entity_uri( $entity_1_id );
        $entity_2_uri = wl_get_entity_uri( $entity_2_id );

        $body_1      = <<<EOF
            <span itemid="$entity_1_uri">Entity 1</span>
            <span itemid="$entity_2_uri">Entity 2</span>
EOF;
        
        // Create a post in draft
        $entity_3_id = wl_create_post( $body_1, 'entity-3', uniqid('entity', true), 'draft', 'entity' );
        $entity_3_uri = wl_get_entity_uri( $entity_3_id );

        // Entity post in draft: should be not pushed on Redlink 
        $lines = $this->getPostTriples( $entity_3_id );
        // Just 1 line returned means the entity was not found on Redlink
        $this->assertCount( 1, $lines );
        // Check that no entity is referenced
        $this->assertCount( 0, wl_get_related_entities( $entity_3_id) );
        // Force entity post status to publish: this triggers the save_post hook
        wl_update_post_status( $entity_3_id, 'publish' );
        // Check entities are properly related once the post is published
        $this->assertCount( 2, wl_get_related_entities( $entity_3_id ) );
        // Entity post published: should be pushed on Redlink 
        $lines = $this->getPostTriples( $entity_3_id );
        $this->assertCount( 6, $lines );
        // Check entity 1 and 2 are properly related to entity 3 on Redlink side
        $this->assertTrue( in_array("<http://purl.org/dc/terms/relation><$entity_1_uri>", $lines) );
        $this->assertTrue( in_array("<http://purl.org/dc/terms/relation><$entity_2_uri>", $lines) );
        // And viceversa 
        $lines = $this->getPostTriples( $entity_1_id );
        $this->assertTrue( in_array("<http://purl.org/dc/terms/relation><$entity_3_uri>", $lines) );
        $lines = $this->getPostTriples( $entity_2_id );
        $this->assertTrue( in_array("<http://purl.org/dc/terms/relation><$entity_3_uri>", $lines) );
        // Update entity 3 body  
        $body_1      = <<<EOF
            <span itemid="$entity_1_uri">Entity 1</span>
EOF;
        wp_update_post( array( 'ID' => $entity_3_id, 'post_content' => $body_1 ));
        $this->assertCount( 1, wl_get_related_entities( $entity_3_id ) );
        $lines = $this->getPostTriples( $entity_3_id );
        $this->assertCount( 5, $lines );
        // Check just entity 1 is properly related to entity 3 on Redlink side
        $this->assertTrue( in_array("<http://purl.org/dc/terms/relation><$entity_1_uri>", $lines) );
        $this->assertFalse( in_array("<http://purl.org/dc/terms/relation><$entity_2_uri>", $lines) );
        // And Veceversa 
        $lines = $this->getPostTriples( $entity_1_id );
        $this->assertTrue( in_array("<http://purl.org/dc/terms/relation><$entity_3_uri>", $lines) );
        // Entity 2 should not be related anymore to Entity 3
        $lines = $this->getPostTriples( $entity_2_id );
        // TODO Fix the bug and uncomment the test
        // $this->assertFalse( in_array("<http://purl.org/dc/terms/relation><$entity_3_uri>", $lines) );
        
        // Delete Entity 1 in order to see if relations with Entity 3 are managed properly
        wp_delete_post( $entity_1_id, false );
        
        // Check that Entity 3 is no more related to Entity 1 on WP
        // TODO Fix the bug and uncomment the test
        // $this->assertCount( 0, wl_get_referenced_entities( $entity_3_id ) );
        
        // Check that Entity 1 is no more on Redlink
        $lines = $this->getPostTriples( $entity_1_id );
        $this->assertCount( 1, $lines );

        // Check that Entity 3 is no more related to Entity 1 on Redlink
        $lines = $this->getPostTriples( $entity_3_id );
        // TODO Fix the bug and uncomment the test
        // $this->assertCount( 4, $lines );
        // $this->assertFalse( in_array("<http://purl.org/dc/terms/relation><$entity_1_uri>", $lines) );
           
    }

    function testReferencedEntities() {

        // create two entities
        $entity_1_id = wl_create_post( '', 'entity-1', uniqid( 'entity', true ), 'draft', 'entity' );
        $entity_2_id = wl_create_post( '', 'entity-2', uniqid( 'entity', true ), 'draft', 'entity' );

        $entity_1_uri = wl_get_entity_uri( $entity_1_id );
        $entity_2_uri = wl_get_entity_uri( $entity_2_id );

        $body_1      = <<<EOF
            <span itemid="$entity_1_uri">Entity 1</span>
            <span itemid="$entity_2_uri">Entity 2</span>
EOF;

        $post_1_id = wl_create_post( $body_1, 'post-1', uniqid( 'post', true ), 'draft', 'post' );
        $lines = $this->getPostTriples( $post_1_id );
        $this->assertCount( 1, $lines );

        // check are not published on redlink
        $lines = $this->getPostTriples( $entity_1_id );
        $this->assertCount( 1, $lines );

        $lines = $this->getPostTriples( $entity_2_id );

        $this->assertCount( 1, $lines );

        // publish post 1
        wl_update_post_status( $post_1_id, 'publish' );
        $this->assertCount( 2, wl_get_referenced_entities( $post_1_id ) );

    }

    function testPublishingUnpublishingPosts() {

        // create two entities
        $entity_1_id = wl_create_post( '', 'entity-1', uniqid( 'entity', true ), 'draft', 'entity' );
        $entity_2_id = wl_create_post( '', 'entity-2', uniqid( 'entity', true ), 'draft', 'entity' );

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
        $post_1_id = wl_create_post( $body_1, 'post-1', uniqid( 'post', true ), 'draft', 'post' );

        // check the post is not published on Redlink.
        $lines = $this->getPostTriples( $post_1_id );
        $this->assertEquals( 1, sizeof( $lines ) );

        // publish the post.
        wl_update_post_status( $post_1_id, 'publish' );
        $this->assertCount( 2, wl_get_referenced_entities( $post_1_id ) );

        // check the post is published on Redlink.
        $lines = $this->getPostTriples( $post_1_id );
        $this->assertCount( 10, $lines );

        // unpublish the post.
        wl_update_post_status( $post_1_id, 'draft' );

        $this->assertCount( 2, wl_get_referenced_entities( $post_1_id ) );

        // check the post is not published on Redlink.
        $lines = $this->getPostTriples( $post_1_id );
        $this->assertCount( 1, $lines );

        // create another post
        $post_2_id = wl_create_post( $body_2, 'post-2', uniqid( 'post', true ), 'draft', 'post' );

        // check all entities published
        $lines = $this->getPostTriples( $entity_1_id );
        echo "mar lines " . var_export($lines, true);
        $this->assertCount( 1, $lines );

        // publish post 2
        wl_update_post_status( $post_2_id, 'publish' );

        $this->assertCount( 1, wl_get_referenced_entities( $post_2_id ) );

        // check post 2 is published on Redlink
        $lines = $this->getPostTriples( $post_2_id );
        $this->assertCount( 9, $lines );

        // publish post 1
        wl_update_post_status( $post_1_id, 'publish' );

        $this->assertCount( 2, wl_get_referenced_entities( $post_1_id ) );

        // check post 1 is published on Redlink
        $lines = $this->getPostTriples( $post_1_id );
        $this->assertCount( 10, $lines );

        $lines = $this->getPostTriples( $entity_1_id );
        $this->assertCount( 3, $lines );

        $lines = $this->getPostTriples( $entity_2_id );
        $this->assertCount( 3, $lines );

        // unpublish post 1
        wl_update_post_status( $post_1_id, 'draft' );

        $this->assertCount( 2, wl_get_referenced_entities( $post_1_id ) );

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
            $lines[] = preg_replace( '/\s+/', '', $line );
        }

        return $lines;

    }

    /**
     * Test saving a post without a title. Check the URI.
     */
    function testSavePostWithoutTitle() {

        $post_id = wl_create_post( 'Sample Post', 'post-1', '', 'publish' );
        $uri     = wl_get_entity_uri( $post_id );
        $expected_uri = wl_configuration_get_redlink_dataset_uri() . "/post/id/$post_id";

        $this->assertEquals( $expected_uri, $uri );
    }
}
