<?php
require_once 'functions.php';

class GetEntityUriTest extends Wordlift_Unit_Test_Case {

	/**
	 * Set up the test.
	 */
	function setUp() {
		parent::setUp();

		// We don't need to check the remote Linked Data store.
		Wordlift_Unit_Test_Case::turn_off_entity_push();;

		wl_configure_wordpress_test();

	}

	function testEntityUriWithConfiguredDatasetUri() {

		$post_id     = wl_create_post( 'A body', 'post-1', uniqid( 'post', true ), 'draft', 'post' );
		$dataset_uri = wl_configuration_get_redlink_dataset_uri();
		$this->assertNotEmpty( $dataset_uri );
		$entity_uri = wl_get_entity_uri( $post_id );
		$this->assertNotNull( $entity_uri );
		// Check the are custom meta set for the current post
		$meta = get_post_meta( $post_id, WL_ENTITY_URL_META_NAME );
		$this->assertNotEmpty( $meta );
		$this->assertCount( 1, $meta );
		$this->assertEquals( $entity_uri, $meta[0] );

	}

	function testEntityUriWithMissingDatasetUri() {

		$post_id     = wl_create_post( 'A body', 'post-1', uniqid( 'post', true ), 'draft', 'post' );
		$dataset_uri = wl_configuration_get_redlink_dataset_uri();
		$this->assertNotEmpty( $dataset_uri );
		// Remove the dataset uri
		wl_configuration_set_redlink_dataset_uri( '' );
		$entity_uri = wl_get_entity_uri( $post_id );
		// Check the wl_get_entity_uri properly returns null
		$this->assertNull( $entity_uri );
		// Check the are not custom meta set for the current post
		$this->assertEmpty( get_post_meta( $post_id, WL_ENTITY_URL_META_NAME ) );
		// Set the dataset uri again
		wl_configuration_set_redlink_dataset_uri( $dataset_uri );

	}

}
