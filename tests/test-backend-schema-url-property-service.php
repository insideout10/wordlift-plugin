<?php
/**
 * Tests.
 *
 * @since 3.6.0
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Test the {@link Wordlift_Schema_Url_Property_Service} class.
 *
 * @since 3.6.0
 * @group backend
 */
class Wordlift_Schema_Url_Property_Service_Test extends Wordlift_Unit_Test_Case {

	/**
	 * @var Wordlift_Schema_Url_Property_Service $schema_url_property_service
	 */
	private $schema_url_property_service;

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();

		$this->schema_url_property_service = Wordlift_Schema_Url_Property_Service::get_instance();
	}

	/**
	 * Test the metabox created for this property.
	 *
	 * @since 3.6.0
	 */
	public function test_metabox() {

		// Create a new metabox and add this property.
		$metabox = new WL_Metabox();
		$metabox->add_field( array( Wordlift_Schema_Url_Property_Service::META_KEY => $this->schema_url_property_service->get_compat_definition() ) );

		// Check that we have one metabox field with the class and label set by this property.
		$this->assertCount( 1, $metabox->fields );
		$this->assertEquals( $this->schema_url_property_service->get_metabox_class(), get_class( $metabox->fields[0] ) );
		$this->assertEquals( $this->schema_url_property_service->get_metabox_label(), $metabox->fields[0]->label );

	}

}
