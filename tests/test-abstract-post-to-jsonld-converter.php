<?php
/**
 * Tests: Abstract Post To Jsonld Converter.
 *
 * Test the {@link Wordlift_Abstract_Post_To_Jsonld_Converter} class.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

use Wordlift\Relation\Relations;

/**
 * Define the Wordlift_Abstract_Post_To_Jsonld_Converter_Test class.
 *
 * @since 3.20.0
 */
class Wordlift_Abstract_Post_To_Jsonld_Converter_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Abstract_Post_To_Jsonld_Converter} instance to test.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var \Wordlift_Abstract_Post_To_Jsonld_Converter $converter The {@link Wordlift_Abstract_Post_To_Jsonld_Converter} instance to test.
	 */
	private $converter;

	/**
	 * {@inheritdoc}
	 */
	function setUp() {
		parent::setUp();

		$this->converter = new Post_To_Jsonld_Converter_Test(
			Wordlift_Entity_Type_Service::get_instance(),
			Wordlift_User_Service::get_instance(),
			Wordlift_Attachment_Service::get_instance(),
			Wordlift_Property_Getter_Factory::create()
		);
	}

	/**
	 * Test the `relative_to_context` function.
	 *
	 * @since 3.20.0
	 */
	public function test_relative_to_context() {

		$relative_to_context_1 = $this->converter->relative_to_context( 'http://schema.org/Organization' );
		$this->assertEquals( 'Organization', $relative_to_context_1, 'Expect the `http://schema.org/` part to be stripped off.' );

		$relative_to_context_2 = $this->converter->relative_to_context( 'http://example.org/Organization' );
		$this->assertEquals( 'http://example.org/Organization', $relative_to_context_2, 'Expect the `http://schema.org/` part not to be stripped off.' );

	}

	/**
	 * Test the `convert` function.
	 *
	 * @since 3.20.0
	 */
	public function test_convert() {

		$post_id = $this->factory()->post->create(
			array(
				'post_type'  => 'entity',
				'post_title' => 'Abstract Post to Json-Ld Converter test_convert',
			)
		);
		// Add 3 entity type terms.
		wp_set_object_terms(
			$post_id,
			array(
				'person',
				'creative-work',
				'local-business',
			),
			Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME
		);

		$references      = array();
		$reference_infos = array();
		$json            = $this->converter->convert( $post_id, $references, $reference_infos, new Relations() );

		$this->assertArrayHasKey( '@context', $json, 'Key `@context` must exist.' );
		$this->assertEquals( 'http://schema.org', $json['@context'] );

		$this->assertArrayHasKey( '@id', $json, 'Key `@id` must exist.' );

		$this->assertArrayHasKey( '@type', $json, 'Key `@type` must exist.' );
		$this->assertCount( 3, $json['@type'], '`@type` must contain 3 types.' );
		$this->assertContains( 'Person', $json['@type'], '`@type` must contain `Person`.' );
		$this->assertContains( 'CreativeWork', $json['@type'], '`@type` must contain `CreativeWork`.' );
		$this->assertContains( 'LocalBusiness', $json['@type'], '`@type` must contain `LocalBusiness`.' );

		$this->assertArrayHasKey( 'description', $json, 'Key `description` must exist.' );
		$this->assertArrayHasKey( 'mainEntityOfPage', $json, 'Key `mainEntityOfPage` must exist.' );

	}

	/**
	 * Test the `set_image_size`.
	 *
	 * @since 3.20.0
	 */
	public function test_set_image_size() {

		$image_1 = Post_To_Jsonld_Converter_Test::set_image_size( array(), array( '', 123, 456 ) );

		$this->assertCount( 2, $image_1, 'Expect the array to have 2 values.' );
		$this->assertArrayHasKey( 'width', $image_1, 'Expect the array to have the `width` key.' );
		$this->assertArrayHasKey( 'height', $image_1, 'Expect the array to have the `height` key.' );
		$this->assertEquals( 123, $image_1['width'], 'Expect the width.' );
		$this->assertEquals( 456, $image_1['height'], 'Expect the height.' );

		$image_2 = Post_To_Jsonld_Converter_Test::set_image_size( array(), array( '', '123px', '456px' ) );

		$this->assertEmpty( $image_2, 'Expect the array to be empty.' );

		$image_3 = Post_To_Jsonld_Converter_Test::set_image_size( array(), array() );

		$this->assertEmpty( $image_3, 'Expect the array to be empty.' );

	}

}

/**
 * Convenience class to test {@link Wordlift_Abstract_Post_To_Jsonld_Converter}.
 *
 * @since 3.20.0
 */
class Post_To_Jsonld_Converter_Test extends Wordlift_Abstract_Post_To_Jsonld_Converter {

}
