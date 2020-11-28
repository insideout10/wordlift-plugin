<?php
/**
 * Tests: Save Post Test.
 *
 * @since   3.0.0
 * @package Wordlift
 */
/**
 * This file covers tests related to the save-post related routines.
 */

/**
 * Class SavePostTest.
 *
 * @since   3.0.0
 * @package Wordlift
 * @group backend
 */
class Wordlift_Save_Post_Test extends Wordlift_Unit_Test_Case {

	/**
	 * @group redlink
	 */
	function test_savepostandreferencedentities() {

		// create two entities
		$entity_1_id = wl_create_post( '', 'entity-1', uniqid( 'entity', true ), 'draft', 'entity' );
		$entity_2_id = wl_create_post( '', 'entity-2', uniqid( 'entity', true ), 'draft', 'entity' );

		$entity_1_uri = wl_get_entity_uri( $entity_1_id );
		$entity_2_uri = wl_get_entity_uri( $entity_2_id );

		$body_1 = <<<EOF
            <span itemid="$entity_1_uri">Entity 1</span>
            <span itemid="$entity_2_uri">Entity 2</span>
EOF;

		// Create a post in draft
		$post_1_id = wl_create_post( $body_1, 'post-1', uniqid( 'post', true ), 'draft', 'post' );
		// Post in draft: should be not pushed on Redlink
		$lines = $this->getPostTriples( $post_1_id );

		// Just 1 line returned means the entity was not found on Redlink
		$this->assertCount( 1, $lines );
		// Check if 2 entities are locally referenced
		$this->assertCount( 2, wl_core_get_related_entity_ids( $post_1_id ) );
		// Force post status to publish: this triggers the save_post hook
		wl_update_post_status( $post_1_id, 'publish' );
		// Check entities are still related once the post is published
		$this->assertCount( 2, wl_core_get_related_entity_ids( $post_1_id ) );
	}

	/**
	 * @group redlink
	 */
	function test_referencedentities() {

		// create two entities
		$entity_1_id = wl_create_post( '', 'entity-1', uniqid( 'entity', true ), 'draft', 'entity' );
		Wordlift_Entity_Type_Service::get_instance()->set( $entity_1_id, 'http://schema.org/Place' );
		$entity_2_id = wl_create_post( '', 'entity-2', uniqid( 'entity', true ), 'draft', 'entity' );
		Wordlift_Entity_Type_Service::get_instance()->set( $entity_1_id, 'http://schema.org/Person' );

		$entity_1_uri = wl_get_entity_uri( $entity_1_id );
		$entity_2_uri = wl_get_entity_uri( $entity_2_id );

		$body_1 = <<<EOF
            <span itemid="$entity_1_uri">Entity 1</span>
            <span itemid="$entity_2_uri">Entity 2</span>
EOF;

		$post_1_id = wl_create_post( $body_1, 'post-1', uniqid( 'post', true ), 'draft', 'post' );
		$lines     = $this->getPostTriples( $post_1_id );
		$this->assertCount( 1, $lines );

		// check are not published on redlink
		$lines = $this->getPostTriples( $entity_1_id );
		$this->assertCount( 1, $lines );

		$lines = $this->getPostTriples( $entity_2_id );

		$this->assertCount( 1, $lines );

		// publish post 1
		wl_update_post_status( $post_1_id, 'publish' );
		$this->assertCount( 2, wl_core_get_related_entity_ids( $post_1_id ) );

	}

	/**
	 * @group redlink
	 */
	function test_publishing_unpublishing_posts() {

		self::turn_on_entity_push();

		// create two entities
		$entity_1_id = wl_create_post( '', 'entity-1', uniqid( 'entity', true ), 'draft', 'entity' );
		$entity_2_id = wl_create_post( '', 'entity-2', uniqid( 'entity', true ), 'draft', 'entity' );

		$entity_1_uri = Wordlift_Entity_Service::get_instance()
		                                       ->get_uri( $entity_1_id );
		$entity_2_uri = Wordlift_Entity_Service::get_instance()
		                                       ->get_uri( $entity_2_id );

		$body_1 = <<<EOF
            <span itemid="$entity_1_uri">Entity 1</span>
            <span itemid="$entity_2_uri">Entity 2</span>
EOF;

		$body_2 = <<<EOF
            <span itemid="$entity_2_uri">Entity 2</span>
EOF;

		// create a post as a draft.
		$post_1_id = wl_create_post( $body_1, 'post-1', uniqid( 'post', true ), 'draft', 'post' );

		// check the post is not published on Redlink.
		$lines = $this->getPostTriples( $post_1_id );
		$this->assertEquals( 1, sizeof( $lines ) );

		$this->assertCount( 2, wl_core_get_related_entity_ids( $post_1_id ) );

		// TODO tmp assertions: check callback order
		wl_update_post_status( $post_1_id, 'publish' );
		wl_update_post_status( $post_1_id, 'draft' );

		// We don't want anymore the entity to be in draft when referencing posts
		// are set to draft.
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/789
		$this->assertEquals( 'publish', get_post_status( $entity_1_id ) );

		// publish the post.
		wp_publish_post( $post_1_id );

		$this->assertCount( 2, wl_core_get_related_entity_ids( $post_1_id ) );

		// check the post isn't published (1 line is the header).
		$lines = $this->getPostTriples( $post_1_id );
		$this->assertCount( 6, $lines );

		// check all entities published
		$lines = $this->getPostTriples( $entity_1_id );
		$this->assertCount( 6, $lines );
		$this->assertEquals( 'publish', get_post_status( $entity_1_id ) );

		$lines = $this->getPostTriples( $entity_2_id );
		$this->assertCount( 6, $lines );
		$this->assertEquals( 'publish', get_post_status( $entity_2_id ) );

		// unpublish the post.
		wl_update_post_status( $post_1_id, 'draft' );
		$this->assertCount( 2, wl_core_get_related_entity_ids( $post_1_id ) );

		// check the post is not published on Redlink.
		$lines = $this->getPostTriples( $post_1_id );
		$this->assertCount( 1, $lines );

		// create another post
		$post_2_id = wl_create_post( $body_2, 'post-2', uniqid( 'post', true ), 'draft', 'post' );

		// We don't want anymore the entity to be in draft when referencing posts
		// are set to draft.
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/789
		$lines = $this->getPostTriples( $entity_1_id );
		$this->assertCount( 6, $lines, "Unexpected lines: " . var_export( $lines, true ) );

		// publish post 2
		wl_update_post_status( $post_2_id, 'publish' );

		$this->assertCount( 1, wl_core_get_related_entity_ids( $post_2_id ) );

		// check post 2 isn't published.
		$lines = $this->getPostTriples( $post_2_id );
		$this->assertCount( 5, $lines );

		// publish post 1
		wl_update_post_status( $post_1_id, 'publish' );

		$this->assertCount( 2, wl_core_get_related_entity_ids( $post_1_id ) );

		// check post 1 isn't published.
		$lines = $this->getPostTriples( $post_1_id );
		$this->assertCount( 6, $lines );

		$lines = $this->getPostTriples( $entity_1_id );
		$this->assertCount( 6, $lines );

		$lines = $this->getPostTriples( $entity_2_id );
		$this->assertCount( 6, $lines );

		// unpublish post 1

		wl_update_post_status( $post_1_id, 'draft' );
		$this->assertCount( 2, wl_core_get_related_entity_ids( $post_1_id ) );

		$lines = $this->getPostTriples( $post_1_id );
		$this->assertCount( 1, $lines );

		// We don't want anymore the entity to be in draft when referencing posts
		// are set to draft.
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/789
		$lines = $this->getPostTriples( $entity_1_id );
		$this->assertCount( 6, $lines, "Unexpected lines: " . var_export( $lines, true ) );

		$lines = $this->getPostTriples( $entity_2_id );
		$this->assertCount( 6, $lines );

		self::turn_off_entity_push();

	}

	/**
	 * @group redlink
	 */
	function test_updated_when_related_entity_is_trashed() {

		self::turn_on_entity_push();

		// Create draft entity
		$e_id  = wl_create_post( 'ciao', 'entity-1', uniqid( 'entity', true ), 'draft', 'entity' );
		$e_uri = wl_get_entity_uri( $e_id );
		$body  = <<<EOF
            <span itemid="$e_uri">Entity 1</span>
EOF;
		// Create draft post mentioning the entity
		$p_id = wl_create_post( $body, 'post-1', uniqid( 'post', true ), 'draft', 'post' );
		// Publish the post (and related entities)
		wl_update_post_status( $p_id, 'publish' );

		// Verify the post triples contain a reference to the entity.
		$lines = $this->getPostTriples( $p_id );
		$this->assertCount( 5, $lines );

		// Trash the entity
		wl_update_post_status( $e_id, 'trash' );

		// Verify the post triples does no more contain a reference to the entity
		$lines = $this->getPostTriples( $p_id );
		$this->assertCount( 4, $lines );
		// Verify the post triples does no more contain a reference to the entity
		$lines = $this->getPostTriples( $e_id );
		// Verify the post triples does no more contain a reference to the entity
		$this->assertCount( 1, $lines );

		self::turn_off_entity_push();

	}

	function getPostTriples( $post_id ) {

		// Get the post Redlink URI.
		$uri     = Wordlift_Entity_Service::get_instance()->get_uri( $post_id );
		$uri_esc = Wordlift_Sparql_Service::escape( $uri );

		// Prepare the SPARQL query to select label and URL.
		$sparql = "SELECT DISTINCT ?p ?o WHERE { <$uri_esc> ?p ?o . }\n";

		// Send the query and get the response.
		$response = rl_sparql_select( $sparql );

		$this->assertFalse( is_wp_error( $response ), "The query $sparql shouldn't return an error: " . var_export( $response, true ) );

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
	function test_savepostwithouttitle() {

		$post_id      = wl_create_post( 'Sample Post', 'post-1', '', 'publish' );
		$uri          = wl_get_entity_uri( $post_id );
		$expected_uri = wl_configuration_get_redlink_dataset_uri() . "/post/id/$post_id";

		$this->assertEquals( $expected_uri, $uri );
	}

}
