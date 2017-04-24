<?php
/**
 * Tests: Post to JSON-LD Converter.
 *
 * @since   3.10.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Post_To_Jsonld_Converter_Test} class.
 *
 * @since   3.10.0
 * @package Wordlift
 */
class Wordlift_Post_To_Jsonld_Converter_Test extends Wordlift_Unit_Test_Case {

	/**
	 *  A {@link Wordlift_Post_To_Jsonld_Converter} instance.
	 *
	 * @since  3.10.0
	 * @access private
	 * @var \Wordlift_Post_To_Jsonld_Converter $post_to_jsonld_converter A {@link Wordlift_Post_To_Jsonld_Converter} instance.
	 */
	private $post_to_jsonld_converter;

	/**
	 * A {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.10.0
	 * @access private
	 * @var \Wordlift_Configuration_Service $configuration_service A {@link Wordlift_Configuration_Service} instance.
	 */
	private $configuration_service;

	/**
	 * A {@link Wordlift_Entity_Service} instance.
	 *
	 * @since    3.10.0
	 * @access   private
	 * @var \Wordlift_Entity_Service $entity_service A {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * A {@link Wordlift_User_Service} instance.
	 *
	 * @since    3.10.0
	 * @access   private
	 * @var \Wordlift_User_Service $user_service A {@link Wordlift_User_Service} instance.
	 */
	private $user_service;

	/**
	 * A {@link Wordlift_Entity_Type_Service} instance.
	 *
	 * @since  3.10.0
	 * @access private
	 * @var \Wordlift_Entity_Type_Service $entity_type_service A {@link Wordlift_Entity_Type_Service} instance.
	 */
	private $entity_type_service;

	/**
	 * A mock-up WordPress user.
	 *
	 * @since  3.10.0
	 * @access private
	 * @var WP_User $author A mock-up WordPress user.
	 */
	private $author;

	/**
	 * WP's mockup user uri.
	 *
	 * @since  3.10.0
	 * @access private
	 * @var WP_User $author_uri WP's mockup user uri.
	 */
	private $author_uri;

	/**
	 * {@inheritdoc}
	 */
	function setUp() {
		parent::setUp();

		// Disable sending SPARQL queries, since we don't need it.
		Wordlift_Unit_Test_Case::turn_off_entity_push();;

		$wordlift                       = new Wordlift_Test();
		$this->post_to_jsonld_converter = $wordlift->get_post_to_jsonld_converter();
		$this->configuration_service    = $wordlift->get_configuration_service();
		$this->entity_service           = $wordlift->get_entity_service();
		$this->user_service             = $wordlift->get_user_service();
		$this->entity_type_service      = $wordlift->get_entity_type_service();

		// Check that we have services' instances.
		$this->assertNotNull( $this->post_to_jsonld_converter );
		$this->assertNotNull( $this->configuration_service );
		$this->assertNotNull( $this->entity_service );
		$this->assertNotNull( $this->user_service );
		$this->assertNotNull( $this->entity_type_service );

		// Create some more mock-up data.
		$this->author     = $this->factory->user->create_and_get();
		$this->author_uri = $this->user_service->get_uri( $this->author->ID );

	}

	/**
	 * Test a Post without entities.
	 *
	 * @since 3.10.0
	 */
	public function test_a_post_without_entities() {

		$post      = $this->factory->post->create_and_get( array( 'post_author' => $this->author->ID ) );
		$post_uri  = $this->entity_service->get_uri( $post->ID );
		$permalink = get_permalink( $post->ID );

		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references );

		// Check that we don't have reference.
		$this->assertCount( 0, $references );

		// Check that we have 9 properties, not one more than that.
		$this->assertCount( 9, $jsonld );

		// Check the json-ld values.
		$this->assertEquals( 'http://schema.org', $jsonld['@context'] );
		$this->assertEquals( $post_uri, $jsonld['@id'] );
		$this->assertEquals( 'Article', $jsonld['@type'] );
		$this->assertEquals( $post->post_excerpt, $jsonld['description'] );
		$this->assertEquals( $post->post_title, $jsonld['headline'] );
		// See https://github.com/insideout10/wordlift-plugin/issues/451
		$this->assertEquals( $permalink, $jsonld['mainEntityOfPage'] );
		$this->assertEquals( 'Person', $jsonld['author']['@type'] );
		$this->assertEquals( $this->author_uri, $jsonld['author']['@id'] );
		$this->assertEquals( $this->author->display_name, $jsonld['author']['name'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_date_gmt, false ), $jsonld['datePublished'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_modified_gmt, false ), $jsonld['dateModified'] );

	}

	/**
	 * Test a Page without entities.
	 *
	 * @since 3.10.0
	 */
	public function test_a_page_without_entities() {

		$post      = $this->factory->post->create_and_get( array(
			'post_type'   => 'page',
			'post_author' => $this->author->ID,
		) );
		$post_uri  = $this->entity_service->get_uri( $post->ID );
		$permalink = get_permalink( $post->ID );

		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references );

		// Check that we don't have reference.
		$this->assertCount( 0, $references );

		// Check that we have 9 properties, not one more than that.
		$this->assertCount( 9, $jsonld );

		// Check the json-ld values.
		$this->assertEquals( 'http://schema.org', $jsonld['@context'] );
		$this->assertEquals( $post_uri, $jsonld['@id'] );
		$this->assertEquals( 'Article', $jsonld['@type'] );
		$this->assertEquals( $post->post_excerpt, $jsonld['description'] );
		$this->assertEquals( $post->post_title, $jsonld['headline'] );
		// See https://github.com/insideout10/wordlift-plugin/issues/451
		$this->assertEquals( $permalink, $jsonld['mainEntityOfPage'] );
		$this->assertEquals( 'Person', $jsonld['author']['@type'] );
		$this->assertEquals( $this->author_uri, $jsonld['author']['@id'] );
		$this->assertEquals( $this->author->display_name, $jsonld['author']['name'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_date_gmt, false ), $jsonld['datePublished'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_modified_gmt, false ), $jsonld['dateModified'] );

	}

	/**
	 * Test a Post with a Person as Publisher.
	 *
	 * @since 3.10.0
	 */
	public function test_a_post_with_a_person_publisher() {

		$publisher = $this->entity_factory->create_and_get();
		$this->entity_type_service->set( $publisher->ID, 'http://schema.org/Person' );

		$type = $this->entity_type_service->get( $publisher->ID );

		$this->assertTrue( is_array( $type ) );
		$this->assertTrue( 0 < sizeof( $type ) );

		$publisher_uri = $this->entity_service->get_uri( $publisher->ID );

		$this->configuration_service->set_publisher_id( $publisher->ID );

		$this->assertEquals( $publisher->ID, $this->configuration_service->get_publisher_id() );

		$post      = $this->factory->post->create_and_get( array( 'post_author' => $this->author->ID ) );
		$post_uri  = $this->entity_service->get_uri( $post->ID );
		$permalink = get_permalink( $post->ID );

		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references );

		// Check that we don't have reference.
		$this->assertCount( 0, $references );

		// Check that we have 10 properties, not one more than that.
		$this->assertCount( 10, $jsonld );

		// Check the json-ld values.
		$this->assertEquals( 'http://schema.org', $jsonld['@context'] );
		$this->assertEquals( $post_uri, $jsonld['@id'] );
		$this->assertEquals( 'Article', $jsonld['@type'] );
		$this->assertEquals( $post->post_excerpt, $jsonld['description'] );
		$this->assertEquals( $post->post_title, $jsonld['headline'] );
		// See https://github.com/insideout10/wordlift-plugin/issues/451
		$this->assertEquals( $permalink, $jsonld['mainEntityOfPage'] );
		$this->assertEquals( 'Person', $jsonld['author']['@type'] );
		$this->assertEquals( $this->author_uri, $jsonld['author']['@id'] );
		$this->assertEquals( $this->author->display_name, $jsonld['author']['name'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_date_gmt, false ), $jsonld['datePublished'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_modified_gmt, false ), $jsonld['dateModified'] );

		// Check the publisher.
		$this->assertCount( 3, $jsonld['publisher'] );
		$this->assertEquals( 'Person', $jsonld['publisher']['@type'] );
		$this->assertEquals( $publisher_uri, $jsonld['publisher']['@id'] );
		$this->assertEquals( $publisher->post_title, $jsonld['publisher']['name'] );

	}

	/**
	 * Test a Post with an Organization as Publisher.
	 *
	 * @since 3.10.0
	 */
	public function test_a_post_with_an_organization_publisher_without_logo() {

		$publisher = $this->entity_factory->create_and_get();
		$this->entity_type_service->set( $publisher->ID, 'http://schema.org/Organization' );
		$publisher_uri = $this->entity_service->get_uri( $publisher->ID );

		$this->configuration_service->set_publisher_id( $publisher->ID );

		$post      = $this->factory->post->create_and_get( array( 'post_author' => $this->author->ID ) );
		$post_uri  = $this->entity_service->get_uri( $post->ID );
		$permalink = get_permalink( $post->ID );

		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references );

		// Check that we don't have reference.
		$this->assertCount( 0, $references );

		// Check that we have 10 properties, not one more than that.
		$this->assertCount( 10, $jsonld );

		// Check the json-ld values.
		$this->assertEquals( 'http://schema.org', $jsonld['@context'] );
		$this->assertEquals( $post_uri, $jsonld['@id'] );
		$this->assertEquals( 'Article', $jsonld['@type'] );
		$this->assertEquals( $post->post_excerpt, $jsonld['description'] );
		$this->assertEquals( $post->post_title, $jsonld['headline'] );
		// See https://github.com/insideout10/wordlift-plugin/issues/451
		$this->assertEquals( $permalink, $jsonld['mainEntityOfPage'] );
		$this->assertEquals( 'Person', $jsonld['author']['@type'] );
		$this->assertEquals( $this->author_uri, $jsonld['author']['@id'] );
		$this->assertEquals( $this->author->display_name, $jsonld['author']['name'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_date_gmt, false ), $jsonld['datePublished'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_modified_gmt, false ), $jsonld['dateModified'] );

		// Check the publisher.
		$this->assertCount( 3, $jsonld['publisher'] );
		$this->assertEquals( 'Organization', $jsonld['publisher']['@type'] );
		$this->assertEquals( $publisher_uri, $jsonld['publisher']['@id'] );
		$this->assertEquals( $publisher->post_title, $jsonld['publisher']['name'] );

	}

	/**
	 * Helper function to create attachment DB without uploading FilesystemIterator.
	 *
	 * @since 3.10
	 *
	 * @param    string  $filename The filename the attachement should have
	 * @param    integer $width    The width of the image
	 * @param    integer $height   The height of the image
	 * @param    integer $post_id  The ID of the post to associated with the attachment
	 *
	 * @return    integer        The ID of the attachment created
	 **/
	private function make_dummy_attachment( $filename, $width, $height, $post_id ) {

		// Create an attachment.
		$attachment_id = $this->factory->attachment->create_object( $filename, $post_id, array(
			'post_mime_type' => 'image/jpeg',
			'post_type'      => 'attachment',
		) );

		// Update the attachment metadata.
		wp_update_attachment_metadata( $attachment_id, array(
			'width'  => $width,
			'height' => $height,
			'file'   => $filename,
		) );

		// Finally return the attachment id.
		return $attachment_id;
	}

	/**
	 * Test a Post with an Organization with a Logo as Publisher.
	 *
	 * @since 3.10.0
	 */
	public function test_a_post_with_an_organization_publisher_with_logo() {

		// Create a publisher.
		$publisher = $this->entity_factory->create_and_get();
		$this->entity_type_service->set( $publisher->ID, 'http://schema.org/Organization' );
		$publisher_uri = $this->entity_service->get_uri( $publisher->ID );

		// Set the publisher.
		$this->configuration_service->set_publisher_id( $publisher->ID );

		// Set the logo for the publisher.
		$attachment_id = $this->make_dummy_attachment( 'image.jpg', 200, 100, $publisher->ID );
		set_post_thumbnail( $publisher->ID, $attachment_id );
		$attachment_url = wp_get_attachment_url( $attachment_id );

		// Create a random post.
		$post      = $this->factory->post->create_and_get( array( 'post_author' => $this->author->ID ) );
		$post_uri  = $this->entity_service->get_uri( $post->ID );
		$permalink = get_permalink( $post->ID );

		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references );

		// Check that we don't have reference.
		$this->assertCount( 0, $references );

		// Check that we have 10 properties, not one more than that.
		$this->assertCount( 10, $jsonld );

		// Check the json-ld values.
		$this->assertEquals( 'http://schema.org', $jsonld['@context'] );
		$this->assertEquals( $post_uri, $jsonld['@id'] );
		$this->assertEquals( 'Article', $jsonld['@type'] );
		$this->assertEquals( $post->post_excerpt, $jsonld['description'] );
		$this->assertEquals( $post->post_title, $jsonld['headline'] );
		// See https://github.com/insideout10/wordlift-plugin/issues/451
		$this->assertEquals( $permalink, $jsonld['mainEntityOfPage'] );
		$this->assertEquals( 'Person', $jsonld['author']['@type'] );
		$this->assertEquals( $this->author_uri, $jsonld['author']['@id'] );
		$this->assertEquals( $this->author->display_name, $jsonld['author']['name'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_date_gmt, false ), $jsonld['datePublished'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_modified_gmt, false ), $jsonld['dateModified'] );

		// Check the publisher.
		$this->assertCount( 4, $jsonld['publisher'] );
		$this->assertEquals( 'Organization', $jsonld['publisher']['@type'] );
		$this->assertEquals( $publisher_uri, $jsonld['publisher']['@id'] );
		$this->assertEquals( $publisher->post_title, $jsonld['publisher']['name'] );

		// Check the logo.
		$this->assertCount( 4, $jsonld['publisher']['logo'] );
		$this->assertEquals( 'ImageObject', $jsonld['publisher']['logo']['@type'] );
		$this->assertEquals( $attachment_url, $jsonld['publisher']['logo']['url'] );
		$this->assertEquals( 200, $jsonld['publisher']['logo']['width'] );
		$this->assertEquals( 100, $jsonld['publisher']['logo']['height'] );

	}

	/**
	 * Test a Post with Entities.
	 *
	 * @since 3.10.0
	 */
	public function test_a_post_with_entities() {

		// Create a post.
		$post      = $this->factory->post->create_and_get( array( 'post_author' => $this->author->ID ) );
		$post_uri  = $this->entity_service->get_uri( $post->ID );
		$permalink = get_permalink( $post->ID );

		// Create a couple of entities.
		$entity_1 = $this->entity_factory->create_and_get();
		$this->entity_type_service->set( $entity_1->ID, 'http://schema.org/Organization' );
		$entity_1_uri = $this->entity_service->get_uri( $entity_1->ID );

		$entity_2 = $this->entity_factory->create_and_get();
		$this->entity_type_service->set( $entity_2->ID, 'http://schema.org/Person' );
		$entity_2_uri = $this->entity_service->get_uri( $entity_2->ID );

		// Bind the entities to the post.
		wl_core_add_relation_instances( $post->ID, WL_WHO_RELATION, array(
			$entity_1->ID,
			$entity_2->ID,
		) );

		//
		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references );

		// Check that we have 2 references.
		$this->assertCount( 2, $references );

		// Check that we have 10 properties, not one more than that.
		$this->assertCount( 10, $jsonld );

		// Check the json-ld values.
		$this->assertEquals( 'http://schema.org', $jsonld['@context'] );
		$this->assertEquals( $post_uri, $jsonld['@id'] );
		$this->assertEquals( 'Article', $jsonld['@type'] );
		$this->assertEquals( $post->post_excerpt, $jsonld['description'] );
		$this->assertEquals( $post->post_title, $jsonld['headline'] );
		// See https://github.com/insideout10/wordlift-plugin/issues/451
		$this->assertEquals( $permalink, $jsonld['mainEntityOfPage'] );
		$this->assertEquals( 'Person', $jsonld['author']['@type'] );
		$this->assertEquals( $this->author_uri, $jsonld['author']['@id'] );
		$this->assertEquals( $this->author->display_name, $jsonld['author']['name'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_date_gmt, false ), $jsonld['datePublished'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_modified_gmt, false ), $jsonld['dateModified'] );

		//
		$this->assertCount( 2, $jsonld['mentions'] );
		$this->assertEquals( $entity_1_uri, $jsonld['mentions'][0]['@id'] );
		$this->assertEquals( $entity_2_uri, $jsonld['mentions'][1]['@id'] );

	}

	/**
	 * Test a post with a `mentions` and an `about`.
	 *
	 * @since 3.12.0
	 */
	public function test_a_post_with_one_mentions_and_one_about() {

		// Create a post.
		$post      = $this->factory->post->create_and_get( array(
			'post_title'  => 'Lorem Ipsum',
			'post_author' => $this->author->ID,
		) );
		$post_uri  = $this->entity_service->get_uri( $post->ID );
		$permalink = get_permalink( $post->ID );

		// Create a couple of entities.
		$entity_1 = $this->entity_factory->create_and_get();
		$this->entity_type_service->set( $entity_1->ID, 'http://schema.org/Organization' );
		$entity_1_uri = $this->entity_service->get_uri( $entity_1->ID );

		$entity_2 = $this->entity_factory->create_and_get( array( 'post_title' => 'Lorem' ) );
		$this->entity_type_service->set( $entity_2->ID, 'http://schema.org/Person' );
		$entity_2_uri = $this->entity_service->get_uri( $entity_2->ID );

		// Bind the entities to the post.
		wl_core_add_relation_instances( $post->ID, WL_WHO_RELATION, array(
			$entity_1->ID,
			$entity_2->ID,
		) );

		//
		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references );

		// Check that we have 2 references.
		$this->assertCount( 2, $references );

		// Check that we have 10 properties, not one more than that.
		$this->assertCount( 11, $jsonld );

		// Check the json-ld values.
		$this->assertEquals( 'http://schema.org', $jsonld['@context'] );
		$this->assertEquals( $post_uri, $jsonld['@id'] );
		$this->assertEquals( 'Article', $jsonld['@type'] );
		$this->assertEquals( $post->post_excerpt, $jsonld['description'] );
		$this->assertEquals( $post->post_title, $jsonld['headline'] );
		$this->assertEquals( 'WebPage', $jsonld['mainEntityOfPage']['@type'] );
		$this->assertEquals( $permalink, $jsonld['mainEntityOfPage']['@id'] );
		$this->assertEquals( 'Person', $jsonld['author']['@type'] );
		$this->assertEquals( $this->author_uri, $jsonld['author']['@id'] );
		$this->assertEquals( $this->author->display_name, $jsonld['author']['name'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_date_gmt, false ), $jsonld['datePublished'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_modified_gmt, false ), $jsonld['dateModified'] );

		//
		$this->assertCount( 1, $jsonld['mentions'] );
		$this->assertEquals( $entity_1_uri, $jsonld['mentions'][0]['@id'] );

		$this->assertCount( 1, $jsonld['about'] );
		$this->assertEquals( $entity_2_uri, $jsonld['about'][0]['@id'] );

	}

	/**
	 * Test a post with a `mentions` and an `about`.
	 *
	 * @since 3.12.0
	 */
	public function test_a_post_with_one_mentions_and_one_about_with_synonym() {

		// Create a post.
		$post      = $this->factory->post->create_and_get( array(
			'post_title'  => 'Lorem Ipsum',
			'post_author' => $this->author->ID,
		) );
		$post_uri  = $this->entity_service->get_uri( $post->ID );
		$permalink = get_permalink( $post->ID );

		// Create a couple of entities.
		$entity_1 = $this->entity_factory->create_and_get();
		$this->entity_type_service->set( $entity_1->ID, 'http://schema.org/Organization' );
		$entity_1_uri = $this->entity_service->get_uri( $entity_1->ID );

		$entity_2 = $this->entity_factory->create_and_get( array( 'post_title' => 'Whatever' ) );
		$this->entity_type_service->set( $entity_2->ID, 'http://schema.org/Person' );
		$entity_2_uri = $this->entity_service->get_uri( $entity_2->ID );
		$this->entity_service->set_alternative_labels( $entity_2->ID, array( 'Lorem' ) );

		// Bind the entities to the post.
		wl_core_add_relation_instances( $post->ID, WL_WHO_RELATION, array(
			$entity_1->ID,
			$entity_2->ID,
		) );

		//
		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references );

		// Check that we have 2 references.
		$this->assertCount( 2, $references );

		// Check that we have 10 properties, not one more than that.
		$this->assertCount( 11, $jsonld );

		// Check the json-ld values.
		$this->assertEquals( 'http://schema.org', $jsonld['@context'] );
		$this->assertEquals( $post_uri, $jsonld['@id'] );
		$this->assertEquals( 'Article', $jsonld['@type'] );
		$this->assertEquals( $post->post_excerpt, $jsonld['description'] );
		$this->assertEquals( $post->post_title, $jsonld['headline'] );
		$this->assertEquals( 'WebPage', $jsonld['mainEntityOfPage']['@type'] );
		$this->assertEquals( $permalink, $jsonld['mainEntityOfPage']['@id'] );
		$this->assertEquals( 'Person', $jsonld['author']['@type'] );
		$this->assertEquals( $this->author_uri, $jsonld['author']['@id'] );
		$this->assertEquals( $this->author->display_name, $jsonld['author']['name'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_date_gmt, false ), $jsonld['datePublished'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_modified_gmt, false ), $jsonld['dateModified'] );

		//
		$this->assertCount( 1, $jsonld['mentions'] );
		$this->assertEquals( $entity_1_uri, $jsonld['mentions'][0]['@id'] );

		$this->assertCount( 1, $jsonld['about'] );
		$this->assertEquals( $entity_2_uri, $jsonld['about'][0]['@id'] );

	}

	/**
	 * Test a Post with featured image and Entities.
	 *
	 * @since 3.10.0
	 */
	public function test_a_post_with_featured_image_and_entities() {
		// Create a post.
		$post      = $this->factory->post->create_and_get( array( 'post_author' => $this->author->ID ) );
		$post_uri  = $this->entity_service->get_uri( $post->ID );
		$permalink = get_permalink( $post->ID );

		// attache a thumbnail to the Post
		$attachment_id = $this->make_dummy_attachment( 'image.jpg', 200, 100, $post->ID );
		set_post_thumbnail( $post->ID, $attachment_id );
		$attachment_url = wp_get_attachment_url( $attachment_id );

		// Create a couple of entities.
		$entity_1 = $this->entity_factory->create_and_get();
		$this->entity_type_service->set( $entity_1->ID, 'http://schema.org/Organization' );
		$entity_1_uri = $this->entity_service->get_uri( $entity_1->ID );

		$entity_2 = $this->entity_factory->create_and_get();
		$this->entity_type_service->set( $entity_2->ID, 'http://schema.org/Person' );
		$entity_2_uri = $this->entity_service->get_uri( $entity_2->ID );

		// Bind the entities to the post.
		wl_core_add_relation_instances( $post->ID, WL_WHO_RELATION, array(
			$entity_1->ID,
			$entity_2->ID,
		) );

		//
		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references );

		// Check that we have 2 references.
		$this->assertCount( 2, $references );

		// Check that we have 11 properties, not one more than that.
		$this->assertCount( 11, $jsonld );

		// Check the json-ld values.
		$this->assertEquals( 'http://schema.org', $jsonld['@context'] );
		$this->assertEquals( $post_uri, $jsonld['@id'] );
		$this->assertEquals( 'Article', $jsonld['@type'] );
		$this->assertEquals( $post->post_excerpt, $jsonld['description'] );
		$this->assertEquals( $post->post_title, $jsonld['headline'] );
		// See https://github.com/insideout10/wordlift-plugin/issues/451
		$this->assertEquals( $permalink, $jsonld['mainEntityOfPage'] );
		$this->assertEquals( 'Person', $jsonld['author']['@type'] );
		$this->assertEquals( $this->author_uri, $jsonld['author']['@id'] );
		$this->assertEquals( $this->author->display_name, $jsonld['author']['name'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_date_gmt, false ), $jsonld['datePublished'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_modified_gmt, false ), $jsonld['dateModified'] );

		// Check that we have exactly one images.
		$this->assertCount( 1, $jsonld['image'] );

		// Check the thumbnail.
		$this->assertCount( 4, $jsonld['image']['0'] );
		$this->assertEquals( 'ImageObject', $jsonld['image']['0']['@type'] );
		$this->assertEquals( $attachment_url, $jsonld['image']['0']['url'] );
		$this->assertEquals( 200, $jsonld['image']['0']['width'] );
		$this->assertEquals( 100, $jsonld['image']['0']['height'] );

		//
		$this->assertCount( 2, $jsonld['mentions'] );
		$this->assertEquals( $entity_1_uri, $jsonld['mentions'][0]['@id'] );
		$this->assertEquals( $entity_2_uri, $jsonld['mentions'][1]['@id'] );

	}

	/**
	 * Test a Post with featured image, unincluded associated images and Entities.
	 *
	 * @since 3.10.0
	 */
	public function test_a_post_with_attached_images_and_entities() {
		// Create a post.
		$post      = $this->factory->post->create_and_get( array( 'post_author' => $this->author->ID ) );
		$post_uri  = $this->entity_service->get_uri( $post->ID );
		$permalink = get_permalink( $post->ID );

		// attache a thumbnail to the Post
		$attachment_id = $this->make_dummy_attachment( 'image.jpg', 200, 100, $post->ID );
		set_post_thumbnail( $post->ID, $attachment_id );
		$attachment_url = wp_get_attachment_url( $attachment_id );

		// attache an image outside of content
		$attachment_id   = $this->make_dummy_attachment( 'image2.jpg', 300, 200, $post->ID );
		$attachment2_url = wp_get_attachment_url( $attachment_id );

		// Create a couple of entities.
		$entity_1 = $this->entity_factory->create_and_get();
		$this->entity_type_service->set( $entity_1->ID, 'http://schema.org/Organization' );
		$entity_1_uri = $this->entity_service->get_uri( $entity_1->ID );

		$entity_2 = $this->entity_factory->create_and_get();
		$this->entity_type_service->set( $entity_2->ID, 'http://schema.org/Person' );
		$entity_2_uri = $this->entity_service->get_uri( $entity_2->ID );

		// Bind the entities to the post.
		wl_core_add_relation_instances( $post->ID, WL_WHO_RELATION, array(
			$entity_1->ID,
			$entity_2->ID,
		) );

		//
		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references );

		// Check that we have 2 references.
		$this->assertCount( 2, $references );

		// Check that we have 11 properties, not one more than that.
		$this->assertCount( 11, $jsonld );

		// Check the json-ld values.
		$this->assertEquals( 'http://schema.org', $jsonld['@context'] );
		$this->assertEquals( $post_uri, $jsonld['@id'] );
		$this->assertEquals( 'Article', $jsonld['@type'] );
		$this->assertEquals( $post->post_excerpt, $jsonld['description'] );
		$this->assertEquals( $post->post_title, $jsonld['headline'] );
		// See https://github.com/insideout10/wordlift-plugin/issues/451
		$this->assertEquals( $permalink, $jsonld['mainEntityOfPage'] );
		$this->assertEquals( 'Person', $jsonld['author']['@type'] );
		$this->assertEquals( $this->author_uri, $jsonld['author']['@id'] );
		$this->assertEquals( $this->author->display_name, $jsonld['author']['name'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_date_gmt, false ), $jsonld['datePublished'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_modified_gmt, false ), $jsonld['dateModified'] );

		// Check that we have exactly one images.
		$this->assertCount( 1, $jsonld['image'] );

		// Check the thumbnail.
		$this->assertCount( 4, $jsonld['image']['0'] );
		$this->assertEquals( 'ImageObject', $jsonld['image']['0']['@type'] );
		$this->assertEquals( $attachment_url, $jsonld['image']['0']['url'] );
		$this->assertEquals( 200, $jsonld['image']['0']['width'] );
		$this->assertEquals( 100, $jsonld['image']['0']['height'] );

		//
		$this->assertCount( 2, $jsonld['mentions'] );
		$this->assertEquals( $entity_1_uri, $jsonld['mentions'][0]['@id'] );
		$this->assertEquals( $entity_2_uri, $jsonld['mentions'][1]['@id'] );
	}

	/**
	 * Test a Post with featured image, embedded images and Entities.
	 *
	 * @since 3.10.0
	 */
	public function test_a_post_with_embedded_images_and_entities() {

		// Attach an image attached to some other post.
		$other_post      = $this->factory->post->create_and_get( array( 'post_author' => $this->author->ID ) );
		$attachment_id   = $this->make_dummy_attachment( 'otherimage.jpg', 300, 200, $other_post->ID );
		$attachment2_url = wp_get_attachment_url( $attachment_id );

		// Create a post that include an img of an attachment and an external URL
		$post      = $this->factory->post->create_and_get( array(
			'post_author'  => $this->author->ID,
			'post_content' => 'text <img src="' . $attachment2_url . '">' . "\n" .
			                  'more text <a href=""><img src="http://example.org">text</a>',
		) );
		$post_uri  = $this->entity_service->get_uri( $post->ID );
		$permalink = get_permalink( $post->ID );

		// attache a thumbnail to the Post
		$attachment_id = $this->make_dummy_attachment( 'image.jpg', 200, 100, $post->ID );
		set_post_thumbnail( $post->ID, $attachment_id );
		$attachment_url = wp_get_attachment_url( $attachment_id );

		// Create a couple of entities.
		$entity_1 = $this->entity_factory->create_and_get();
		$this->entity_type_service->set( $entity_1->ID, 'http://schema.org/Organization' );
		$entity_1_uri = $this->entity_service->get_uri( $entity_1->ID );

		$entity_2 = $this->entity_factory->create_and_get();
		$this->entity_type_service->set( $entity_2->ID, 'http://schema.org/Person' );
		$entity_2_uri = $this->entity_service->get_uri( $entity_2->ID );

		// Bind the entities to the post.
		wl_core_add_relation_instances( $post->ID, WL_WHO_RELATION, array(
			$entity_1->ID,
			$entity_2->ID,
		) );

		//
		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references );

		// Check that we have 2 references.
		$this->assertCount( 2, $references );

		// Check that we have 11 properties, not one more than that.
		$this->assertCount( 11, $jsonld );

		// Check the json-ld values.
		$this->assertEquals( 'http://schema.org', $jsonld['@context'] );
		$this->assertEquals( $post_uri, $jsonld['@id'] );
		$this->assertEquals( 'Article', $jsonld['@type'] );
		$this->assertEquals( $post->post_excerpt, $jsonld['description'] );
		$this->assertEquals( $post->post_title, $jsonld['headline'] );
		// See https://github.com/insideout10/wordlift-plugin/issues/451
		$this->assertEquals( $permalink, $jsonld['mainEntityOfPage'] );
		$this->assertEquals( 'Person', $jsonld['author']['@type'] );
		$this->assertEquals( $this->author_uri, $jsonld['author']['@id'] );
		$this->assertEquals( $this->author->display_name, $jsonld['author']['name'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_date_gmt, false ), $jsonld['datePublished'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_modified_gmt, false ), $jsonld['dateModified'] );

		// Check that we have exactly 2 images.
		$this->assertCount( 2, $jsonld['image'] );

		// Check the thumbnail.
		$this->assertCount( 4, $jsonld['image']['0'] );
		$this->assertEquals( 'ImageObject', $jsonld['image']['0']['@type'] );
		$this->assertEquals( $attachment_url, $jsonld['image']['0']['url'] );
		$this->assertEquals( 200, $jsonld['image']['0']['width'] );
		$this->assertEquals( 100, $jsonld['image']['0']['height'] );

		// Check the in content attachments.
		$this->assertCount( 4, $jsonld['image']['1'] );
		$this->assertEquals( 'ImageObject', $jsonld['image']['1']['@type'] );
		$this->assertEquals( $attachment2_url, $jsonld['image']['1']['url'] );
		$this->assertEquals( 300, $jsonld['image']['1']['width'] );
		$this->assertEquals( 200, $jsonld['image']['1']['height'] );

		//
		$this->assertCount( 2, $jsonld['mentions'] );
		$this->assertEquals( $entity_1_uri, $jsonld['mentions'][0]['@id'] );
		$this->assertEquals( $entity_2_uri, $jsonld['mentions'][1]['@id'] );
	}

	/**
	 * Test a Post with gallery of attached images and Entities.
	 *
	 * @since 3.10.0
	 */
	public function test_a_post_with_attached_gallery_images_and_entities() {
		// attache an image  attached to some other post
		$other_post      = $this->factory->post->create_and_get( array( 'post_author' => $this->author->ID ) );
		$attachment_id   = $this->make_dummy_attachment( 'otherimage.jpg', 300, 200, $other_post->ID );
		$attachment2_url = wp_get_attachment_url( $attachment_id );

		// Create a post that incluse an img of an attachment and an external URL
		$post      = $this->factory->post->create_and_get( array(
			'post_author'  => $this->author->ID,
			'post_content' => 'text <img src="' . $attachment2_url . '">' . "\n" .
			                  'more text <a href=""><img src="http://example.org">text</a>' .
			                  '[gallery] plain text',
		) );
		$post_uri  = $this->entity_service->get_uri( $post->ID );
		$permalink = get_permalink( $post->ID );

		// attache an image  attached to same post
		$other_post      = $this->factory->post->create_and_get( array( 'post_author' => $this->author->ID ) );
		$attachment3_id  = $this->make_dummy_attachment( 'gallery.jpg', 150, 150, $post->ID );
		$attachment3_url = wp_get_attachment_url( $attachment3_id );

		// Create a couple of entities.
		$entity_1 = $this->entity_factory->create_and_get();
		$this->entity_type_service->set( $entity_1->ID, 'http://schema.org/Organization' );
		$entity_1_uri = $this->entity_service->get_uri( $entity_1->ID );

		$entity_2 = $this->entity_factory->create_and_get();
		$this->entity_type_service->set( $entity_2->ID, 'http://schema.org/Person' );
		$entity_2_uri = $this->entity_service->get_uri( $entity_2->ID );

		// Bind the entities to the post.
		wl_core_add_relation_instances( $post->ID, WL_WHO_RELATION, array(
			$entity_1->ID,
			$entity_2->ID,
		) );

		//
		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references );

		// Check that we have 2 references.
		$this->assertCount( 2, $references );

		// Check that we have 11 properties, not one more than that.
		$this->assertCount( 11, $jsonld );

		// Check the json-ld values.
		$this->assertEquals( 'http://schema.org', $jsonld['@context'] );
		$this->assertEquals( $post_uri, $jsonld['@id'] );
		$this->assertEquals( 'Article', $jsonld['@type'] );
		$this->assertEquals( $post->post_excerpt, $jsonld['description'] );
		$this->assertEquals( $post->post_title, $jsonld['headline'] );
		// See https://github.com/insideout10/wordlift-plugin/issues/451
		$this->assertEquals( $permalink, $jsonld['mainEntityOfPage'] );
		$this->assertEquals( 'Person', $jsonld['author']['@type'] );
		$this->assertEquals( $this->author_uri, $jsonld['author']['@id'] );
		$this->assertEquals( $this->author->display_name, $jsonld['author']['name'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_date_gmt, false ), $jsonld['datePublished'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_modified_gmt, false ), $jsonld['dateModified'] );

		// Check that we have exactly 2 images.
		$this->assertCount( 2, $jsonld['image'] );

		// Check the in content image.
		$this->assertCount( 4, $jsonld['image']['0'] );
		$this->assertEquals( 'ImageObject', $jsonld['image']['0']['@type'] );
		$this->assertEquals( $attachment2_url, $jsonld['image']['0']['url'] );
		$this->assertEquals( 300, $jsonld['image']['0']['width'] );
		$this->assertEquals( 200, $jsonld['image']['0']['height'] );

		// Check the gallery image.
		$this->assertCount( 4, $jsonld['image']['1'] );
		$this->assertEquals( 'ImageObject', $jsonld['image']['1']['@type'] );
		$this->assertEquals( $attachment3_url, $jsonld['image']['1']['url'] );
		$this->assertEquals( 150, $jsonld['image']['1']['width'] );
		$this->assertEquals( 150, $jsonld['image']['1']['height'] );

		//
		$this->assertCount( 2, $jsonld['mentions'] );
		$this->assertEquals( $entity_1_uri, $jsonld['mentions'][0]['@id'] );
		$this->assertEquals( $entity_2_uri, $jsonld['mentions'][1]['@id'] );
	}

	/**
	 * Test a Post with explicit images in gallery and Entities.
	 * also checks image duplication avoidance
	 *
	 * @since 3.10.0
	 */
	public function test_a_post_with_gallery_images_and_entities() {
		// attache an image  attached to some other post
		$other_post      = $this->factory->post->create_and_get( array( 'post_author' => $this->author->ID ) );
		$attachment2_id  = $this->make_dummy_attachment( 'otherimage.jpg', 300, 200, $other_post->ID );
		$attachment2_url = wp_get_attachment_url( $attachment2_id );

		// attache an image  attached to some other post
		$attachment3_id  = $this->make_dummy_attachment( 'yetaotherimage.jpg', 200, 300, $other_post->ID );
		$attachment3_url = wp_get_attachment_url( $attachment3_id );

		// Create a post that incluse an img of an attachment and an external URL
		$post      = $this->factory->post->create_and_get( array(
			'post_author'  => $this->author->ID,
			'post_content' => 'text <img src="' . $attachment2_url . '">' . "\n" .
			                  'more text <a href=""><img src="http://example.org">text</a>' .
			                  '[gallery ids="' . $attachment3_id . ',' . $attachment2_id . ',8905"] plain text',
		) );
		$post_uri  = $this->entity_service->get_uri( $post->ID );
		$permalink = get_permalink( $post->ID );

		// attache an to same post, should not be in json-ld
		$other_post      = $this->factory->post->create_and_get( array( 'post_author' => $this->author->ID ) );
		$attachment4_id  = $this->make_dummy_attachment( 'gallery.jpg', 150, 150, $post->ID );
		$attachment4_url = wp_get_attachment_url( $attachment3_id );

		// Create a couple of entities.
		$entity_1 = $this->entity_factory->create_and_get();
		$this->entity_type_service->set( $entity_1->ID, 'http://schema.org/Organization' );
		$entity_1_uri = $this->entity_service->get_uri( $entity_1->ID );

		$entity_2 = $this->entity_factory->create_and_get();
		$this->entity_type_service->set( $entity_2->ID, 'http://schema.org/Person' );
		$entity_2_uri = $this->entity_service->get_uri( $entity_2->ID );

		// Bind the entities to the post.
		wl_core_add_relation_instances( $post->ID, WL_WHO_RELATION, array(
			$entity_1->ID,
			$entity_2->ID,
		) );

		//
		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references );

		// Check that we have 2 references.
		$this->assertCount( 2, $references );

		// Check that we have 11 properties, not one more than that.
		$this->assertCount( 11, $jsonld );

		// Check the json-ld values.
		$this->assertEquals( 'http://schema.org', $jsonld['@context'] );
		$this->assertEquals( $post_uri, $jsonld['@id'] );
		$this->assertEquals( 'Article', $jsonld['@type'] );
		$this->assertEquals( $post->post_excerpt, $jsonld['description'] );
		$this->assertEquals( $post->post_title, $jsonld['headline'] );
		// See https://github.com/insideout10/wordlift-plugin/issues/451
		$this->assertEquals( $permalink, $jsonld['mainEntityOfPage'] );
		$this->assertEquals( 'Person', $jsonld['author']['@type'] );
		$this->assertEquals( $this->author_uri, $jsonld['author']['@id'] );
		$this->assertEquals( $this->author->display_name, $jsonld['author']['name'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_date_gmt, false ), $jsonld['datePublished'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_modified_gmt, false ), $jsonld['dateModified'] );

		// Check that we have exactly 2 images.
		$this->assertCount( 2, $jsonld['image'] );

		// Check the in content image.
		$this->assertCount( 4, $jsonld['image']['0'] );
		$this->assertEquals( 'ImageObject', $jsonld['image']['0']['@type'] );
		$this->assertEquals( $attachment2_url, $jsonld['image']['0']['url'] );
		$this->assertEquals( 300, $jsonld['image']['0']['width'] );
		$this->assertEquals( 200, $jsonld['image']['0']['height'] );

		// Check the gallery image.
		$this->assertCount( 4, $jsonld['image']['1'] );
		$this->assertEquals( 'ImageObject', $jsonld['image']['1']['@type'] );
		$this->assertEquals( $attachment3_url, $jsonld['image']['1']['url'] );
		$this->assertEquals( 200, $jsonld['image']['1']['width'] );
		$this->assertEquals( 300, $jsonld['image']['1']['height'] );

		//
		$this->assertCount( 2, $jsonld['mentions'] );
		$this->assertEquals( $entity_1_uri, $jsonld['mentions'][0]['@id'] );
		$this->assertEquals( $entity_2_uri, $jsonld['mentions'][1]['@id'] );
	}

}
