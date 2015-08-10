<?php

/**
 * This file covers tests related entity creation via post creation.
 */

require_once 'functions.php';

class EntityCreationViaPostCreationTest extends WP_UnitTestCase {

	/**
	 * Set up the test.
	 */
	function setUp() {
		parent::setUp();

		wl_configure_wordpress_test();
		wl_empty_blog();
		rl_empty_dataset();
	}

	// Test if the microdata compiling does not fail on an entity with an undefined schema.org type
	function testEntityIsCreatedAndLinkedToThePost() {

		$fake = $this->prepareFakeGlobalPostArrayFromFile(
			'/assets/fake_global_post_array_with_one_entity_linked_as_what.json' 
		);
		$_POST = $fake;
		// Retrieve the entity uri (the first key in wl_entities associative aray)
		$entity_uri = current( array_keys ( $fake['wl_entities' ] ) );
		// Reference the entity to the post content 
		$content    = <<<EOF
    <span itemid="$entity_uri">My entity</span>
EOF;
		// Be sure that the entity does not exist yet
		$entity = wl_get_entity_post_by_uri( $entity_uri );
		$this->assertNull( $entity );
		// Create a post referincing to the created entity
		$post_id = wl_create_post( $content, 'my-post', 'A post' , 'draft');
		// Here the entity should be created instead
		$entity = wl_get_entity_post_by_uri( $entity_uri );
		$this->assertNotNull( $entity );
		// And it should be related to the post as what predicate
		$related_entity_ids = wl_core_get_related_entity_ids( $post_id, array( "predicate" => "what" ) );
		$this->assertCount( 1, $related_entity_ids );
		$this->assertContains( $entity->ID, $related_entity_ids );

	}

	// Test if the microdata compiling does not fail on an entity with an undefined schema.org type
	function testEntityIsCreatedButNotLinkedToThePost() {

		$fake = $this->prepareFakeGlobalPostArrayFromFile(
			'/assets/fake_global_post_array_with_one_entity_linked_as_what.json' 
		);
		$_POST = $fake;
		// Retrieve the entity uri (the first key in wl_entities associative aray)
		$entity_uri = current( array_keys ( $fake['wl_entities' ] ) );
		// Here I DON'T reference the entity to the post content 
		$content    = <<<EOF
    <span>My entity</span>
EOF;
		// Be sure that the entity does not exist yet
		$entity = wl_get_entity_post_by_uri( $entity_uri );
		$this->assertNull( $entity );
		// Create a post referincing to the created entity
		$post_id = wl_create_post( $content, 'my-post', 'A post' , 'draft');
		// Here the entity should be created instead
		$entity = wl_get_entity_post_by_uri( $entity_uri );
		$this->assertNotNull( $entity );
		// And it should be related to the post as what predicate
		$related_entity_ids = wl_core_get_related_entity_ids( $post_id, array( "predicate" => "what" ) );
		$this->assertCount( 0, $related_entity_ids );
	
	}
	function prepareFakeGlobalPostArrayFromFile( $fileName ) {
		$json_data = file_get_contents( dirname( __FILE__ ) . $fileName );
		$json_data = preg_replace(
			'/{{REDLINK_ENDPOINT}}/',
			wl_configuration_get_redlink_dataset_uri(),
			$json_data );
		$data = json_decode( $json_data, true );
		return $data;
	}

}

