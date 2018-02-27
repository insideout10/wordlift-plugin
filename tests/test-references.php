<?php
/**
 * Tests: Test References.
 *
 * @since   3.18.0
 * @package Wordlift
 */
class Wordlift_References_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test that non entity refer entities.
	 */
	function test_that_non_entity_has_references() {

		self::turn_on_entity_push();

		// Create required posts/entities.
		$entity_1_id = $this->entity_factory->create( array( 'post_status' => 'publish' ) );
		$entity_2_id = $this->entity_factory->create( array( 'post_status' => 'publish' ) );
		$property_id = $this->factory->post->create( array(
			'post_type'   => 'property',
			'post_status' => 'publish',
		) );

		// Add relations.
		wl_core_add_relation_instance( $property_id, WL_WHAT_RELATION, $entity_1_id );
		wl_core_add_relation_instance( $property_id, WL_WHAT_RELATION, $entity_2_id );

		// Push the property.
		Wordlift_Linked_Data_Service::get_instance()->push( $property_id );

		// Get the URIs.
		$entity_1_uri = Wordlift_Sparql_Service::escape( wl_get_entity_uri( $entity_1_id ) );
		$entity_2_uri = Wordlift_Sparql_Service::escape( wl_get_entity_uri( $entity_2_id ) );
		$property_uri = Wordlift_Sparql_Service::escape( wl_get_entity_uri( $property_id ) );

		// Prepare the SPARQL query.
		$sparql = "SELECT * WHERE { <$property_uri> ?p ?o }";

		// Send the query and get the response.
		$response = rl_sparql_select( $sparql );
		$body     = $response['body'];
		$lines    = str_getcsv( $body, "\n" );

		$this->assertContains( "http://purl.org/dc/terms/references,$entity_1_uri" , $lines );
		$this->assertContains( "http://purl.org/dc/terms/references,$entity_2_uri" , $lines );
		$this->assertContains( "http://www.w3.org/1999/02/22-rdf-syntax-ns#type,http://schema.org/WebPage" , $lines );

		self::turn_off_entity_push();
	}

	/**
	 * Test that entity doens't refer other entities.
	 */
	function test_that_entity_doesnt_have_references() {

		self::turn_on_entity_push();
		
		// Create required posts/entities.
		$entity_1_id = $this->entity_factory->create( array( 'post_status' => 'publish' ) );
		$entity_2_id = $this->entity_factory->create( array( 'post_status' => 'publish' ) );
		$entity_3_id = $this->entity_factory->create( array( 'post_status' => 'publish' ) );

		// Add relations.
		wl_core_add_relation_instance( $entity_1_id, WL_WHAT_RELATION, $entity_2_id );
		wl_core_add_relation_instance( $entity_1_id, WL_WHAT_RELATION, $entity_3_id );

		// Push the property.
		Wordlift_Linked_Data_Service::get_instance()->push( $entity_1_id );

		// Get the URIs.
		$entity_1_uri = Wordlift_Sparql_Service::escape( wl_get_entity_uri( $entity_1_id ) );
		$entity_2_uri = Wordlift_Sparql_Service::escape( wl_get_entity_uri( $entity_2_id ) );
		$entity_3_uri = Wordlift_Sparql_Service::escape( wl_get_entity_uri( $entity_3_id ) );

		// Prepare the SPARQL query to select label and URL.
		$sparql = "SELECT * WHERE { <$entity_1_uri> ?p ?o }";

		// Send the query and get the response.
		$response = rl_sparql_select( $sparql );
		$body     = $response['body'];
		$lines    = str_getcsv( $body, "\n" );

		$this->assertNotContains( "http://purl.org/dc/terms/references,$entity_2_uri" , $lines );
		$this->assertNotContains( "http://purl.org/dc/terms/references,$entity_3_uri" , $lines );
		$this->assertNotContains( "http://www.w3.org/1999/02/22-rdf-syntax-ns#type,http://schema.org/WebPage" , $lines );

		$this->assertContains( "http://purl.org/dc/terms/relation,$entity_2_uri" , $lines );
		$this->assertContains( "http://purl.org/dc/terms/relation,$entity_3_uri" , $lines );
		$this->assertContains( "http://www.w3.org/1999/02/22-rdf-syntax-ns#type,http://schema.org/Thing" , $lines );

		self::turn_off_entity_push();
	}

}
