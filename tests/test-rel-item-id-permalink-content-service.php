<?php

use Wordlift\Content\WordPress\Wordpress_Content_Id;
use Wordlift\Content\WordPress\Wordpress_Permalink_Content_Service;

class Wordpress_Permalink_Content_Service_Test extends Wordlift_Unit_Test_Case {

	public function test_get_entity_id() {

		$post_id = $this->factory()->post->create();

		$entity_id = Wordpress_Permalink_Content_Service::get_instance()
		                                                ->get_entity_id( Wordpress_Content_Id::create_post( $post_id ) );

		$this->assertNotEmpty( $entity_id, 'We expect the entity ID not to be empty.' );

		$permalink = get_permalink( $post_id );
		$this->assertEquals( "$permalink#post/$post_id", $entity_id,
			"The provided entity ID `$entity_id` does not match the form of `permalink`#post/`post_id`." );

	}

	public function test_get_by_entity_id() {

		$post_id = $this->factory()->post->create();

		$content_service = Wordpress_Permalink_Content_Service::get_instance();
		$entity_id       = $content_service
			->get_entity_id( Wordpress_Content_Id::create_post( $post_id ) );

		$content = $content_service->get_by_entity_id( $entity_id );
		$this->assertEquals( $post_id, $content->get_bag()->ID, 'The post ID is expected to match.' );

	}

	public function test_supports() {

		$content_service = Wordpress_Permalink_Content_Service::get_instance();

		$this->assertTrue(
			$content_service->supports( Wordpress_Content_Id::create_post( 1 ) ),
			'Post should be supported.' );

		$this->assertTrue(
			$content_service->supports( Wordpress_Content_Id::create_term( 1 ) ),
			'Term should be supported.' );

		$this->assertTrue(
			$content_service->supports( Wordpress_Content_Id::create_user( 1 ) ),
			'User should be supported.' );

	}

	public function test_get_by_entity_id_or_same_as() {

		$post_id = $this->factory()->post->create();
		add_post_meta( $post_id, 'entity_same_as', 'http://cloud.example.org/data/entity/0' );

		$content = Wordpress_Permalink_Content_Service::get_instance()
		                                              ->get_by_entity_id_or_same_as( 'http://cloud.example.org/data/entity/0' );
		$this->assertEquals( $post_id, $content->get_bag()->ID, 'The post ID is expected to match.' );

	}
}
