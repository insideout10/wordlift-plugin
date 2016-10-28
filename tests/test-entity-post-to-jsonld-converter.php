<?php

/**
 * This file defines tests for the {@link Wordlift_Entity_Post_To_Jsonld_Converter} class.
 */

/**
 * Test the {@link Wordlift_Entity_Post_To_Jsonld_Converter} class.
 *
 * @since 3.8.0
 */
class Wordlift_Entity_Post_To_Jsonld_Converter_Test extends WP_UnitTestCase {

	/**
	 * The {@link Wordlift_Entity_Post_To_Jsonld_Converter} to test.
	 *
	 * @since 3.8.0
	 * @access private
	 * @var Wordlift_Entity_Post_To_Jsonld_Converter $entity_post_to_jsonld_converter A {@link Wordlift_Entity_Post_To_Jsonld_Converter} instance.
	 */
	private $entity_post_to_jsonld_converter;

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();

		$property_getter                       = Wordlift_Property_Getter_Factory::create( $this );
		$this->entity_post_to_jsonld_converter = new Wordlift_Entity_Post_To_Jsonld_Converter( $this, $this, $property_getter );

	}


}
