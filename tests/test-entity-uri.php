<?php

/**
 * Class GetEntityUriTest
 * @group entity
 */
class GetEntityUriTest extends Wordlift_Unit_Test_Case {

	function testEntityUriWithConfiguredDatasetUri() {

		\Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

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

}
