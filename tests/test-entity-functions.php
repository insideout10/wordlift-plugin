<?php
/**
 * Class EntityTest
 *
 * @group entity
 */
class Wordlift_Entity_Functions_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Tests the *wl_get_meta_type* function
	 */
	function test_entity_get_meta_type() {

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
	function test_wl_entity_taxonomy_get_custom_fields() {
		// Create entity and get custom_fields by id
		$place_id = wl_create_post( "Entity 1 Text", 'entity-1', "Entity 1 Title", 'publish', 'entity' );
		wl_set_entity_main_type( $place_id, 'http://schema.org/Place' );

		$custom_fields = wl_entity_taxonomy_get_custom_fields( $place_id );

		$this->assertArrayHasKey( Wordlift_Schema_Service::FIELD_GEO_LATITUDE, $custom_fields );
		$this->assertArrayHasKey( Wordlift_Schema_Service::FIELD_GEO_LONGITUDE, $custom_fields );
		$this->assertArrayHasKey( Wordlift_Schema_Service::FIELD_ADDRESS, $custom_fields );
		$this->assertArrayNotHasKey( Wordlift_Schema_Service::FIELD_LOCATION, $custom_fields );    // Negative test
		$this->assertArrayHasKey( 'predicate', $custom_fields[ Wordlift_Schema_Service::FIELD_GEO_LATITUDE ] );
		$this->assertArrayHasKey( 'type', $custom_fields[ Wordlift_Schema_Service::FIELD_GEO_LATITUDE ] );
		$this->assertArrayHasKey( 'export_type', $custom_fields[ Wordlift_Schema_Service::FIELD_GEO_LATITUDE ] );

		// Get all custom_fileds
		$custom_fields = wl_entity_taxonomy_get_custom_fields();
		$custom_fields = print_r( $custom_fields, true ); // Stringify for brevity

		$this->assertContains( Wordlift_Schema_Service::FIELD_GEO_LATITUDE, $custom_fields );
		$this->assertContains( Wordlift_Schema_Service::FIELD_GEO_LONGITUDE, $custom_fields );
		$this->assertContains( Wordlift_Schema_Service::FIELD_ADDRESS, $custom_fields );
		$this->assertContains( Wordlift_Schema_Service::FIELD_DATE_START, $custom_fields );
		$this->assertContains( Wordlift_Schema_Service::FIELD_DATE_END, $custom_fields );
		$this->assertContains( Wordlift_Schema_Service::FIELD_LOCATION, $custom_fields );
	}

	function test_wl_entity_taxonomy_custom_fields_inheritance() {

		// Create entity and set type
		$business_id = wl_create_post( "Entity 1 Text", 'entity-1', "Entity 1 Title", 'publish', 'entity' );
		wl_set_entity_main_type( $business_id, 'http://schema.org/LocalBusiness' );

		// Get custom fields
		$custom_fields = wl_entity_taxonomy_get_custom_fields( $business_id );

		// Check inherited custom fields:
		// sameAs from Thing
		$this->assertArrayHasKey( Wordlift_Schema_Service::FIELD_SAME_AS, $custom_fields );
		// latitude from Place
		$this->assertArrayHasKey( Wordlift_Schema_Service::FIELD_GEO_LATITUDE, $custom_fields );
		// founder from Organization
		$this->assertArrayHasKey( Wordlift_Schema_Service::FIELD_FOUNDER, $custom_fields );
		// negative test
		$this->assertArrayNotHasKey( Wordlift_Schema_Service::FIELD_DATE_START, $custom_fields );
	}

}
