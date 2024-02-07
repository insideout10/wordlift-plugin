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
 * @group entity
 */
class Wordlift_Publisher_Service_Test extends Wordlift_Unit_Test_Case {

	private $entity_service;

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();

		// We don't need to check the remote Linked Data store.
		Wordlift_Unit_Test_Case::turn_off_entity_push();;

		$this->entity_service = Wordlift_Entity_Service::get_instance();
	}

	/**
	 * Test results when there are no publishers at all
	 *
	 * @since 3.11.0
	 */
	function test_nopublishers() {

		$publishers_service = Wordlift_Publisher_Service::get_instance();

		// with nothing in the DB
		$this->assertEquals( 0, $publishers_service->count() );
		$this->assertEquals( 0, count( $publishers_service->query() ) );
		$this->assertEquals( 0, count( $publishers_service->query( 'bla' ) ) );

		// now with some unrelated entity

		$pid = $this->factory()->post->create( array(
			'post_title'  => 'Test Publisher Service test_nopublishers',
			'post_type'   => 'entity',
			'post_status' => 'publish',
		) );

		Wordlift_Entity_Type_Service::get_instance()->set( $pid, 'http://schema.org/EducationalOrganization' );

		$this->assertEquals( 0, $publishers_service->count() );
		$this->assertEquals( 0, count( $publishers_service->query() ) );
		$this->assertEquals( 0, count( $publishers_service->query( 'bla' ) ) );
	}

	/**
	 * Test results when there are some publishers
	 *
	 * @since 3.11.0
	 */
	function test_publishers() {

		$publishers_service = Wordlift_Publisher_Service::get_instance();

		// create a non publisher entity to make the test more real
		$busines = $this->factory()->post->create( array(
			'post_title'  => 'Test_Publisher_Service test_publishers 1',
			'post_type'   => 'entity',
			'post_status' => 'publish',
		) );

		Wordlift_Entity_Type_Service::get_instance()->set( $busines, 'http://schema.org/EducationalOrganization' );

		// create a person
		$person = $this->factory()->post->create( array(
			'post_title'  => 'Test_Publisher_ServiceTest_Publisher_Service test_publishers 2',
			'post_type'   => 'entity',
			'post_status' => 'publish',
		) );

		Wordlift_Entity_Type_Service::get_instance()->set( $person, 'http://schema.org/Person' );

		$this->assertEquals( 1, $publishers_service->count() );
		$this->assertEquals( 1, count( $publishers_service->query() ) );
		$this->assertEquals( 1, count( $publishers_service->query( '' ) ) );

		// create an organization
		$org = $this->factory()->post->create( array(
			// Without underscores, so that we shouldn't be found with the query.
			'post_title'  => 'Test Publisher Service test_publishers 3',
			'post_type'   => 'entity',
			'post_status' => 'publish',
		) );

		Wordlift_Entity_Type_Service::get_instance()->set( $org, 'http://schema.org/Organization' );

		$this->assertEquals( 2, $publishers_service->count() );
		$this->assertEquals( 2, count( $publishers_service->query() ) );
		$this->assertEquals( 2, count( $publishers_service->query( '' ) ) );

		// test the search
		$this->assertEquals( 1, count( $publishers_service->query( 'Test_Publisher_Service' ) ) );

		// Test that posts with the relevant entity type are also returned

		// random post article
		$blapost = $this->factory()->post->create( array(
			'post_title'  => 'Test_Publisher_ServiceTest_Publisher_Service test_publishers 4',
			'post_type'   => 'post',
			'post_status' => 'publish',
		) );
		update_post_meta( $blapost, '_thumbnail_id', 1 );

		$this->assertEquals( 2, $publishers_service->count() );
		$this->assertEquals( 2, count( $publishers_service->query() ) );
		$this->assertEquals( 2, count( $publishers_service->query( '' ) ) );

		// test the search
		$this->assertEquals( 1, count( $publishers_service->query( 'Test_Publisher_Service' ) ) );

		// create a post person
		$postperson = $this->factory()->post->create( array(
			'post_title'  => 'oTest_Publisher_Serviceo test_publishers 5',
			'post_type'   => 'post',
			'post_status' => 'publish',
		) );

		Wordlift_Entity_Type_Service::get_instance()->set( $postperson, 'http://schema.org/Person' );

		// create a post organization
		$postorg = $this->factory()->post->create( array(
			'post_title'  => 'Test_Publisher_ServiceTest_Publisher_Service test_publishers 6',
			'post_type'   => 'post',
			'post_status' => 'publish',
		) );

		Wordlift_Entity_Type_Service::get_instance()->set( $postorg, 'http://schema.org/Organization' );

		$this->assertEquals( 4, $publishers_service->count() );
		$this->assertEquals( 4, count( $publishers_service->query() ) );
		$this->assertEquals( 4, count( $publishers_service->query( '' ) ) );

		// test the search
		$this->assertEquals( 3, count( $publishers_service->query( 'Test_Publisher_Service' ) ) );
	}

}
