<?php
/**
 * Define tests for the {@link Wordlift_Property_Getter}.
 */

use Wordlift\Content\WordPress\Wordpress_Content_Id;
use Wordlift\Content\WordPress\Wordpress_Content_Service;
use Wordlift\Object_Type_Enum;

/**
 * Test the {@link Wordlift_Property_Getter} class.
 *
 * @since 3.8.0
 * @group backend
 */
class Wordlift_Property_Getter_Test extends Wordlift_Unit_Test_Case {

	const ENTITY_URI = 'http://data.example.org/data/entity';

	private $entity_service;

	/**
	 * The {@link Wordlift_Property_Getter} instance to test.
	 *
	 * @since  3.8.0
	 * @access private
	 * @var Wordlift_Property_Getter $property_getter The {@link Wordlift_Property_Getter} instance.
	 */
	private $property_getter;

	/**
	 * A post id of a generated post for tests.
	 *
	 * @since  3.8.0
	 * @access private
	 * @var int $post_id A post id.
	 */
	private $post_id;

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();

		// We don't need to check the remote Linked Data store.
		Wordlift_Unit_Test_Case::turn_off_entity_push();;

		$this->entity_service  = Wordlift_Entity_Service::get_instance();
		$this->property_getter = Wordlift_Property_Getter_Factory::create();

		$this->post_id = $this->factory->post->create();

		Wordlift_Configuration_Service::get_instance()
		                              ->set_dataset_uri( 'http://data.example.org/data/' );

		Wordpress_Content_Service::get_instance()
		                         ->set_entity_id( Wordpress_Content_Id::create_post( $this->post_id ), self::ENTITY_URI );

	}

	/**
	 * Test when a meta key is not set.
	 *
	 * @since 3.8.0
	 */
	public function test_default_getter_meta_key_not_set() {

		$this->assertTrue( is_array( $this->property_getter->get( $this->post_id, 'a_non_existent_meta_key', Object_Type_Enum::POST ) ) );
		$this->assertCount( 0, $this->property_getter->get( $this->post_id, 'a_non_existent_meta_key', Object_Type_Enum::POST ) );

	}

	/**
	 * Test when a meta key is set and the property getter resorts to the default property getter.
	 *
	 * @since 3.8.0
	 */
	public function test_default_getter_meta_key_set() {

		$value = rand_str();
		add_post_meta( $this->post_id, 'a_meta_key', $value );
		$this->assertEquals( array( $value ), $this->property_getter->get( $this->post_id, 'a_meta_key', Object_Type_Enum::POST ) );

	}

	/**
	 * Test entity references' fields.
	 *
	 * @since 3.8.0
	 */
	public function test_entity_references_fields() {

		foreach (
			array(
				Wordlift_Schema_Service::FIELD_LOCATION,
				Wordlift_Schema_Service::FIELD_FOUNDER,
				Wordlift_Schema_Service::FIELD_AUTHOR,
				Wordlift_Schema_Service::FIELD_KNOWS,
				Wordlift_Schema_Service::FIELD_BIRTH_PLACE,
				Wordlift_Schema_Service::FIELD_AFFILIATION,
			) as $field_name
		) {

			add_post_meta( $this->post_id, $field_name, $this->post_id );

			/** @var Wordlift_Property_Entity_Reference[] $values */
			$values = $this->property_getter->get( $this->post_id, $field_name, Object_Type_Enum::POST );
			$this->assertTrue( is_array( $values ) );
			$this->assertCount( 1, $values );
			$this->assertTrue( $values[0] instanceof Wordlift_Property_Entity_Reference );
			$this->assertEquals( self::ENTITY_URI, $values[0]->get_url() );

		}

	}

	/**
	 * Test when a defined URL is set.
	 *
	 * @since 3.8.0
	 */
	public function test_url_no_permalink() {

		$expected = 'http://example.org/' . rand_str();
		add_post_meta( $this->post_id, Wordlift_Url_Property_Service::META_KEY, $expected );

		/** @var Wordlift_Property_Entity_Reference[] $values */
		$values = $this->property_getter->get( $this->post_id, Wordlift_Url_Property_Service::META_KEY, Object_Type_Enum::POST );
		$this->assertTrue( is_array( $values ) );
		$this->assertCount( 1, $values );
		$this->assertContains( $expected, $values[0] );
	}

	/**
	 * Test when a <permalink> is used.
	 *
	 * @since 3.8.0
	 */
	public function test_url_permalink() {

		add_post_meta( $this->post_id, Wordlift_Url_Property_Service::META_KEY, '<permalink>' );

		/** @var Wordlift_Property_Entity_Reference[] $values */
		$values = $this->property_getter->get( $this->post_id, Wordlift_Url_Property_Service::META_KEY, Object_Type_Enum::POST );
		$this->assertTrue( is_array( $values ) );
		$this->assertCount( 1, $values );
		$this->assertEquals( get_permalink( $this->post_id ), $values[0] );

	}

	/**
	 * Test the geocoordinates.
	 *
	 * @since 3.8.0
	 */
	public function test_geo_coordinates() {

		$expected = 1.1;

		foreach (
			array(
				Wordlift_Schema_Service::FIELD_GEO_LATITUDE,
				Wordlift_Schema_Service::FIELD_GEO_LONGITUDE,
			) as $field_name
		) {

			add_post_meta( $this->post_id, $field_name, $expected );

			/** @var Wordlift_Property_Entity_Reference[] $values */
			$values = $this->property_getter->get( $this->post_id, $field_name, Object_Type_Enum::POST );
			$this->assertTrue( is_array( $values ) );
			$this->assertCount( 1, $values );
			$this->assertEquals( $expected, $values[0] );

		}

	}

	/**
	 * Mock the {@link Wordlift_Entity_Service} get_uri function.
	 *
	 * @param int $post_id The post id.
	 *
	 * @return string The entity URI.
	 * @since 3.8.0
	 *
	 */
	public function get_uri( $post_id ) {

		return self::ENTITY_URI;
	}

}
