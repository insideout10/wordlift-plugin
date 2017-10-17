<?php
/**
 * Test Entity functions.
 */

require_once 'functions.php';

/**
 * Class EntityTest
 */
class EntityFunctionsTest extends Wordlift_Unit_Test_Case {

	/**
	 * Set up the test.
	 */
	function setUp() {
		parent::setUp();

		// We don't need to check the remote Linked Data store.
		Wordlift_Unit_Test_Case::turn_off_entity_push();

		// Configure WordPress with the test settings.
		wl_configure_wordpress_test();

		// Empty the blog.
		wl_empty_blog();

	}

	/**
	 * Check entity URI building.
	 */
	function testEntityURIforAPost() {

		$post_id = wl_create_post( '', 'test', 'This is a test' );

		$expected_uri = wl_configuration_get_redlink_dataset_uri() . '/post/this_is_a_test';
		$this->assertEquals( $expected_uri, wl_build_entity_uri( $post_id ) );

	}

	/**
	 * Check entity URI building.
	 */
	function testEntityURIforAnEntity() {

		$post_id = wl_create_post( '', 'test', 'This is a test', 'draft', 'entity' );

		$expected_uri = wl_configuration_get_redlink_dataset_uri() . '/entity/this_is_a_test';
		$this->assertEquals( $expected_uri, wl_build_entity_uri( $post_id ) );

	}

	/**
	 * Tests the *wl_get_meta_type* function
	 */
	function testEntityGetMetaType() {

		$type = wl_get_meta_type( Wordlift_Schema_Service::FIELD_GEO_LATITUDE );
		$this->assertEquals( 'double', $type );
		$type = wl_get_meta_type( 'latitude' );
		$this->assertEquals( 'double', $type );
		$this->assertEquals( Wordlift_Schema_Service::DATA_TYPE_DOUBLE, $type );

		$type = wl_get_meta_type( Wordlift_Schema_Service::FIELD_DATE_START );
		$this->assertEquals( 'date', $type );
		$type = wl_get_meta_type( 'startDate' );
		$this->assertEquals( 'date', $type );
		$this->assertEquals( Wordlift_Schema_Service::DATA_TYPE_DATE, $type );

		$type = wl_get_meta_type( Wordlift_Schema_Service::FIELD_LOCATION );
		$this->assertEquals( 'uri', $type );
		$type = wl_get_meta_type( 'location' );
		$this->assertEquals( 'uri', $type );
		$this->assertEquals( Wordlift_Schema_Service::DATA_TYPE_URI, $type );

		$type = wl_get_meta_type( 'random_silly_name' );
		$this->assertEquals( null, $type );
	}


	/**
	 * Tests the *wl_get_meta_constraints* function
	 */
	function testWlEntityTaxonomyGetCustomFields() {
		// Create entity and get custom_fields by id.
		$place_id = wl_create_post( 'Entity 1 Text', 'entity-1', 'Entity 1 Title', 'publish', 'entity' );
		wl_set_entity_main_type( $place_id, 'http://schema.org/Place' );

		$custom_fields = wl_entity_taxonomy_get_custom_fields( $place_id );

		$this->assertArrayHasKey( Wordlift_Schema_Service::FIELD_GEO_LATITUDE, $custom_fields );
		$this->assertArrayHasKey( Wordlift_Schema_Service::FIELD_GEO_LONGITUDE, $custom_fields );
		$this->assertArrayHasKey( Wordlift_Schema_Service::FIELD_ADDRESS, $custom_fields );
		$this->assertArrayNotHasKey( Wordlift_Schema_Service::FIELD_LOCATION, $custom_fields );    // Negative test.
		$this->assertArrayHasKey( 'predicate', $custom_fields[ Wordlift_Schema_Service::FIELD_GEO_LATITUDE ] );
		$this->assertArrayHasKey( 'type', $custom_fields[ Wordlift_Schema_Service::FIELD_GEO_LATITUDE ] );
		$this->assertArrayHasKey( 'export_type', $custom_fields[ Wordlift_Schema_Service::FIELD_GEO_LATITUDE ] );

		// Get all custom_fileds.
		$custom_fields = wl_entity_taxonomy_get_custom_fields();
		$custom_fields = print_r( $custom_fields, true ); // Stringify for brevity.

		$this->assertContains( Wordlift_Schema_Service::FIELD_GEO_LATITUDE, $custom_fields );
		$this->assertContains( Wordlift_Schema_Service::FIELD_GEO_LONGITUDE, $custom_fields );
		$this->assertContains( Wordlift_Schema_Service::FIELD_ADDRESS, $custom_fields );
		$this->assertContains( Wordlift_Schema_Service::FIELD_DATE_START, $custom_fields );
		$this->assertContains( Wordlift_Schema_Service::FIELD_DATE_END, $custom_fields );
		$this->assertContains( Wordlift_Schema_Service::FIELD_LOCATION, $custom_fields );
	}

	/**
	 * Test the costume field inheritance
	 */
	function testWlEntityTaxonomyCustomFieldsInheritance() {

		// Create entity and set type.
		$business_id = wl_create_post( 'Entity 1 Text', 'entity-1', 'Entity 1 Title', 'publish', 'entity' );
		wl_set_entity_main_type( $business_id, 'http://schema.org/LocalBusiness' );

		// Get custom fields.
		$custom_fields = wl_entity_taxonomy_get_custom_fields( $business_id );

		// Check inherited custom fields:
		// sameAs from Thing.
		$this->assertArrayHasKey( Wordlift_Schema_Service::FIELD_SAME_AS, $custom_fields );
		// latitude from Place.
		$this->assertArrayHasKey( Wordlift_Schema_Service::FIELD_GEO_LATITUDE, $custom_fields );
		// founder from Organization.
		$this->assertArrayHasKey( Wordlift_Schema_Service::FIELD_FOUNDER, $custom_fields );
		// negative test.
		$this->assertArrayNotHasKey( Wordlift_Schema_Service::FIELD_DATE_START, $custom_fields );
	}

	/**
	 * Tests the *wl_get_meta_constraints* function
	 */
	function testEntityGetMetaConstraints() {

		// TODO: complete this test.
		$fields = wl_entity_taxonomy_get_custom_fields();
	}

	/**
	 * Test the wl_get_entity_post_ids_by_uris function
	 */
	function testwl_get_entity_post_ids_by_uris() {

		// The existence of sticky might break results due to weird way
		// wordpress handles them in queries. Get one to exist as background noise.
		$sticky_post_id = wl_create_post( '', 'sticky-1', uniqid( 'sticky', true ), 'publish' );
		stick_post( $sticky_post_id );

		$entity_1_id = wl_create_post( '', 'entity-1', uniqid( 'entity', true ), 'draft', 'entity' );
		wl_schema_set_value( $entity_1_id, 'sameAs', get_permalink( $entity_1_id ) );

		$this->assertEquals( 0, count( wl_get_entity_post_ids_by_uris( '' ) ) );

		// check that the entity is returned and only it.
		$entities = wl_get_entity_post_ids_by_uris( array( get_permalink( $entity_1_id ) ) );
		$this->assertCount( 1, $entities );
		$this->assertEquals( $entity_1_id, $entities[0] );
	}
}
