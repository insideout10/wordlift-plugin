<?php
/**
 * Tests: Address Sparql Tuple Rendition Test.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the {@link Wordlift_Default_Sparql_Tuple_Rendition_Test} class.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */
class Wordlift_Default_Sparql_Tuple_Rendition_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Sparql_Tuple_Rendition_Factory} instance.
	 *
	 * @since  3.18.0
	 * @access protected
	 * @var \Wordlift_Sparql_Tuple_Rendition_Factory $rendition_factory The {@link Wordlift_Sparql_Tuple_Rendition_Factory} instance.
	 */
	protected $rendition_factory;

	/**
	 * The {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.18.0
	 * @access protected
	 * @var \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} instance.
	 */
	protected $entity_service;

	/**
	 * The {@link Wordlift_Storage_Factory} instance.
	 *
	 * @since  3.18.0
	 * @access protected
	 * @var \Wordlift_Storage_Factory $storage_factory The {@link Wordlift_Storage_Factory} instance.
	 */
	protected $storage_factory;

	/**
	 * @inheritdoc
	 */
	function setUp() {
		parent::setUp();

		$this->rendition_factory = $this->get_wordlift_test()->get_rendition_factory();
		$this->entity_service    = $this->get_wordlift_test()->get_entity_service();
		$this->storage_factory   = $this->get_wordlift_test()->get_storage_factory();
	}

	/**
	 * Test `get_delete_triples` & `get_insert_triples` methods for legalName.
	 *
	 * @since 3.18.0
	 */
	public function test_legal_name_delete_insert_renditions() {
		$legal_name = $this->rendition_factory->create(
			$this->storage_factory->post_meta( Wordlift_Schema_Service::FIELD_LEGAL_NAME ),
			'http://schema.org/legalName'
		);

		// Create an entity.
		$entity_id = $this->factory->post->create( array(
			'post_type'   => 'entity',
			'post_status' => 'publish',
			'post_title'  => 'Test Sparql Tuple Rendition test_legal_name_delete_insert_renditions',
		) );

		// Get the entity uri.
		$uri = $this->entity_service->get_uri( $entity_id );

		// Add legalName post meta.
		update_post_meta( $entity_id, Wordlift_Schema_Service::FIELD_LEGAL_NAME, 'Lorem Ipsum' );

		// Get delete triples
		$delete_triples = $legal_name->get_delete_triples( $entity_id );

		// Test the delete triples.
		$this->assertContains( "<$uri> <http://schema.org/legalName> ?o", $delete_triples );
		$this->assertContains( "?s <http://schema.org/legalName> <$uri>", $delete_triples );

		// Test the insert trimples.
		$insert_triples = $legal_name->get_insert_triples( $entity_id );
		$this->assertContains( "<$uri> <http://schema.org/legalName> \"Lorem Ipsum\" . ", $insert_triples );
	}

	/**
	 * Test `get_delete_triples` & `get_insert_triples` methods for totalTime.
	 *
	 * @since 3.18.0
	 */
	public function test_total_time_delete_insert_renditions() {
		$total_time = $this->rendition_factory->create(
			$this->storage_factory->post_meta( Wordlift_Schema_Service::FIELD_TOTAL_TIME ),
			'http://schema.org/totalTime',
			Wordlift_Schema_Service::DATA_TYPE_DURATION
		);

		// Create an entity.
		$entity_id = $this->factory->post->create( array(
			'post_type'   => 'entity',
			'post_status' => 'publish',
			'post_title'  => 'Test Sparql Tuple Rendition test_total_time_delete_insert_renditions',
		) );

		// Get the entity uri.
		$uri = $this->entity_service->get_uri( $entity_id );

		// Add totalTime post meta.
		update_post_meta( $entity_id, Wordlift_Schema_Service::FIELD_TOTAL_TIME, '13:00' );

		// Get delete triples
		$delete_triples = $total_time->get_delete_triples( $entity_id );
		$insert_triples = $total_time->get_insert_triples( $entity_id );

		$this->assertContains( "<$uri> <http://schema.org/totalTime> ?o", $delete_triples );
		$this->assertContains( "?s <http://schema.org/totalTime> <$uri>", $delete_triples );
		$this->assertContains( "<$uri> <http://schema.org/totalTime> \"PT13H0M\"^^xsd:duration . ", $insert_triples );
	}

}
