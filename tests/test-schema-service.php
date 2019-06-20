<?php
/**
 * Tests: Schema Service Test.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the {@link Wordlift_Schema_Service_Test} class.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */
class Wordlift_Schema_Service_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Schema_Service} under testing.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift_Schema_Service $schema_service The {@link Wordlift_Schema_Service} under testing.
	 */
	private $schema_service;

	/**
	 * The Entity service.
	 *
	 * @since  3.18.0
	 * @access protected
	 * @var \Wordlift_Entity_Service $entity_service The Entity service.
	 */
	private $entity_service;

	/**
	 * @inheritdoc
	 */
	function setUp() {
		parent::setUp();

		$this->schema_service = $this->get_wordlift_test()->get_schema_service();
		$this->entity_service = $this->get_wordlift_test()->get_entity_service();

	}

	/**
	 * Test the predicates in the schema configuration.
	 *
	 * @since 3.14.0
	 */
	function test_predicates() {

		$schemas = array(
			'thing',
			'creative-work',
			'event',
			'organization',
			'person',
			'place',
			'local-business',
			'recipe',
		);

		foreach ( $schemas as $schema ) {
			$this->check_custom_fields( $schema );
		}

	}

	/**
	 * Check the custom fields for each schema.
	 *
	 * @since 3.14.0
	 *
	 * @param string $name The schema name.
	 */
	private function check_custom_fields( $name ) {

		$schema = $this->schema_service->get_schema( $name );

		$this->assertTrue( is_array( $schema ) );
		$this->assertArrayHasKey( 'custom_fields', $schema );

		foreach ( $schema['custom_fields'] as $field => $params ) {
			$this->assertArrayHasKey( 'predicate', $params );
			// Check that the predicate starts with `http`.
			$this->assertStringStartsWith( 'http', $params['predicate'] );
		}

	}

	/**
	 * Test `get_field` method for `wl_email` field.
	 *
	 * @since 3.18.0
	 *
	 * @return void
	 */
	public function test_get_field_email() {

		// Init the expected properties.
		$expected = array(
			'predicate'   => 'http://schema.org/email',
			'type'        => Wordlift_Schema_Service::DATA_TYPE_STRING,
			'export_type' => 'xsd:string',
			'constraints' => '',
		);

		// Get the property for `wl_email` field.
		$actual = $this->schema_service->get_field( 'wl_email' );

		// Check that both expected and actual are the same.
		$this->assertEquals( $actual, $expected );
	}

	/**
	 * Test `get_field` method for `wl_address`.
	 *
	 * @since 3.18.0
	 *
	 * @return void
	 */
	public function test_get_field_address() {
		// Init the expected properties.
		$expected = array(
			'predicate'   => 'http://schema.org/streetAddress',
			'type'        => Wordlift_Schema_Service::DATA_TYPE_STRING,
			'export_type' => 'xsd:string',
			'constraints' => '',
			'input_field' => 'address',
		);
		// Get the property for `wl_email` field.
		$actual = $this->schema_service->get_field( 'wl_address' );

		// Check that both expected and actual are the same.
		$this->assertEquals( $actual, $expected );
	}

	/**
	 * Test `get_schema` method.
	 *
	 * @since 3.18.0
	 *
	 * @return void
	 */
	public function test_get_schema() {

		// Get schemas.
		$thing_schema = $this->schema_service->get_schema( 'thing' );
		$event_schema = $this->schema_service->get_schema( 'event' );

		// Test thing schema.
		$this->assertEquals( $thing_schema['css_class'], 'wl-thing' );
		$this->assertEquals( $thing_schema['uri'], 'http://schema.org/Thing' );
		$this->assertArrayHasKey( 'custom_fields', $thing_schema );
		$this->assertArrayHasKey( 'linked_data', $thing_schema );
		$this->assertArrayHasKey( 'entity_same_as', $thing_schema['custom_fields'] );

		// This value may change if we add/remove properties from `linked_data`.
		$this->assertCount( 13, $thing_schema['linked_data'] );

		// Test event schema.
		$this->assertEquals( $event_schema['css_class'], 'wl-event' );
		$this->assertEquals( $event_schema['uri'], 'http://schema.org/Event' );
		$this->assertArrayHasKey( 'custom_fields', $event_schema );
		$this->assertArrayHasKey( 'linked_data', $event_schema );
		$this->assertArrayHasKey( 'entity_same_as', $event_schema['custom_fields'] );

		// This value may change if we add/remove properties from `linked_data`.
		$this->assertCount( 18, $event_schema['linked_data'] );

	}

	/**
	 * Tests the `get_renditions` method.
	 *
	 * @since 3.18.0
	 *
	 * @return void
	 */
	public function test_get_renditions() {
		// Get all renditions.
		$renditions = $this->schema_service->get_renditions();

		$this->assertInternalType( 'array', $renditions );
		$this->assertNotEmpty( $renditions );

		// The renditions classes.
		$rendition_classes = array(
			'Wordlift_Default_Sparql_Tuple_Rendition',
			'Wordlift_Address_Sparql_Tuple_Rendition',
		);

		// Loop through all renditions and check their type and instance.
		foreach ( $renditions as $rendition ) {
			$this->assertInternalType( 'object', $rendition );

			// Get the rendition class name.
			$class_name = get_class( $rendition );

			// Check that the class name exists in rendition classes.
			$this->assertContains( $class_name, $rendition_classes );
		}
	}

	/**
	 * Tests the `get_schema_by_uri`.
	 *
	 * @since 3.18.0
	 *
	 * @return void
	 */
	public function test_get_schema_by_uri() {

		// The schema uri we are looking for.
		$schema_uri = 'http://schema.org/Place';

		// Get the place schema.
		$place_schema = $this->schema_service->get_schema_by_uri( $schema_uri );

		// Test place schema.
		$this->assertEquals( $place_schema['css_class'], 'wl-place' );
		$this->assertEquals( $place_schema['uri'], $schema_uri );
		$this->assertArrayHasKey( 'custom_fields', $place_schema );
		$this->assertArrayHasKey( 'linked_data', $place_schema );
		$this->assertArrayHasKey( 'entity_same_as', $place_schema['custom_fields'] );

	}

}
