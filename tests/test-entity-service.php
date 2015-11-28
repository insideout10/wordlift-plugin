<?php
require_once( 'functions.php' );

/**
 * Test the {@link Wordlift_Entity_Service}.
 *
 * @since 3.2.0
 */
class EntityServiceTest extends WP_UnitTestCase {

	/**
	 * The Log service.
	 *
	 * @since 3.2.0
	 * @access private
	 * @var \Wordlift_Log_Service $log_service The Log service.
	 */
	private $log_service;

	/**
	 * Set up the test.
	 */
	function setUp() {
		parent::setUp();

		$this->log_service = Wordlift_Log_Service::get_logger( 'EntityServiceTest' );

		wl_configure_wordpress_test();
		wl_empty_blog();

	}

	/**
	 * Test the {@link get_alternative_labels} function by creating an entity and checking that the number of alternative
	 * labels matches the one we set via {@link save_post}.
	 *
	 * @since 3.2.0
	 */
	function test_get_alternative_labels() {

		$entity_service = Wordlift_Entity_Service::get_instance();

		// Create a test entity.
		$entity_id = wl_create_post( 'This is Entity 1', 'entity-1', 'Entity 1', 'publish', 'entity' );
		wl_set_entity_main_type( $entity_id, 'http://schema.org/Thing' );

		// Check that we have no alternative labels.
		$this->assertCount( 0, $entity_service->get_alternative_labels( $entity_id ) );

		// Call save_post to set the alternative labels, mock the request first.
		$_REQUEST['wl_alternative_label'] = array( 'ABC 1', 'ABD 2', 'EFG 3' );
		$entity_service->save_post( $entity_id, null, null );

		// Check that we have 3 alternative labels.
		$this->assertCount( 3, $entity_service->get_alternative_labels( $entity_id ) );
		$this->assertCount( 2, wl_entity_get_by_title( 'AB', true ) );

		// Call save_post to set the alternative labels, mock the request first.
		$_REQUEST['wl_alternative_label'] = array( 'ABC 1', 'ABD 2' );
		$entity_service->save_post( $entity_id, null, null );

		// Check that we have 2 alternative labels.
		$this->assertCount( 2, $entity_service->get_alternative_labels( $entity_id ) );
		$this->assertCount( 2, wl_entity_get_by_title( 'AB', true ) );

		// Call save_post to set the alternative labels, mock the request first.
		$_REQUEST['wl_alternative_label'] = array();
		$entity_service->save_post( $entity_id, null, null );

		// Check that we have no alternative labels.
		$this->assertCount( 0, $entity_service->get_alternative_labels( $entity_id ) );
		$this->assertCount( 0, wl_entity_get_by_title( 'AB' ) );

	}

}
