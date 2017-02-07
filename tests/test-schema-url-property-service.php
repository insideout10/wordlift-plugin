<?php

/**
 * Test the {@link Wordlift_Schema_Url_Property_Service} class.
 *
 * @since 3.6.0
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

		// We don't need to check the remote Linked Data store.
		Wordlift_Unit_Test_Case::turn_off_entity_push();;

		$this->schema_url_property_service = Wordlift_Schema_Url_Property_Service::get_instance();
	}

	/**
	 * Test getting/setting the post meta.
	 *
	 * @since 3.6.0
	 */
	public function test() {

		// Create a fake post.
		$id = $this->factory->post->create();

		// 1. Check that the post meta returned when there's no meta set is <permalink>
		$meta = get_post_meta( $id, Wordlift_Schema_Url_Property_Service::META_KEY );

		$this->assertTrue( is_array( $meta ) );
		$this->assertCount( 1, $meta );
		$this->assertEquals( '<permalink>', $meta[0] );

		// Check that Wordlift_Schema_Url_Property_Service returns the same <permalink>.
		$values = $this->schema_url_property_service->get( $id );

		$this->assertTrue( is_array( $values ) );
		$this->assertCount( 1, $values );
		$this->assertEquals( '<permalink>', $values[0] );

		// Check that the query is empty.
		$query = $this->schema_url_property_service->get_insert_query( 'http://example.org', $id );

		$this->assertNotEmpty( $query );
		$this->assertFalse( strpos( $query, '<permalink>' ) );

		// 2. Now add an empty string post meta.
		add_post_meta( $id, Wordlift_Schema_Url_Property_Service::META_KEY, '' );

		// Check that get_post_meta returns the empty string.
		$meta = get_post_meta( $id, Wordlift_Schema_Url_Property_Service::META_KEY );

		$this->assertTrue( is_array( $meta ) );
		$this->assertCount( 1, $meta );
		$this->assertEquals( '', $meta[0] );

		// Check that Wordlift_Schema_Url_Property_Service returns NULL instead.
		$values = $this->schema_url_property_service->get( $id );

		$this->assertNull( $values );

		// Check that the query is empty.
		$query = $this->schema_url_property_service->get_insert_query( 'http://example.org', $id );

		$this->assertEmpty( $query );

		// 3. Empty the post meta and add another value.
		delete_post_meta( $id, Wordlift_Schema_Url_Property_Service::META_KEY );

		// Now add a string post meta.
		add_post_meta( $id, Wordlift_Schema_Url_Property_Service::META_KEY, 'http://example.org' );

		// Check that get_post_meta returns the empty string.
		$meta = get_post_meta( $id, Wordlift_Schema_Url_Property_Service::META_KEY );

		$this->assertTrue( is_array( $meta ) );
		$this->assertCount( 1, $meta );
		$this->assertEquals( 'http://example.org', $meta[0] );

		// Check that Wordlift_Schema_Url_Property_Service returns NULL instead.
		$values = $this->schema_url_property_service->get( $id );

		$this->assertTrue( is_array( $values ) );
		$this->assertCount( 1, $values );
		$this->assertEquals( 'http://example.org', $values[0] );

		// Check that the query is empty.
		$query = $this->schema_url_property_service->get_insert_query( 'http://example.org', $id );

		$this->assertNotEmpty( $query );
		$this->assertFalse( strpos( $query, '<permalink>' ) );

	}

	/**
	 * Test the metabox created for this property.
	 *
	 * @since 3.6.0
	 */
	public function testMetabox() {

		// Create a new metabox and add this property.
		$metabox = new WL_Metabox();
		$metabox->add_field( array( Wordlift_Schema_Url_Property_Service::META_KEY => $this->schema_url_property_service->get_compat_definition() ) );

		// Check that we have one metabox field with the class and label set by this property.
		$this->assertCount( 1, $metabox->fields );
		$this->assertEquals( $this->schema_url_property_service->get_metabox_class(), get_class( $metabox->fields[0] ) );
		$this->assertEquals( $this->schema_url_property_service->get_metabox_label(), $metabox->fields[0]->label );

	}

}
