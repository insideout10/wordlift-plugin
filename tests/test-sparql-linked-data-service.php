<?php
/**
 * Tests: Linked Data Service
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 * @group sparql
 */

class Wordlift_Linked_Data_Service_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Sparql_Service} instance to test.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Sparql_Service $sparql_service The {@link Wordlift_Sparql_Service} instance to test.
	 */
	private $sparql_service;

	/**
	 * A {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var Wordlift_Entity_Service $entity_service A {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	function setUp() {
		parent::setUp();

		$this->entity_service = $this->get_wordlift_test()->get_entity_service();
		// Create a mock sparql service.
		$this->sparql_service = $this->getMockBuilder( 'Wordlift_Sparql_Service' )
		                             ->disableOriginalConstructor()
		                             ->setMethods( array( 'execute' ) )
		                             ->getMock();

	}

	/**
	 * Check that queries buffering is disabled by default.
	 *
	 * @since 3.23.2
	 */
	function test_push() {

		$this->assertFalse( wl_is_sparql_update_queries_buffering_enabled() );

	}

	function test_remove() {

		$post_id = $this->factory->post->create( array( 'post_type' => 'post' ) );

		// Create the linked data service instance to be tested along with the
		// sparql service mock to check that the `execute` function is called
		// on the sparql service.
		$linked_data_service = new Wordlift_Linked_Data_Service(
			$this->get_wordlift_test()->get_entity_service(),
			$this->entity_type_service,
			$this->get_wordlift_test()->get_schema_service(),
			$this->sparql_service );

		$uri = $this->entity_service->get_uri( $post_id );

		// Load the expected function parameter for the sparql service `execute`
		// function. Beware that the results may change in the future if we
		// add new predicates to the schema service or renditions.
		$expected = str_replace( 'POST_URI', $uri, file_get_contents( __DIR__ . '/assets/linked_data_service__remove__1.sparql' ) );

		// Declare our expectation for the `execute` function to be called once
		// with the above parameter.
		$this->sparql_service->expects( $this->once() )
		                     ->method( 'execute' )
		                     ->with( $this->equalTo( $expected ) );

		// Call the `remove` function to test it.
		$linked_data_service->remove( $post_id );

	}

}