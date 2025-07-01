<?php

use Wordlift\Content\WordPress\Wordpress_Content_Id;
use Wordlift\Content\WordPress\Wordpress_User_Content_Legacy_Service;

/**
 * Test the {@link Wordpress_User_Content_Legacy_Service}.
 *
 * @author David Riccitelli
 * @version 3.33.9
 * @group rel-item-id
 */
class Wordpress_User_Content_Legacy_Service_Test extends Wordlift_Unit_Test_Case {

	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage `content_id` must be of type user.
	 * @return void
	 * @throws Exception
	 */
	public function test_set_entity_id_of_not_a_term_type_raises_exception() {

		$post_id = $this->factory()->post->create();

		Wordpress_User_Content_Legacy_Service::get_instance()
		                                     ->set_entity_id(
			                                     Wordpress_Content_Id::create_post( $post_id ),
			                                     'https://data.example.org/dataset/entity/0' );

	}

	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage `uri` can't be empty
	 * @return void
	 * @throws Exception
	 */
	public function test_set_entity_id_with_empty_uri_raises_exception() {

		$user_id = $this->factory()->user->create();

		Wordpress_User_Content_Legacy_Service::get_instance()
		                                     ->set_entity_id(
			                                     Wordpress_Content_Id::create_user( $user_id ),
			                                     '' );

	}

	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage `uri` must be within the dataset URI scope.
	 * @return void
	 * @throws Exception
	 */
	public function test_set_entity_id_outside_dataset_scope_raises_exception() {

		$user_id = $this->factory()->user->create();

		Wordpress_User_Content_Legacy_Service::get_instance()
		                                     ->set_entity_id(
			                                     Wordpress_Content_Id::create_user( $user_id ),
			                                     'https://data.example.org/dataset/entity/0' );

	}

	public function test_set_entity_id_with_relative_uri() {

		$user_id         = $this->factory()->user->create();
		$content_id      = Wordpress_Content_Id::create_user( $user_id );
		$content_service = Wordpress_User_Content_Legacy_Service::get_instance();
		$content_service->set_entity_id( $content_id, 'entity/0' );

		$this->assertEquals(
			'https://data.localdomain.localhost/dataset/entity/0',
			$content_service->get_entity_id( $content_id ) );

	}

	public function test_set_entity_id_with_absolute_uri() {

		$user_id         = $this->factory()->user->create();
		$content_id      = Wordpress_Content_Id::create_user( $user_id );
		$content_service = Wordpress_User_Content_Legacy_Service::get_instance();
		$content_service->set_entity_id( $content_id, 'https://data.localdomain.localhost/dataset/entity/0' );

		$this->assertEquals(
			'https://data.localdomain.localhost/dataset/entity/0',
			$content_service->get_entity_id( $content_id ) );

	}

	public function test_get_entity_id() {

		$user_id    = $this->factory()->user->create( array( 'user_name' => 'user-name' ) );
		$content_id = Wordpress_Content_Id::create_user( $user_id );

		$content_service = Wordpress_User_Content_Legacy_Service::get_instance();
		$content_service->set_entity_id( $content_id, 'https://data.localdomain.localhost/dataset/user/user-name' );
		$entity_id = $content_service->get_entity_id( $content_id );

		$this->assertEquals(
			'https://data.localdomain.localhost/dataset/user/user-name', $entity_id,
			"The provided entity ID `$entity_id` does not match the expected id." );

	}

	public function test_get_by_entity_id() {

		$user_id    = $this->factory()->user->create();
		$content_id = Wordpress_Content_Id::create_user( $user_id );

		$content_service = Wordpress_User_Content_Legacy_Service::get_instance();
		$content_service->set_entity_id( $content_id, 'https://data.localdomain.localhost/dataset/user/0' );
		$content = $content_service->get_by_entity_id( 'https://data.localdomain.localhost/dataset/user/0' );

		$this->assertEquals( $user_id, $content->get_bag()->ID, 'The user ID is expected to match.' );

	}

	public function test_supports() {

		$content_service = Wordpress_User_Content_Legacy_Service::get_instance();

		$this->assertFalse(
			$content_service->supports( Wordpress_Content_Id::create_post( 1 ) ),
			'Post should not be supported.' );

		$this->assertFalse(
			$content_service->supports( Wordpress_Content_Id::create_term( 1 ) ),
			'Term should not be supported.' );

		$this->assertTrue(
			$content_service->supports( Wordpress_Content_Id::create_user( 1 ) ),
			'User should be supported.' );

	}

	public function test_get_by_entity_id_or_same_as() {

		$user_id = $this->factory()->user->create();
		add_user_meta( $user_id, 'entity_same_as', 'http://cloud.example.org/data/entity/0' );

		$content = Wordpress_User_Content_Legacy_Service::get_instance()
		                                                ->get_by_entity_id_or_same_as( 'http://cloud.example.org/data/entity/0' );
		$this->assertEquals( $user_id, $content->get_bag()->ID, 'The user ID is expected to match.' );

	}

}
