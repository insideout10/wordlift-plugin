<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 29.08.18
 * Time: 16:57
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

	function setUp() {
		parent::setUp();

		$this->converter = new Post_To_Jsonld_Converter_Test(
			Wordlift_Entity_Type_Service::get_instance(),
			Wordlift_Entity_Service::get_instance(),
			Wordlift_User_Service::get_instance(),
			Wordlift_Attachment_Service::get_instance()
		);
	}

	public function test_relative_to_context() {

		$relative_to_context_1 = $this->converter->relative_to_context( 'http://schema.org/Organization' );
		$this->assertEquals( 'Organization', $relative_to_context_1, 'Expect the `http://schema.org/` part to be stripped off.' );

		$relative_to_context_2 = $this->converter->relative_to_context( 'http://example.org/Organization' );
		$this->assertEquals( 'http://example.org/Organization', $relative_to_context_2, 'Expect the `http://schema.org/` part not to be stripped off.' );

	}

	public function test_convert() {

		$post_id = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		// Add 3 entity type terms.
		wp_set_post_terms( $post_id, array(
			'person',
			'creative-work',
			'local-business'
		), Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		$references = array();
		$json       = $this->converter->convert( $post_id, $references );

		var_dump( $json );

		$this->assertArrayHasKey( '@context', $json, 'Key `@context` must exist.' );
		$this->assertEquals( 'http://schema.org', $json['@context'] );

		$this->assertArrayHasKey( '@id', $json, 'Key `@id` must exist.' );
		$this->assertArrayHasKey( '@type', $json, 'Key `@type` must exist.' );
		$this->assertArrayHasKey( 'description', $json, 'Key `description` must exist.' );
		$this->assertArrayHasKey( 'mainEntityOfPage', $json, 'Key `mainEntityOfPage` must exist.' );

	}

	public function test_set_image_size() {

	}

}

/**
 * Convenience class to test {@link Wordlift_Abstract_Post_To_Jsonld_Converter}.
 *
 * @since 3.20.0
 */
class Post_To_Jsonld_Converter_Test extends Wordlift_Abstract_Post_To_Jsonld_Converter {

}
