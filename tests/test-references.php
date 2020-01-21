<?php

/**
 * Tests: Test References.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */
class Wordlift_References_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * The {@link Wordlift_Linked_Data_Service} instance.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Linked_Data_Service $linked_data_service The {@link Wordlift_Linked_Data_Service} instance.
	 */
	private $linked_data_service;

	/**
	 * @inheritdoc
	 */
	function setUp() {
		parent::setUp();

		$this->entity_service      = $this->get_wordlift_test()->get_entity_service();
		$this->linked_data_service = $this->get_wordlift_test()->get_linked_data_service();

	}

	/**
	 * Test that non entity refer entities.
	 *
	 * @since 3.18.0
	 */
	function test_that_non_entity_has_references() {

		self::turn_on_entity_push();

		// Create required posts/entities.
		$entity_1_id         = $this->entity_factory->create( array(
			'post_status' => 'publish',
			'post_title'  => 'Test References test_that_non_entity_has_references 1',
		) );
		$entity_2_id         = $this->entity_factory->create( array(
			'post_status' => 'publish',
			'post_title'  => 'Test References test_that_non_entity_has_references 2',
		) );
		$custom_post_type_id = $this->factory()->post->create( array(
			'post_type'   => 'custom_post_type',
			'post_status' => 'publish',
			'post_title'  => 'Test References test_that_non_entity_has_references 3',
		) );

		// Add relations.
		wl_core_add_relation_instance( $custom_post_type_id, WL_WHAT_RELATION, $entity_1_id );
		wl_core_add_relation_instance( $custom_post_type_id, WL_WHAT_RELATION, $entity_2_id );

		// Push the custom_post_type.
		$this->linked_data_service->push( $custom_post_type_id );

		// Get the URIs.
		$entity_1_uri         = Wordlift_Sparql_Service::escape_uri( $this->entity_service->get_uri( $entity_1_id ) );
		$entity_2_uri         = Wordlift_Sparql_Service::escape_uri( $this->entity_service->get_uri( $entity_2_id ) );
		$custom_post_type_uri = Wordlift_Sparql_Service::escape_uri( $this->entity_service->get_uri( $custom_post_type_id ) );

		// Prepare the SPARQL query.
		$sparql = "SELECT * WHERE { <$custom_post_type_uri> ?p ?o }";

		// Send the query and get the response.
		$response = rl_sparql_select( $sparql );

		if ( is_wp_error( $response ) ) {
			$this->fail( "Call returned an error: " . $response->get_error_message() );
		}

		$body  = $response['body'];
		$lines = str_getcsv( $body, "\n" );

		$this->assertContains( "http://purl.org/dc/terms/references,$entity_1_uri", $lines );
		$this->assertContains( "http://purl.org/dc/terms/references,$entity_2_uri", $lines );
		$this->assertContains( "http://www.w3.org/1999/02/22-rdf-syntax-ns#type,http://schema.org/WebPage", $lines, "The following lines do not match: \n" . implode( "\n", $lines ) );

		self::turn_off_entity_push();
	}

	/**
	 * Test that entity doesn't refer other entities.
	 *
	 * @since 3.18.0
	 */
	function test_that_entity_doesnt_have_references() {

		self::turn_on_entity_push();

		// Create required posts/entities.
		$entity_1_id = $this->entity_factory->create( array(
			'post_status' => 'publish',
			'post_title'  => 'Test References test_that_entity_doesnt_have_references 1',
		) );
		$entity_2_id = $this->entity_factory->create( array(
			'post_status' => 'publish',
			'post_title'  => 'Test References test_that_entity_doesnt_have_references 2',
		) );
		$entity_3_id = $this->entity_factory->create( array(
			'post_status' => 'publish',
			'post_title'  => 'Test References test_that_entity_doesnt_have_references 3',
		) );

		// Add relations.
		wl_core_add_relation_instance( $entity_1_id, WL_WHAT_RELATION, $entity_2_id );
		wl_core_add_relation_instance( $entity_1_id, WL_WHAT_RELATION, $entity_3_id );

		// Push the property.
		$this->linked_data_service->push( $entity_1_id );

		// Get the URIs.
		$entity_1_uri = Wordlift_Sparql_Service::escape_uri( $this->entity_service->get_uri( $entity_1_id ) );
		$entity_2_uri = Wordlift_Sparql_Service::escape_uri( $this->entity_service->get_uri( $entity_2_id ) );
		$entity_3_uri = Wordlift_Sparql_Service::escape_uri( $this->entity_service->get_uri( $entity_3_id ) );

		// Prepare the SPARQL query to select label and URL.
		$sparql = "SELECT * WHERE { <$entity_1_uri> ?p ?o }";

		// Send the query and get the response.
		$response = rl_sparql_select( $sparql );
		$body     = $response['body'];
		$lines    = str_getcsv( $body, "\n" );

		$this->assertNotContains( "http://purl.org/dc/terms/references,$entity_2_uri", $lines );
		$this->assertNotContains( "http://purl.org/dc/terms/references,$entity_3_uri", $lines );
		$this->assertNotContains( "http://www.w3.org/1999/02/22-rdf-syntax-ns#type,http://schema.org/WebPage", $lines );

		$this->assertContains( "http://purl.org/dc/terms/relation,$entity_2_uri", $lines );
		$this->assertContains( "http://purl.org/dc/terms/relation,$entity_3_uri", $lines );
		$this->assertContains( "http://www.w3.org/1999/02/22-rdf-syntax-ns#type,http://schema.org/Thing", $lines );

		self::turn_off_entity_push();

	}

}
