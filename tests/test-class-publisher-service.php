<?php
/**
 * Tests: Admin Input Element Test.
 *
 * Test the {@link Wordlift_Publisher_Service} class.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the {@link Wordlift_Publisher_Service_Test} test class.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */
class Wordlift_Publisher_Service_Test extends Wordlift_Unit_Test_Case {

	private $entity_type_service;

	private $entity_service;

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();

		// We don't need to check the remote Linked Data store.
		Wordlift_Unit_Test_Case::turn_off_entity_push();;

		$wordlift = new Wordlift_Test();

		$this->entity_type_service             = $wordlift->get_entity_type_service();
		$this->entity_service                  = $wordlift->get_entity_service();
	}

	/**
	 * Test results when there are no publishers at all
	 *
	 * @since 3.11.0
	 */
	function test_nopublishers() {

		$publishers_service = new Wordlift_Publisher_Service();

		// with nothing in the DB
		$this->assertEquals( 0 , $publishers_service->count() );
		$this->assertEquals( 0 , count( $publishers_service->query() ) );
		$this->assertEquals( 0 , count( $publishers_service->query( 'bla' ) ) );

		// now with some unrelated entity

		$pid = $this->factory->post->create( array(
			'post_title' => 'bla',
			'post_type'  => 'entity',
			'post_status' => 'publish',
		) );

		$this->entity_type_service->set( $pid, 'http://schema.org/LocalBusiness' );

		$this->assertEquals( 0 , $publishers_service->count() );
		$this->assertEquals( 0 , count( $publishers_service->query() ) );
		$this->assertEquals( 0 , count( $publishers_service->query( 'bla' ) ) );
	}

	/**
	 * Test results when there are some publishers
	 *
	 * @since 3.11.0
	 */
	function test_publishers() {

		$publishers_service = new Wordlift_Publisher_Service();

		// create a non publisher entity to make the test more real
		$pid = $this->factory->post->create( array(
			'post_title' => 'bla',
			'post_type'  => 'entity',
			'post_status' => 'publish',
		) );

		$this->entity_type_service->set( $pid, 'http://schema.org/LocalBusiness' );

		// create a person
		$pid = $this->factory->post->create( array(
			'post_title' => 'blabla',
			'post_type'  => 'entity',
			'post_status' => 'publish',
		) );

		$this->entity_type_service->set( $pid, 'http://schema.org/Person' );

		$this->assertEquals( 1 , $publishers_service->count() );
		$this->assertEquals( 1 , count( $publishers_service->query() ) );
		$this->assertEquals( 1 , count( $publishers_service->query( '' ) ) );

		// create an organization
		$pid = $this->factory->post->create( array(
			'post_title' => 'alb',
			'post_type'  => 'entity',
			'post_status' => 'publish',
		) );

		$this->entity_type_service->set( $pid, 'http://schema.org/Organization' );

		$this->assertEquals( 2 , $publishers_service->count() );
		$this->assertEquals( 2 , count( $publishers_service->query() ) );
		$this->assertEquals( 2 , count( $publishers_service->query( '' ) ) );

		// test the search
		$this->assertEquals( 1 , count( $publishers_service->query( 'bla' ) ) );
	}

}
