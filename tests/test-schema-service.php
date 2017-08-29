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
	 * @inheritdoc
	 */
	function setUp() {
		parent::setUp();

		$this->schema_service = $this->get_wordlift_test()->get_schema_service();

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
			'localbusiness',
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

}
