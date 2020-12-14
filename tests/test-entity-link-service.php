<?php
/**
 * @group entity
 */
class Wordlift_Entity_Link_Service_Test extends Wordlift_Unit_Test_Case {

	function setUp() {
		parent::setUp();

		// We don't need to check the remote Linked Data store.
		Wordlift_Unit_Test_Case::turn_off_entity_push();;

	}

	public function test_post_with_archive_slug() {
		// We will test our custom post type archive slug.
		$post_title = 'entity';

		// insert a post and make sure the ID is ok
		$post = get_post( wp_insert_post( array(
			'post_author'  => 1,
			'post_status'  => 'publish',
			'post_content' => rand_str(),
			'post_title'   => $post_title,
			'post_type'    => 'post',
		) ) );

		// Check that the post title
		$this->assertEquals( $post_title, $post->post_name );
	}

	public function test_entity_with_archive_slug() {
		// We will test our custom post type archive slug.
		$post_title = 'entity';

		// insert a entity and make sure the ID is ok
		$entity = get_post( wp_insert_post( array(
			'post_author'  => 1,
			'post_status'  => 'publish',
			'post_content' => rand_str(),
			'post_title'   => $post_title,
			'post_type'    => 'entity',
		) ) );

		// Check that the entity title
		$this->assertEquals( $post_title, $entity->post_name );
	}

	public function test_page_with_archive_slug() {
		// We will test our custom page type archive slug.
		$post_title = 'entity';

		// insert a page and make sure the ID is ok
		$page = get_post( wp_insert_post( array(
			'post_author'  => 1,
			'post_status'  => 'publish',
			'post_content' => rand_str(),
			'post_title'   => $post_title,
			'post_type'    => 'page',
		) ) );

		// Check that the page title
		$this->assertEquals( $post_title, $page->post_name );
	}

	/**
	 * Try creating a post then an entity with the same title and check that the entity post name receives the -2 suffix.
	 */
	public function test_post_then_entity() {

		$post_title = rand_str();

		// insert a post and make sure the ID is ok
		$post = get_post( wp_insert_post( array(
			'post_author'  => 1,
			'post_status'  => 'publish',
			'post_content' => rand_str(),
			'post_title'   => $post_title,
			'post_type'    => 'post',
		) ) );

		$entity = get_post( wp_insert_post( array(
			'post_author'  => 1,
			'post_status'  => 'publish',
			'post_content' => rand_str(),
			'post_title'   => $post_title,
			'post_type'    => 'entity',
		) ) );

		// Check that the entity title
		$this->assertEquals( $post->post_name, $entity->post_name );

	}

	/**
	 * Try creating an entity then a post with the same title and check that the post name receives the -2 suffix.
	 */
	public function test_entity_then_post() {

		$post_title = rand_str();

		$entity = get_post( wp_insert_post( array(
			'post_author'  => 1,
			'post_status'  => 'publish',
			'post_content' => rand_str(),
			'post_title'   => $post_title,
			'post_type'    => 'entity',
		) ) );

		// insert a post and make sure the ID is ok
		$post = get_post( wp_insert_post( array(
			'post_author'  => 1,
			'post_status'  => 'publish',
			'post_content' => rand_str(),
			'post_title'   => $post_title,
			'post_type'    => 'post',
		) ) );

		// Check that the entity title
		$this->assertEquals( $entity->post_name, $post->post_name );

	}

	/**
	 * Test the entity link with an empty slug.
	 */
	public function test_post_link_with_empty_slug() {

		$slug                = '';
		$entity_type_service = new Wordlift_Entity_Post_Type_Service( 'entity', $slug );
		$entity_link_service = new Wordlift_Entity_Link_Service( $entity_type_service, $slug );

		$entity = get_post( wp_insert_post( array(
			'post_author'  => 1,
			'post_status'  => 'publish',
			'post_content' => rand_str(),
			'post_title'   => rand_str(),
			'post_type'    => 'entity',
		) ) );

		// Simulate a standard post link, in the form of /entity-type-slug/entity-name
		$post_link = "/{$entity_type_service->get_slug()}/$entity->post_name/";

		// Get the revised post link, we expect /entity-name (because the slug is empty).
		$new_post_link = $entity_link_service->post_type_link( $post_link, $entity, false, false );

		$this->assertEquals( "/$entity->post_name/", $new_post_link );

	}

	/**
	 * Test the entity link with a random slug.
	 */
	public function test_post_link_with_random_slug() {

		$slug                = rand_str();
		$entity_type_service = new Wordlift_Entity_Post_Type_Service( 'entity', $slug );
		$entity_link_service = new Wordlift_Entity_Link_Service( $entity_type_service, $slug );

		$entity = get_post( wp_insert_post( array(
			'post_author'  => 1,
			'post_status'  => 'publish',
			'post_content' => rand_str(),
			'post_title'   => rand_str(),
			'post_type'    => 'entity',
		) ) );

		// Simulate a standard post link, in the form of /entity-type-slug/entity-name
		$post_link = "/{$entity_type_service->get_slug()}/$entity->post_name/";

		// Get the revised post link, we expect /entity-name (because the slug is empty).
		$new_post_link = $entity_link_service->post_type_link( $post_link, $entity, false, false );

		$this->assertEquals( "/$slug/$entity->post_name/", $new_post_link );

	}

}
