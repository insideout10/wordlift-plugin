<?php
/**
 * Tests: Post to JSON-LD Converter.
 *
 * @since   3.10.0
 * @package Wordlift
 */

use Wordlift\Jsonld\Post_Reference;
use Wordlift\Jsonld\Term_Reference;
use Wordlift\Object_Type_Enum;
use Wordlift\Relation\Relations;

/**
 * Define the {@link Wordlift_Post_To_Jsonld_Converter_Test} class.
 *
 * List of properties for the post [
 * "@context","@id","@type",
 * "description","mainEntityOfPage","headline",
 * "url","datePublished","dateModified",
 * "wordCount","articleSection","commentCount",
 * "inLanguage","publisher","about",
 * "mentions","author"
 * ]
 *
 * @since   3.10.0
 * @package Wordlift
 * @group jsonld
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
		Wordlift_Unit_Test_Case::turn_off_entity_push();

		$this->post_to_jsonld_converter = new Wordlift_Post_To_Jsonld_Converter(
			Wordlift_Entity_Type_Service::get_instance(),
			Wordlift_User_Service::get_instance(),
			Wordlift_Attachment_Service::get_instance() );
		$this->entity_service           = Wordlift_Entity_Service::get_instance();
		$this->user_service             = Wordlift_User_Service::get_instance();

		// Check that we have services' instances.
		$this->assertNotNull( $this->post_to_jsonld_converter );
		$this->assertNotNull( Wordlift_Configuration_Service::get_instance() );
		$this->assertNotNull( $this->entity_service );
		$this->assertNotNull( $this->user_service );
		$this->assertNotNull( Wordlift_Entity_Type_Service::get_instance() );

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data' );

		// Create some more mock-up data.
		$this->author     = $this->factory()->user->create_and_get();
		$this->author_uri = $this->user_service->get_uri( $this->author->ID );

	}

	/**
	 * Test a Post without entities.
	 *
	 * @since 3.10.0
	 */
	public function test_a_post_without_entities() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data' );

		$post      = $this->factory()->post->create_and_get( array( 'post_author' => $this->author->ID ) );
		$post_uri  = $this->entity_service->get_uri( $post->ID );
		$permalink = get_permalink( $post->ID );

		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references, $references, new Relations() );

		// Check that we don't have reference.
		$this->assertCount( 0, $references );

		// Check that we have ... properties, not one more than that.
		// Since 3.16.0 we also have the publisher among the properties.
		$this->assertCount( 15, $jsonld );

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
		$this->assertEquals( self::word_count( $post->ID ), $jsonld['wordCount'] );

	}

	/**
	 * Test a Page without entities.
	 *
	 * @since 3.10.0
	 */
	public function test_a_page_without_entities() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$post      = $this->factory()->post->create_and_get( array(
			'post_type'    => 'page',
			'post_author'  => $this->author->ID,
		) );
		add_post_type_support( 'page', 'excerpt' );

		$post_uri  = $this->entity_service->get_uri( $post->ID );
		$permalink = get_permalink( $post->ID );

		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references, $references, new Relations() );


		// Check that we don't have reference.
		$this->assertCount( 0, $references );

		// Check that we have ... properties, not one more than that.
		// Since 3.16.0 we also have the publisher among the properties.
		$this->assertCount( 14, $jsonld );

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
		$this->assertEquals( self::word_count( $post->ID ), $jsonld['wordCount'] );

	}

	/**
	 * Test a Post with a Person as Publisher.
	 *
	 * @since 3.10.0
	 */
	public function test_a_post_with_a_person_publisher() {

		$publisher = $this->entity_factory->create_and_get( array(
			'post_title' => 'Test Post To Json-Ld Converter test_a_post_with_a_person_publisher',
		) );
		Wordlift_Entity_Type_Service::get_instance()->set( $publisher->ID, 'http://schema.org/Person' );

		$type = Wordlift_Entity_Type_Service::get_instance()->get( $publisher->ID );

		$this->assertTrue( is_array( $type ) );
		$this->assertTrue( 0 < sizeof( $type ) );

		$publisher_uri = $this->entity_service->get_uri( $publisher->ID );

		Wordlift_Configuration_Service::get_instance()->set_publisher_id( $publisher->ID );
		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$this->assertEquals( $publisher->ID, Wordlift_Configuration_Service::get_instance()->get_publisher_id() );


		$post      = $this->factory()->post->create_and_get( array(
			'post_title'  => 'test_a_post_with_a_person_publisher',
			'post_author' => $this->author->ID
		) );
		$post_uri  = $this->entity_service->get_uri( $post->ID );
		$permalink = get_permalink( $post->ID );

		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references, $references, new Relations() );

		// Check that we don't have reference.
		$this->assertCount( 0, $references );

		// Check that we have ... properties, not one more than that.
		$this->assertCount( 15, $jsonld );

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
		$this->assertEquals( self::word_count( $post->ID ), $jsonld['wordCount'] );

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


		$publisher = $this->entity_factory->create_and_get( array(
			'post_title' => 'Test Post To Json-Ld Converter test_a_post_with_an_organization_publisher_without_logo',
		) );
		Wordlift_Entity_Type_Service::get_instance()->set( $publisher->ID, 'http://schema.org/Organization' );
		$publisher_uri = $this->entity_service->get_uri( $publisher->ID );

		Wordlift_Configuration_Service::get_instance()->set_publisher_id( $publisher->ID );
		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$post      = $this->factory()->post->create_and_get( array( 'post_author' => $this->author->ID ) );
		$post_uri  = $this->entity_service->get_uri( $post->ID );
		$permalink = get_permalink( $post->ID );

		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references, $references, new Relations() );

		// Check that we don't have reference.
		$this->assertCount( 0, $references );

		// Check that we have ... properties, not one more than that.
		$this->assertCount( 15, $jsonld );

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
		$this->assertEquals( self::word_count( $post->ID ), $jsonld['wordCount'] );

		// Check the publisher.
		$this->assertCount( 3, $jsonld['publisher'] );
		$this->assertEquals( 'Organization', $jsonld['publisher']['@type'] );
		$this->assertEquals( $publisher_uri, $jsonld['publisher']['@id'] );
		$this->assertEquals( $publisher->post_title, $jsonld['publisher']['name'] );

	}

	/**
	 * Helper function to create attachment DB without uploading FilesystemIterator.
	 *
	 * @param string $filename The filename the attachement should have
	 * @param integer $width The width of the image
	 * @param integer $height The height of the image
	 * @param integer $post_id The ID of the post to associated with the attachment
	 *
	 * @return    integer        The ID of the attachment created
	 **@since 3.10
	 *
	 */
	private function make_dummy_attachment( $filename, $width, $height, $post_id ) {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		// Get the mock file.
		$real_filename = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'image.png';

		// Copy the mock file to the uploads folder.
		$upload_dir = wp_upload_dir();
		copy( $real_filename, $upload_dir['basedir'] . DIRECTORY_SEPARATOR . $filename );

		// Create an attachment.
		$attachment_id = $this->factory()->attachment->create_object( $filename, $post_id, array(
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

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		// Create a publisher.
		$publisher = $this->entity_factory->create_and_get( array(
			'post_title' => 'Test Post To Json-Ld Converter test_a_post_with_an_organization_publisher_with_logo',
		) );
		Wordlift_Entity_Type_Service::get_instance()->set( $publisher->ID, 'http://schema.org/Organization' );
		$publisher_uri = $this->entity_service->get_uri( $publisher->ID );

		// Set the publisher.
		Wordlift_Configuration_Service::get_instance()->set_publisher_id( $publisher->ID );

		// Set the logo for the publisher.
		$attachment_id = $this->make_dummy_attachment( 'image.jpg', 200, 100, $publisher->ID );
		set_post_thumbnail( $publisher->ID, $attachment_id );
		$attachment_url = wp_get_attachment_url( $attachment_id );

		// Create a random post.
		$post      = $this->factory()->post->create_and_get( array( 'post_author' => $this->author->ID ) );
		$post_uri  = $this->entity_service->get_uri( $post->ID );
		$permalink = get_permalink( $post->ID );

		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references, $references, new Relations() );

		// Check that we don't have reference.
		$this->assertCount( 0, $references );

		// Check that we have ... properties, not one more than that.
		$this->assertCount( 15, $jsonld );

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
		$this->assertEquals( self::word_count( $post->ID ), $jsonld['wordCount'] );

		// Check the publisher.
		$publisher_count = ( extension_loaded( 'imagick' ) && class_exists( "Imagick" ) && version_compare( PHP_VERSION, '8.0.0', '<' ) ) ? 4 : 3;
		$this->assertCount( $publisher_count, $jsonld['publisher'] );
		$this->assertEquals( 'Organization', $jsonld['publisher']['@type'] );
		$this->assertEquals( $publisher_uri, $jsonld['publisher']['@id'] );
		$this->assertEquals( $publisher->post_title, $jsonld['publisher']['name'] );

		// Check the logo.
		$expected_attachment_url = substr( $attachment_url, 0, strrpos( $attachment_url, '.' ) )
		                           . '--publisher-logo'
		                           . substr( $attachment_url, strrpos( $attachment_url, '.' ) );
		if ( extension_loaded( 'imagick' ) && class_exists( "Imagick" ) && version_compare( PHP_VERSION, '8.0.0', '<' ) ) {
			$this->assertCount( 4, $jsonld['publisher']['logo'] );
			$this->assertEquals( 'ImageObject', $jsonld['publisher']['logo']['@type'] );
			$this->assertEquals( $expected_attachment_url, $jsonld['publisher']['logo']['url'] );
			$this->assertEquals( 86, $jsonld['publisher']['logo']['width'], "Width doesn't match for $attachment_url." );
			$this->assertEquals( 60, $jsonld['publisher']['logo']['height'] );
		}

	}

	/**
	 * Test a Post with Entities.
	 *
	 * @since 3.10.0
	 */
	public function test_a_post_with_entities() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		// Create a post.
		$post      = $this->factory()->post->create_and_get( array( 'post_author' => $this->author->ID ) );
		$post_uri  = $this->entity_service->get_uri( $post->ID );
		$permalink = get_permalink( $post->ID );

		// Create a couple of entities.
		$entity_1 = $this->entity_factory->create_and_get( array(
			'post_title' => 'Test Post To Json-Ld Converter test_a_post_with_entities 1',
		) );
		Wordlift_Entity_Type_Service::get_instance()->set( $entity_1->ID, 'http://schema.org/Organization' );
		$entity_1_uri = $this->entity_service->get_uri( $entity_1->ID );

		$entity_2 = $this->entity_factory->create_and_get( array(
			'post_title' => 'Test Post To Json-Ld Converter test_a_post_with_entities 2',
		) );
		Wordlift_Entity_Type_Service::get_instance()->set( $entity_2->ID, 'http://schema.org/Person' );
		$entity_2_uri = $this->entity_service->get_uri( $entity_2->ID );

		// Bind the entities to the post.
		wl_core_add_relation_instances( $post->ID, WL_WHO_RELATION, array(
			$entity_1->ID,
			$entity_2->ID,
		) );

		//
		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references, $references, new Relations() );

		// Check that we have 2 references.
		$this->assertCount( 2, $references );

		// Check that we have ... properties, not one more than that.
		//
		// Since 3.16.0 we also have the publisher among the properties.
		$this->assertCount( 15, $jsonld );

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
		$this->assertEquals( self::word_count( $post->ID ), $jsonld['wordCount'] );

		// disabled since mentions is moved to class-wordlift-jsonld-service.php since 3.43.0
//		$this->assertCount( 2, $jsonld['mentions'] );
//		$this->assertEquals( $entity_1_uri, $jsonld['mentions'][0]['@id'] );
//		$this->assertEquals( $entity_2_uri, $jsonld['mentions'][1]['@id'] );

	}



	/**
	 * Test a post with a `mentions` and an `about`.
	 *
	 * @since 3.12.0
	 */
	public function test_a_post_with_one_mentions_and_one_about_with_synonym() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		// Create a post.
		$post      = $this->factory()->post->create_and_get( array(
			'post_title'  => 'Lorem Ipsum',
			'post_author' => $this->author->ID,
		) );
		$post_uri  = $this->entity_service->get_uri( $post->ID );
		$permalink = get_permalink( $post->ID );

		// Create a couple of entities.
		$entity_1 = $this->entity_factory->create_and_get( array(
			'post_title' => 'Test Post To Json-Ld Converter test_a_post_with_one_mentions_and_one_about_with_synonym 2',
		) );
		Wordlift_Entity_Type_Service::get_instance()->set( $entity_1->ID, 'http://schema.org/Organization' );
		$entity_1_uri = $this->entity_service->get_uri( $entity_1->ID );

		$entity_2 = $this->entity_factory->create_and_get( array(
			'post_title' => 'Test Post To Json-Ld Converter test_a_post_with_one_mentions_and_one_about_with_synonym 3',
		) );
		Wordlift_Entity_Type_Service::get_instance()->set( $entity_2->ID, 'http://schema.org/Person' );
		$entity_2_uri = $this->entity_service->get_uri( $entity_2->ID );
		$this->entity_service->set_alternative_labels( $entity_2->ID, array( 'Lorem' ) );

		// Bind the entities to the post.
		wl_core_add_relation_instances( $post->ID, WL_WHO_RELATION, array(
			$entity_1->ID,
			$entity_2->ID,
		) );

		//
		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references, $references, new Relations() );

		// Check that we have 2 references.
		$this->assertCount( 2, $references );

		// Check that we have ... properties, not one more than that.
		//
		// Since 3.16.0 we also have the publisher among the properties.
		// since 3.43.0, mentions and about is moved to class-wordlift-jsonld-service.php
		$this->assertCount( 15, $jsonld );

		// Check the json-ld values.
		$this->assertEquals( 'http://schema.org', $jsonld['@context'] );
		$this->assertEquals( $post_uri, $jsonld['@id'] );
		$this->assertEquals( 'Article', $jsonld['@type'] );
		$this->assertEquals( $post->post_excerpt, $jsonld['description'] );
		$this->assertEquals( $post->post_title, $jsonld['headline'] );
		$this->assertEquals( $permalink, $jsonld['mainEntityOfPage'] );
		$this->assertEquals( 'Person', $jsonld['author']['@type'] );
		$this->assertEquals( $this->author_uri, $jsonld['author']['@id'] );
		$this->assertEquals( $this->author->display_name, $jsonld['author']['name'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_date_gmt, false ), $jsonld['datePublished'] );
		$this->assertEquals( mysql2date( 'Y-m-d\TH:i', $post->post_modified_gmt, false ), $jsonld['dateModified'] );
		$this->assertEquals( self::word_count( $post->ID ), $jsonld['wordCount'] );

		// disabled since the mentions and about is moved to class-wordlift-jsonld-service.php in 3.43.0
//		$this->assertCount( 1, $jsonld['mentions'] );
//		$this->assertEquals( $entity_1_uri, $jsonld['mentions'][0]['@id'] );
//
//		$this->assertCount( 1, $jsonld['about'] );
//		$this->assertEquals( $entity_2_uri, $jsonld['about'][0]['@id'] );

	}

	/**
	 * Test the about match in post title with matched label should returns true.
	 *
	 * @see https://github.com/insideout10/wordlift-plugin/issues/1613
	 */
	public function test_the_about_match_in_post_title_with_matched_label_should_returns_true() {
		$title  = 'Example Post Title';
		$labels = array( 'o', 'Post' );

		$check_matches = Wordlift_Jsonld_Service::get_instance()->check_title_match( $labels, $title );

		$this->assertTrue( $check_matches );
	}

	/**
	 * Test the about match in post title without matched label should returns false.
	 *
	 * @see https://github.com/insideout10/wordlift-plugin/issues/1613
	 */
	public function test_the_about_match_in_post_title_without_matched_label_should_returns_false() {
		$title  = 'Example Post Title';
		$labels = array( 'o', 'WordLift' );

		$check_matches = Wordlift_Jsonld_Service::get_instance()->check_title_match( $labels, $title );

		$this->assertFalse( $check_matches );
	}

	/**
	 * Test a Post with featured image and Entities.
	 *
	 * @since 3.10.0
	 */
	public function test_a_post_with_featured_image_and_entities() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		// Create a post.
		$post      = $this->factory()->post->create_and_get( array(
			'post_author' => $this->author->ID,
			'post_title'  => 'Test Post To Json-Ld Converter test_a_post_with_featured_image_and_entities 1',
		) );
		$post_uri  = $this->entity_service->get_uri( $post->ID );
		$permalink = get_permalink( $post->ID );

		// attache a thumbnail to the Post
		$attachment_id = $this->make_dummy_attachment( 'image.jpg', 200, 100, $post->ID );
		set_post_thumbnail( $post->ID, $attachment_id );
		$attachment_url = wp_get_attachment_url( $attachment_id );

		// Create a couple of entities.
		$entity_1 = $this->entity_factory->create_and_get( array(
			'post_title' => 'Test Post To Json-Ld Converter test_a_post_with_featured_image_and_entities 2',
		) );
		Wordlift_Entity_Type_Service::get_instance()->set( $entity_1->ID, 'http://schema.org/Organization' );
		$entity_1_uri = $this->entity_service->get_uri( $entity_1->ID );

		$entity_2 = $this->entity_factory->create_and_get( array(
			'post_title' => 'Test Post To Json-Ld Converter test_a_post_with_featured_image_and_entities 3',
		) );
		Wordlift_Entity_Type_Service::get_instance()->set( $entity_2->ID, 'http://schema.org/Person' );
		$entity_2_uri = $this->entity_service->get_uri( $entity_2->ID );

		// Bind the entities to the post.
		wl_core_add_relation_instances( $post->ID, WL_WHO_RELATION, array(
			$entity_1->ID,
			$entity_2->ID,
		) );

		//
		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references, $references, new Relations() );

		// Check that we have 2 references.
		$this->assertCount( 2, $references );

		// Check that we have ... properties, not one more than that.
		//
		// Since 3.16.0 we also have the publisher among the properties.
		// since 3.43.0, mentions and about is moved to class-wordlift-jsonld-service.php
		$this->assertCount( 16, $jsonld );

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
		$this->assertEquals( self::word_count( $post->ID ), $jsonld['wordCount'] );

		// Check that we have exactly one images.
		$this->assertCount( 1, $jsonld['image'] );

		// Check the thumbnail.
		$this->assertCount( 4, $jsonld['image']['0'] );
		$this->assertEquals( 'ImageObject', $jsonld['image']['0']['@type'] );
		$this->assertEquals( $attachment_url, $jsonld['image']['0']['url'] );
		$this->assertEquals( 200, $jsonld['image']['0']['width'] );
		$this->assertEquals( 100, $jsonld['image']['0']['height'] );

		// disabled since mentions is moved to class-wordlift-jsonld-service.php since 3.43.0
//		$this->assertCount( 2, $jsonld['mentions'] );
//		$this->assertEquals( $entity_1_uri, $jsonld['mentions'][0]['@id'] );
//		$this->assertEquals( $entity_2_uri, $jsonld['mentions'][1]['@id'] );

	}

	/**
	 * Test a Post with featured image, unincluded associated images and Entities.
	 *
	 * @since 3.10.0
	 */
	public function test_a_post_with_attached_images_and_entities() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		// Create a post.
		$post      = $this->factory()->post->create_and_get( array(
			'post_author' => $this->author->ID,
			'post_title'  => 'Test Post To Json-Ld Converter test_a_post_with_attached_images_and_entities 1',
		) );
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
		$entity_1 = $this->entity_factory->create_and_get( array(
			'post_title' => 'Test Post To Json-Ld Converter test_a_post_with_attached_images_and_entities 2',
		) );
		Wordlift_Entity_Type_Service::get_instance()->set( $entity_1->ID, 'http://schema.org/Organization' );
		$entity_1_uri = $this->entity_service->get_uri( $entity_1->ID );

		$entity_2 = $this->entity_factory->create_and_get( array(
			'post_title' => 'Test Post To Json-Ld Converter test_a_post_with_attached_images_and_entities 3',
		) );
		Wordlift_Entity_Type_Service::get_instance()->set( $entity_2->ID, 'http://schema.org/Person' );
		$entity_2_uri = $this->entity_service->get_uri( $entity_2->ID );

		// Bind the entities to the post.
		wl_core_add_relation_instances( $post->ID, WL_WHO_RELATION, array(
			$entity_1->ID,
			$entity_2->ID,
		) );

		//
		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references, $references, new Relations() );

		// Check that we have 2 references.
		$this->assertCount( 2, $references );

		// Check that we have ... properties, not one more than that.
		//
		// Since 3.16.0 we also have the publisher among the properties.
		// since 3.43.0, mentions and about is moved to class-wordlift-jsonld-service.php
		$this->assertCount( 16, $jsonld );

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
		$this->assertEquals( self::word_count( $post->ID ), $jsonld['wordCount'] );

		// Check that we have exactly one images.
		$this->assertCount( 1, $jsonld['image'] );

		// Check the thumbnail.
		$this->assertCount( 4, $jsonld['image']['0'] );
		$this->assertEquals( 'ImageObject', $jsonld['image']['0']['@type'] );
		$this->assertEquals( $attachment_url, $jsonld['image']['0']['url'] );
		$this->assertEquals( 200, $jsonld['image']['0']['width'] );
		$this->assertEquals( 100, $jsonld['image']['0']['height'] );

		// disabled since mentions is moved to class-wordlift-jsonld-service.php since 3.43.0
//		$this->assertCount( 2, $jsonld['mentions'] );
//		$this->assertEquals( $entity_1_uri, $jsonld['mentions'][0]['@id'] );
//		$this->assertEquals( $entity_2_uri, $jsonld['mentions'][1]['@id'] );
	}

	/**
	 * Test a Post with featured image, embedded images and Entities.
	 *
	 * @since 3.10.0
	 */
	public function test_a_post_with_embedded_images_and_entities() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		// Attach an image attached to some other post.
		$other_post      = $this->factory()->post->create_and_get( array(
			'post_author' => $this->author->ID,
			'post_title'  => 'Test Post To Json-Ld Converter test_a_post_with_embedded_images_and_entities 1',
		) );
		$attachment_id   = $this->make_dummy_attachment( 'otherimage.jpg', 300, 200, $other_post->ID );
		$attachment2_url = wp_get_attachment_url( $attachment_id );

		// Create a post that include an img of an attachment and an external URL
		$post      = $this->factory()->post->create_and_get( array(
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
		$entity_1 = $this->entity_factory->create_and_get( array(
			'post_title' => 'Test Post To Json-Ld Converter test_a_post_with_embedded_images_and_entities 2',
		) );
		Wordlift_Entity_Type_Service::get_instance()->set( $entity_1->ID, 'http://schema.org/Organization' );
		$entity_1_uri = $this->entity_service->get_uri( $entity_1->ID );

		$entity_2 = $this->entity_factory->create_and_get( array(
			'post_title' => 'Test Post To Json-Ld Converter test_a_post_with_embedded_images_and_entities 3',
		) );
		Wordlift_Entity_Type_Service::get_instance()->set( $entity_2->ID, 'http://schema.org/Person' );
		$entity_2_uri = $this->entity_service->get_uri( $entity_2->ID );

		// Bind the entities to the post.
		wl_core_add_relation_instances( $post->ID, WL_WHO_RELATION, array(
			$entity_1->ID,
			$entity_2->ID,
		) );

		//
		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references, $references, new Relations() );

		// Check that we have 2 references.
		$this->assertCount( 2, $references );


		// Check that we have ... properties, not one more than that.
		//
		// Since 3.16.0 we also have the publisher among the properties.
		// since 3.43.0, mentions and about is moved to class-wordlift-jsonld-service.php
		$this->assertCount( 16, $jsonld );

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
		$this->assertEquals( self::word_count( $post->ID ), $jsonld['wordCount'] );

		// The following assertion is now disabled due to:
		// https://github.com/insideout10/wordlift-plugin/issues/689.
		//
		// Check that we have exactly 2 images.
		// $this->assertCount( 2, $jsonld['image'] );

		// Check the thumbnail.
		$this->assertCount( 4, $jsonld['image']['0'] );
		$this->assertEquals( 'ImageObject', $jsonld['image']['0']['@type'] );
		$this->assertEquals( $attachment_url, $jsonld['image']['0']['url'] );
		$this->assertEquals( 200, $jsonld['image']['0']['width'] );
		$this->assertEquals( 100, $jsonld['image']['0']['height'] );

		// Check the in content attachments.
		// The following assertions are disabled because of:
		// https://github.com/insideout10/wordlift-plugin/issues/689.
		// $this->assertCount( 4, $jsonld['image']['1'] );
		// $this->assertEquals( 'ImageObject', $jsonld['image']['1']['@type'] );
		// $this->assertEquals( $attachment2_url, $jsonld['image']['1']['url'] );
		// $this->assertEquals( 300, $jsonld['image']['1']['width'] );
		// $this->assertEquals( 200, $jsonld['image']['1']['height'] );

		// disabled since the mentions is moved to class-wordlift-jsonld-service.php in 3.43.0
//		$this->assertCount( 2, $jsonld['mentions'] );
//		$this->assertEquals( $entity_1_uri, $jsonld['mentions'][0]['@id'] );
//		$this->assertEquals( $entity_2_uri, $jsonld['mentions'][1]['@id'] );
	}

	/**
	 * Test a Post with gallery of attached images and Entities.
	 *
	 * @since 3.10.0
	 */
	public function test_a_post_with_attached_gallery_images_and_entities() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		// attache an image  attached to some other post
		$other_post      = $this->factory()->post->create_and_get( array(
			'post_author' => $this->author->ID,
			'post_title'  => 'Test Post To Json-Ld Converter test_a_post_with_attached_gallery_images_and_entities 1',
		) );
		$attachment_id   = $this->make_dummy_attachment( 'otherimage.jpg', 300, 200, $other_post->ID );
		$attachment2_url = wp_get_attachment_url( $attachment_id );

		// Create a post that incluse an img of an attachment and an external URL
		$post      = $this->factory()->post->create_and_get( array(
			'post_author'  => $this->author->ID,
			'post_content' => 'text <img src="' . $attachment2_url . '">' . "\n" .
			                  'more text <a href=""><img src="http://example.org">text</a>' .
			                  '[gallery] plain text',
		) );
		$post_uri  = $this->entity_service->get_uri( $post->ID );
		$permalink = get_permalink( $post->ID );

		// attache an image  attached to same post
		$other_post      = $this->factory()->post->create_and_get( array( 'post_author' => $this->author->ID ) );
		$attachment3_id  = $this->make_dummy_attachment( 'gallery.jpg', 150, 150, $post->ID );
		$attachment3_url = wp_get_attachment_url( $attachment3_id );

		// Create a couple of entities.
		$entity_1 = $this->entity_factory->create_and_get( array(
			'post_title' => 'Test Post To Json-Ld Converter test_a_post_with_attached_gallery_images_and_entities 2',
		) );
		Wordlift_Entity_Type_Service::get_instance()->set( $entity_1->ID, 'http://schema.org/Organization' );
		$entity_1_uri = $this->entity_service->get_uri( $entity_1->ID );

		$entity_2 = $this->entity_factory->create_and_get( array(
			'post_title' => 'Test Post To Json-Ld Converter test_a_post_with_attached_gallery_images_and_entities 3',
		) );
		Wordlift_Entity_Type_Service::get_instance()->set( $entity_2->ID, 'http://schema.org/Person' );
		$entity_2_uri = $this->entity_service->get_uri( $entity_2->ID );

		// Bind the entities to the post.
		wl_core_add_relation_instances( $post->ID, WL_WHO_RELATION, array(
			$entity_1->ID,
			$entity_2->ID,
		) );

		//
		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references, $references, new Relations() );

		// Check that we have 2 references.
		$this->assertCount( 2, $references );

		// Check that we have ... properties, not one more than that.
		//
		// Since 3.16.0 we also have the publisher among the properties.
		// since 3.43.0, mentions and about is moved to class-wordlift-jsonld-service.php
		$this->assertCount( 16, $jsonld );

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
		// The following assertions are disabled because of:
		// https://github.com/insideout10/wordlift-plugin/issues/689.
		// $this->assertCount( 2, $jsonld['image'] );

		// Check the in content image.
//		$this->assertCount( 4, $jsonld['image']['0'] );
//		$this->assertEquals( 'ImageObject', $jsonld['image']['0']['@type'] );
//		$this->assertEquals( $attachment2_url, $jsonld['image']['0']['url'] );
//		$this->assertEquals( 300, $jsonld['image']['0']['width'] );
//		$this->assertEquals( 200, $jsonld['image']['0']['height'] );

		// Check the gallery image.
//		$this->assertCount( 4, $jsonld['image']['1'] );
//		$this->assertEquals( 'ImageObject', $jsonld['image']['1']['@type'] );
//		$this->assertEquals( $attachment3_url, $jsonld['image']['1']['url'] );
//		$this->assertEquals( 150, $jsonld['image']['1']['width'] );
//		$this->assertEquals( 150, $jsonld['image']['1']['height'] );

		// disabled since mentions is moved to class-wordlift-jsonld-service.php since 3.43.0
//		$this->assertCount( 2, $jsonld['mentions'] );
//		$this->assertEquals( $entity_1_uri, $jsonld['mentions'][0]['@id'] );
//		$this->assertEquals( $entity_2_uri, $jsonld['mentions'][1]['@id'] );
	}

	/**
	 * Test a Post with explicit images in gallery and Entities.
	 * also checks image duplication avoidance
	 *
	 * @since 3.10.0
	 */
	public function test_a_post_with_gallery_images_and_entities() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		// attache an image  attached to some other post
		$other_post      = $this->factory()->post->create_and_get( array(
			'post_author' => $this->author->ID,
			'post_title'  => 'Test Post To Json-Ld Converter test_a_post_with_gallery_images_and_entities 1',
		) );
		$attachment2_id  = $this->make_dummy_attachment( 'otherimage.jpg', 300, 200, $other_post->ID );
		$attachment2_url = wp_get_attachment_url( $attachment2_id );

		// attache an image  attached to some other post
		$attachment3_id  = $this->make_dummy_attachment( 'yetaotherimage.jpg', 200, 300, $other_post->ID );
		$attachment3_url = wp_get_attachment_url( $attachment3_id );

		// Create a post that incluse an img of an attachment and an external URL
		$post      = $this->factory()->post->create_and_get( array(
			'post_author'  => $this->author->ID,
			'post_content' => 'text <img src="' . $attachment2_url . '">' . "\n" .
			                  'more text <a href=""><img src="http://example.org">text</a>' .
			                  '[gallery ids="' . $attachment3_id . ',' . $attachment2_id . ',8905"] plain text',
		) );
		$post_uri  = $this->entity_service->get_uri( $post->ID );
		$permalink = get_permalink( $post->ID );

		// attache an to same post, should not be in json-ld
		$other_post      = $this->factory()->post->create_and_get( array( 'post_author' => $this->author->ID ) );
		$attachment4_id  = $this->make_dummy_attachment( 'gallery.jpg', 150, 150, $post->ID );
		$attachment4_url = wp_get_attachment_url( $attachment3_id );

		// Create a couple of entities.
		$entity_1 = $this->entity_factory->create_and_get( array(
			'post_title' => 'Test Post To Json-Ld Converter test_a_post_with_gallery_images_and_entities 2',
		) );
		Wordlift_Entity_Type_Service::get_instance()->set( $entity_1->ID, 'http://schema.org/Organization' );
		$entity_1_uri = $this->entity_service->get_uri( $entity_1->ID );

		$entity_2 = $this->entity_factory->create_and_get( array(
			'post_title' => 'Test Post To Json-Ld Converter test_a_post_with_gallery_images_and_entities 3',
		) );
		Wordlift_Entity_Type_Service::get_instance()->set( $entity_2->ID, 'http://schema.org/Person' );
		$entity_2_uri = $this->entity_service->get_uri( $entity_2->ID );

		// Bind the entities to the post.
		wl_core_add_relation_instances( $post->ID, WL_WHO_RELATION, array(
			$entity_1->ID,
			$entity_2->ID,
		) );

		//
		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references, $references, new Relations() );

		// Check that we have 2 references.
		$this->assertCount( 2, $references );

		// Check that we have ... properties, not one more than that.
		//
		// Since 3.16.0 we also have the publisher among the properties.
		// since 3.43.0, mentions and about is moved to class-wordlift-jsonld-service.php
		$this->assertCount( 16, $jsonld );

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
		$this->assertEquals( self::word_count( $post->ID ), $jsonld['wordCount'] );

		// Check that we have exactly 2 images.
		$this->assertCount( 2, $jsonld['image'] );

		// Check the in content image.
		$this->assertCount( 4, $jsonld['image']['0'] );
		$this->assertEquals( 'ImageObject', $jsonld['image']['0']['@type'] );
		// The following assertions are disabled because of:
		// https://github.com/insideout10/wordlift-plugin/issues/689.
//		$this->assertEquals( $attachment2_url, $jsonld['image']['0']['url'] );
//		$this->assertEquals( 300, $jsonld['image']['0']['width'] );
//		$this->assertEquals( 200, $jsonld['image']['0']['height'] );

		// Check the gallery image.
		$this->assertCount( 4, $jsonld['image']['0'] );
		$this->assertEquals( 'ImageObject', $jsonld['image']['0']['@type'] );
		$this->assertEquals( $attachment3_url, $jsonld['image']['0']['url'] );
		$this->assertEquals( 200, $jsonld['image']['0']['width'] );
		$this->assertEquals( 300, $jsonld['image']['0']['height'] );

		// disabled since mentions is moved to class-wordlift-jsonld-service.php since 3.43.0
//		$this->assertCount( 2, $jsonld['mentions'] );
//		$this->assertEquals( $entity_1_uri, $jsonld['mentions'][0]['@id'] );
//		$this->assertEquals( $entity_2_uri, $jsonld['mentions'][1]['@id'] );

	}

	/**
	 * Test a Post made by an author without a representing entity.
	 *
	 * @since 3.14.0
	 */
	public function test_a_post_with_a_user_without_a_representing_entity() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$author_id  = $this->factory()->user->create( array(
			'display_name' => 'John Smith',
		) );
		$author_uri = $this->user_service->get_uri( $author_id );

		// Create a post that includes an img of an attachment and an external URL.
		$post = $this->factory()->post->create_and_get( array(
			'post_author' => $author_id,
		) );

		//
		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references, $references, new Relations() );

		$this->assertEquals( 'Person', $jsonld['author']['@type'] );
		$this->assertEquals( $author_uri, $jsonld['author']['@id'] );
		$this->assertEquals( 'John Smith', $jsonld['author']['name'] );

	}

	/**
	 * Test a Post made by an author with a representing entity.
	 *
	 * @since 3.14.0
	 */
	public function test_a_post_with_a_user_with_a_representing_person_entity() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$author_id  = $this->factory()->user->create( array(
			'display_name' => 'John Smith',
		) );
		$author_uri = $this->user_service->get_uri( $author_id );

		$entity_id  = $this->entity_factory->create( array(
			'post_title'   => 'John Smith Entity',
			'post_excerpt' => 'Test Post To Json-Ld Converter test_a_post_with_a_user_with_a_representing_person_entity 1',
		) );
		$entity_uri = $this->entity_service->get_uri( $entity_id );
		$entity_url = get_permalink( $entity_id );
		Wordlift_Entity_Type_Service::get_instance()->set( $entity_id, 'http://schema.org/Person' );

		$this->assertGreaterThan( 0, $this->user_service->set_entity( $author_id, $entity_id ) );

		// Create a post that includes an img of an attachment and an external URL.
		$post = $this->factory()->post->create_and_get( array(
			'post_author'  => $author_id,
			'post_excerpt' => 'Test Post To Json-Ld Converter test_a_post_with_a_user_with_a_representing_person_entity 2',
		) );

		//
		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references, $references, new Relations() );

		$this->assertEquals( $entity_uri, $jsonld['author']['@id'] );

		$this->assertArraySubset( array( $entity_id ), $references );

		// Since 3.16.0 the author is printed on its own.
		//		$this->assertEquals( 'Person', $jsonld['author']['@type'] );
		//		$this->assertEquals( 'Lorem Ipsum', $jsonld['author']['description'] );
		//		$this->assertEquals( 'John Smith Entity', $jsonld['author']['name'] );
		//		$this->assertEquals( $entity_url, $jsonld['author']['mainEntityOfPage'] );
		//		$this->assertEquals( $entity_url, $jsonld['author']['url'] );

	}

	/**
	 * Test a Post made by an author with a representing entity.
	 *
	 * @since 3.14.0
	 */
	public function test_a_post_with_a_user_with_a_representing_organization_entity() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$author_id  = $this->factory()->user->create( array(
			'display_name' => 'John Smith',
		) );
		$author_uri = $this->user_service->get_uri( $author_id );

		$entity_id  = $this->entity_factory->create( array(
			'post_title'   => 'John Smith Entity',
			'post_excerpt' => 'Lorem Ipsum',
		) );
		$entity_uri = $this->entity_service->get_uri( $entity_id );
		$entity_url = get_permalink( $entity_id );
		Wordlift_Entity_Type_Service::get_instance()->set( $entity_id, 'http://schema.org/Organization' );

		$this->assertGreaterThan( 0, $this->user_service->set_entity( $author_id, $entity_id ) );

		// Create a post that includes an img of an attachment and an external URL.
		$post = $this->factory()->post->create_and_get( array(
			'post_author' => $author_id,
		) );

		//
		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references, $references, new Relations() );

		$this->assertEquals( $entity_uri, $jsonld['author']['@id'] );

		$this->assertArraySubset( array( $entity_id ), $references );

		// Since 3.16.0 the author is printed on its own.
		//		$this->assertEquals( 'Organization', $jsonld['author']['@type'] );
		//		$this->assertEquals( 'Lorem Ipsum', $jsonld['author']['description'] );
		//		$this->assertEquals( 'John Smith Entity', $jsonld['author']['name'] );
		//		$this->assertEquals( $entity_url, $jsonld['author']['mainEntityOfPage'] );
		//		$this->assertEquals( $entity_url, $jsonld['author']['url'] );

	}

	/**
	 * Test that when the author is changed in user settings
	 * the author triple is also changed in jsonld.
	 *
	 * @since 3.18.0
	 */
	public function test_a_post_with_a_user_when_the_author_is_updated() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$author_id = $this->factory()->user->create(
			array(
				'display_name' => 'John Smith',
			)
		);

		$entity_id = $this->entity_factory->create(
			array(
				'post_title'   => 'John Smith Entity',
				'post_excerpt' => 'Lorem Ipsum',
			)
		);

		$entity_id_2 = $this->entity_factory->create(
			array(
				'post_title'   => 'John Smith Entity 2',
				'post_excerpt' => 'Lorem Ipsum 2',
			)
		);

		$author_uri   = $this->user_service->get_uri( $author_id );
		$entity_uri   = $this->entity_service->get_uri( $entity_id );
		$entity_uri_2 = $this->entity_service->get_uri( $entity_id_2 );
		Wordlift_Entity_Type_Service::get_instance()->set( $entity_id, 'http://schema.org/Person' );

		$this->assertGreaterThan( 0, $this->user_service->set_entity( $author_id, $entity_id ) );

		// Create a post that includes an img of an attachment and an external URL.
		$post = $this->factory()->post->create_and_get(
			array(
				'post_author' => $author_id,
			)
		);

		$references = array();
		$jsonld     = $this->post_to_jsonld_converter->convert( $post->ID, $references, $references, new Relations() );

		$this->assertEquals( $entity_uri, $jsonld['author']['@id'] );
		$this->assertArraySubset( array( $entity_id ), $references );

		// Change the user author and test again that is has changed in jsonld.
		$this->assertGreaterThan( 0, $this->user_service->set_entity( $author_id, $entity_id_2 ) );

		$references_2 = array();
		$jsonld_2     = $this->post_to_jsonld_converter->convert( $post->ID, $references_2, $references_2, new Relations() );

		$this->assertEquals( $entity_uri_2, $jsonld_2['author']['@id'] );
		$this->assertArraySubset( array( $entity_id_2 ), $references_2 );

		// Delete the author entity and check that the author triple is properly generated.
		delete_user_meta( $author_id, Wordlift_User_Service::ENTITY_META_KEY );
		$references_3 = array();
		$jsonld_3 = $this->post_to_jsonld_converter->convert( $post->ID, $references_3, $references_3, new Relations() );
		$this->assertEquals( $author_uri, $jsonld_3['author']['@id'] );

	}

	/**
	 * Test that the `wl_post_jsonld` filter is not called on inexistent posts.
	 *
	 * @since 3.14.0
	 */
	public function test_convert_post_not_found() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$called = 0;

		add_filter( 'wl_post_jsonld', function () use ( &$called ) {
			$called ++;
		} );

		$result = $this->post_to_jsonld_converter->convert( PHP_INT_MAX , $references, $references, new Relations());

		$this->assertNull( $result, "Calling convert on a post not found returns NULL." );

		$this->assertEquals( 0, $called, "When the post is not found `wl_post_jsonld` is not found." );

	}

	/**
	 * Test that the `wl_post_jsonld` filter is called on posts and that it's
	 * possible to tweak the JSON-LD structure.
	 *
	 * @since 3.14.0
	 */
	public function test_convert_post() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$called = 0;

		add_filter( 'wl_post_jsonld', function ( $jsonld ) use ( &$called ) {
			$called ++;
			$jsonld['check'] = true;

			return $jsonld;
		} );

		$post_id = $this->factory()->post->create();

		$references = array();
		$result = $this->post_to_jsonld_converter->convert( $post_id, $references, $references, new Relations() );

		$this->assertTrue( is_array( $result ), 'Convert returns an array.' );

		$this->assertArrayHasKey( 'check', $result );

		$this->assertTrue( $result['check'] );

		$this->assertEquals( 1, $called, 'Filter `wl_post_jsonld` is called once.' );

	}

	public function test_issue_858() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		// Create a post.
		$post_id = $this->factory()->post->create();

		// Create an event to bind to the post.
		$event_post_id = $this->factory()->post->create( array(
			'post_type' => 'entity',
		) );
		Wordlift_Entity_Type_Service::get_instance()->set( $event_post_id, 'http://schema.org/Event' );

		// Check that the event type is set.
		$this->assertTrue( Wordlift_Entity_Type_Service::get_instance()->has_entity_type( $event_post_id, 'http://schema.org/Event' ) );

		// Create a place to bind to the event.
		$place_post_id = $this->factory()->post->create( array(
			'post_type' => 'entity',
		) );
		Wordlift_Entity_Type_Service::get_instance()->set( $place_post_id, 'http://schema.org/Place' );

		// Bind the place as location for the event.
		add_post_meta( $event_post_id, Wordlift_Schema_Service::FIELD_LOCATION, $place_post_id );

		// Check that the place type id set.
		$this->assertTrue( Wordlift_Entity_Type_Service::get_instance()->has_entity_type( $place_post_id, 'http://schema.org/Place' ) );

		// Connect the event to the post and the place to the event.
		wl_core_add_relation_instances( $post_id, 'when', array( $event_post_id ) );
		wl_core_add_relation_instances( $event_post_id, 'where', array( $place_post_id ) );

		// Convert the post to json-ld.
		$references = array();
		$reference_infos = array();
		$this->post_to_jsonld_converter->convert( $post_id, $references, $reference_infos, new Relations()  );


		// Check that the references contain both the event and the place.
		$this->assertContains( $event_post_id, $references, 'References must contain the event post id.' );
		$this->assertContains( $place_post_id, $references, 'References must contain the place post id.' );
		$this->assertCount( 2, $references, 'References must be 2.' );

	}

	public function test_issue_888_entity_type_not_set() {

		register_post_type( 'a-cpt' );

		// Create an event to bind to the post.
		$post_id = $this->factory()->post->create( array(
			'post_type' => 'a-cpt',
		) );

		// Convert the post to json-ld.
		$references = array();
		$json_ld    = $this->post_to_jsonld_converter->convert( $post_id, $references, $references, new Relations() );

		$this->assertArrayNotHasKey( 'wordCount', $json_ld, '`wordCount` must not be set when the entity type is unknown or `WebPage`.' );

	}

	public function test_issue_888_entity_type_set_to_web_page() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$term = Wordlift_Entity_Type_Service::get_instance()->get_term_by_uri( 'http://schema.org/WebPage' );

		$this->assertTrue( is_a( $term, 'WP_Term' ), 'WebPage should be a WP_Term' );

		register_post_type( 'a-cpt' );

		// Create an event to bind to the post.
		$post_id = $this->factory()->post->create( array(
			'post_type' => 'a-cpt',
		) );

		Wordlift_Entity_Type_Service::get_instance()->set( $post_id, 'http://schema.org/WebPage' );

		$terms = wp_get_post_terms( $post_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		$this->assertCount( 1, $terms, 'There must be one term: WebPage' );
		$this->assertEquals( 'WebPage', $terms[0]->name, 'There must be one term: WebPage.' );
		$this->assertEquals( 'web-page', $terms[0]->slug, 'There must be one term: web-page.' );

		// Convert the post to json-ld.
		$references = array();
		$json_ld    = $this->post_to_jsonld_converter->convert( $post_id, $references, $references, new Relations() );

		$this->assertArrayNotHasKey( 'wordCount', $json_ld, '`wordCount` must not be set when the entity type is unknown or `WebPage`.' );

	}

	public function test_should_be_able_to_use_array_unique_on_references() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$post_reference = new Post_Reference( 1 );
		$term_reference = new Term_Reference( 1 );

		$this->assertSame( (string) $post_reference, Object_Type_Enum::POST . "_1", "Post reference should be 
		able to convert to string" );

		$this->assertSame( (string) $term_reference, Object_Type_Enum::TERM . "_1", "Term reference should be 
		able to convert to string" );

		$this->assertCount( 2, array_unique( array(
			$post_reference,
			$post_reference,
			$term_reference,
			$term_reference
		) ), 'Duplicate references should not be present ' );

	}

	public function test_when_the_article_is_linked_to_entity_should_not_have_duplicate_mentions() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$post = $this->factory()->post->create();

		// create an entity and link it to this post.
		$entity = $this->factory()->post->create( array( 'post_type' => 'entity' ) );

		// create a relation.
		wl_core_add_relation_instance( $post, WL_WHAT_RELATION, $entity );
		// create duplicate relation
		wl_core_add_relation_instance( $post, WL_WHAT_RELATION, $entity );

		$jsonld = Wordlift_Jsonld_Service::get_instance()->get_jsonld(
			false,
			$post
		);

		$this->assertArrayHasKey( 'mentions', $jsonld[0] );

		$mentions = $jsonld[0]['mentions'];
		$this->assertCount( 1, $mentions );
		$this->assertEquals( array( '@id' => wl_get_entity_uri( $entity ) ), $mentions[0] );
	}


	public function test_when_the_linked_entity_has_no_labels_should_not_add_to_about() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$post = $this->factory()->post->create();

		// create a tag.
		$tag     = wp_create_tag( 'linked_tag' );
		$term_id = $tag['term_id'];
		// create an entity.
		$entity = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		add_term_meta( $term_id, '_wl_entity_id', wl_get_entity_uri( $entity ) );

		// set this tag to the post.
		wp_set_object_terms( $post, array( $term_id ), 'post_tag' );

		wp_update_post( array(
			'ID'         => $entity,
			'post_title' => ''
		) );

		// get the jsonld
		$jsonld = Wordlift_Jsonld_Service::get_instance()->get_jsonld(
			false,
			$post
		);

		$article_jsonld = $jsonld[0];
		$this->assertFalse( array_key_exists( 'about', $article_jsonld ), 'About should not be present since it has empty labels' );
	}


	public function test_when_the_linked_entity_title_matches_the_title_of_post_should_add_it_to_about() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$post   = $this->factory()->post->create( array( 'post_title' => 'Windows 7 ' ) );
		$entity = $this->factory()->post->create( array( 'post_type' => 'entity', 'post_title' => 'Windows' ) );
		wl_core_add_relation_instance( $post, WL_WHAT_RELATION, $entity );
		// get the jsonld
		$jsonld         = Wordlift_Jsonld_Service::get_instance()->get_jsonld(
			false,
			$post
		);
		$article_jsonld = $jsonld[0];
		$this->assertTrue( array_key_exists( 'about', $article_jsonld ), 'About should  be present since the title matches' );
		$this->assertCount( 1, $article_jsonld['about'] );
		$this->assertSame( array( '@id' => wl_get_entity_uri( $entity ) ), $article_jsonld['about'][0], 'The entity id should be correctly added on about key' );
	}

	/**
	 * Add the author.url to the article markup.
	 *
	 * @see https://github.com/insideout10/wordlift-plugin/issues/1461
	 */
	public function test_author_url_property_should_be_present_on_the_post_jsonld() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$user_id = wl_test_create_user();

		$empty_references = array();

		$result = Wordlift_Post_To_Jsonld_Converter::get_instance()->get_author( $user_id, $empty_references );

		$this->assertArrayHasKey( 'url', $result );

		$this->assertSame( get_author_posts_url( $user_id ), $result['url'], 'The author url should be present on the jsonld' );
	}


	/**
	 * Get the word count for a {@link WP_Post}.
	 *
	 * @param int $post_id The {@link WP_Post} `id`.
	 *
	 * @return int The word count.
	 * @since 3.14.0
	 *
	 */
	private static function word_count( $post_id ) {

		$adapter = new Wordlift_Post_Adapter( $post_id );

		return $adapter->word_count();
	}

}
