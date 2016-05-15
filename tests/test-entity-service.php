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

	/**
	 * Test the {@link get_entity_post_by_uri} function 
	 * using external uris set as same as for an internal entity
	 *
	 * @since 3.3.2
	 */
	function test_get_entity_post_by_uri_with_external_uris() {

		$entity_service = Wordlift_Entity_Service::get_instance();

		$entity_1_id = wl_create_post( '', 'entity-1', uniqid( 'entity', true ), 'draft', 'entity' );
		// Retrieve the new entity uri
		$entity_1_uri = wl_get_entity_uri( $entity_1_id );
		// Check the is an internal uri
		$this->assertTrue( $entity_service->is_internal_uri( $entity_1_uri ) );
		
		// Look for an antity with that uri
		$retrieved_entity = $entity_service->get_entity_post_by_uri( $entity_1_uri );
		// Check returned entity is not null
		$this->assertNotNull( $retrieved_entity );
		// Check returned entity is the same we expect
		$this->assertEquals( $entity_1_id, $retrieved_entity->ID );

		// Set an external uri as same as
		$external_uri = 'http://dbpedia.org/resource/berlin';
		// Check the is NOT an internal uri
		$this->assertFalse( $entity_service->is_internal_uri( $external_uri ) );
		// Set this external uri as sameAs of the created entity
		wl_schema_set_value( $entity_1_id, 'sameAs', $external_uri );
		
		// Look for an antity with that uri
		$retrieved_entity = $entity_service->get_entity_post_by_uri( $external_uri );
		// Check returned entity is not null
		$this->assertNotNull( $retrieved_entity );
		// Check returned entity is the same we expect
		$this->assertEquals( $entity_1_id, $retrieved_entity->ID );

	}

	/**
	 * Test the {@link get_entity_post_by_uri} function 
	 * using external uris set as same as for an internal entity
	 * See https://github.com/insideout10/wordlift-plugin/issues/237
	 *
	 * @since 3.3.2
	 */
	function test_get_entity_post_by_uri_with_cross_referenced_internal_entities() {

		$entity_service = Wordlift_Entity_Service::get_instance();

		// Create the first entity
		$entity_1_id = wl_create_post( '', 'entity-1', uniqid( 'entity', true ), 'draft', 'entity' );
		$entity_1_uri = wl_get_entity_uri( $entity_1_id );
		// Create the second entity
		$entity_2_id = wl_create_post( '', 'entity-2', uniqid( 'entity', true ), 'draft', 'entity' );
		$entity_2_uri = wl_get_entity_uri( $entity_2_id );
		// Reference the first entity as sameAs for the second one
		wl_schema_set_value( $entity_2_id, 'sameAs', $entity_1_uri );
		
		// Look for the first antity 
		$retrieved_entity = $entity_service->get_entity_post_by_uri( $entity_1_uri );
		// Check returned entity is not null
		$this->assertNotNull( $retrieved_entity );
		// Check returned entity is the same we expect
		$this->assertEquals( $entity_1_id, $retrieved_entity->ID );

	}

	/**
	 * Test the {@link is_used} function 
	 *
	 * @since 3.4.0
	 */
	function test_entity_usage_on_related_entities() {

		$entity_service = Wordlift_Entity_Service::get_instance();
		// Create the first entity
		$entity_1_id = wl_create_post( '', 'entity-1', uniqid( 'entity', true ), 'draft', 'entity' );
		// It should be not used now
		$this->assertFalse( $entity_service->is_used( $entity_1_id ) );
		// Create the first entity
		$entity_2_id = wl_create_post( '', 'entity-2', uniqid( 'entity', true ), 'draft', 'entity' );
		// Create a relation instance between these 2 entities
		wl_core_add_relation_instance( $entity_2_id, WL_WHAT_RELATION, $entity_1_id );
		// It should be not used now
		$this->assertTrue( $entity_service->is_used( $entity_1_id ) );
            
	}

	/**
	 * Test the {@link is_used} function 
	 *
	 * @since 3.4.0
	 */
	function test_entity_usage_on_entities_used_as_meta_value() {

		$entity_service = Wordlift_Entity_Service::get_instance();
		// Create the first entity
		$entity_1_id = wl_create_post( '', 'entity-1', uniqid( 'entity', true ), 'draft', 'entity' );
		// It should be not used now
		$this->assertFalse( $entity_service->is_used( $entity_1_id ) );
		// Create the first entity
		$entity_2_id = wl_create_post( '', 'entity-2', uniqid( 'entity', true ), 'draft', 'entity' );
		// Set the current entity as same as for another entity
		wl_schema_set_value( $entity_2_id, 'sameAs', wl_get_entity_uri( $entity_1_id ) );
		// It should be used now
		$this->assertTrue( $entity_service->is_used( $entity_1_id ) );
            
	}

	/**
	 * Test the {@link is_used} function 
	 *
	 * @since 3.4.0
	 */
	function test_entity_usage_on_referenced_entities() {

		$entity_service = Wordlift_Entity_Service::get_instance();
		// Create the first entity
		$entity_1_id = wl_create_post( '', 'entity-1', uniqid( 'entity', true ), 'draft', 'entity' );
		// It should be not used now
		$this->assertFalse( $entity_service->is_used( $entity_1_id ) );
		// Create the first entity
		$post_id = wl_create_post( '', 'post-1', uniqid( 'post', true ), 'draft', 'post' );
		// Create a relation instance between these 2 entities
		wl_core_add_relation_instance( $post_id, WL_WHAT_RELATION, $entity_1_id );
		// It should be used now
		$this->assertTrue( $entity_service->is_used( $entity_1_id ) );
            
	}

	/**
	 * Test the {@link is_used} function 
	 *
	 * @since 3.4.0
	 */
	function test_entity_usage_on_a_standard_post() {

		$entity_service = Wordlift_Entity_Service::get_instance();
		// Create the first entity
		$post_id = wl_create_post( '', 'post-1', uniqid( 'post', true ), 'draft', 'post' );
		$this->assertNull( $entity_service->is_used( $post_id ) );
            
	}

	/**
	 * Test the {@link build_uri} function 
	 *
	 * @since 3.5.0
	 */
	function test_build_uri_when_there_is_no_entity_with_the_same_label() {

		$entity_service = Wordlift_Entity_Service::get_instance();

		$entity_name = uniqid( 'entity', true );
		
		$new_entity_uri = sprintf( '%s/%s/%s', 
			wl_configuration_get_redlink_dataset_uri(), 
			Wordlift_Entity_Service::TYPE_NAME, 
			wl_sanitize_uri_path( $entity_name )
		); 

		$this->assertEquals( 
			$new_entity_uri, 
			$entity_service->build_uri( $entity_name, Wordlift_Entity_Service::TYPE_NAME ) 
		);            
	}

	/**
	 * Test the {@link build_uri} function 
	 *
	 * @since 3.5.0
	 */
	function test_build_uri_when_there_is_already_an_entity_with_the_same_label() {

		$entity_service = Wordlift_Entity_Service::get_instance();

		$entity_name = uniqid( 'entity', true );
		
		$new_entity_uri = sprintf( '%s/%s/%s', 
			wl_configuration_get_redlink_dataset_uri(), 
			Wordlift_Entity_Service::TYPE_NAME, 
			wl_sanitize_uri_path( $entity_name )
		); 

		// Create the first entity
		$entity_id = wl_create_post( '', 'entity-1', $entity_name, 'draft', 'entity' );
		// Check the new entity uri
		$this->assertEquals( $new_entity_uri, wl_get_entity_uri( $entity_id ) );
		// Check that the uri for a new entity contains a numeric suffix
		$this->assertEquals( 
			$new_entity_uri . '_2', 
			$entity_service->build_uri( $entity_name, Wordlift_Entity_Service::TYPE_NAME ) 
		);
		      
	}

	/**
	 * Test the {@link build_uri} function 
	 *
	 * @since 3.5.0
	 */
	function test_build_uri_override_when_there_is_already_an_entity_with_the_same_label_and_type() {

		$entity_service = Wordlift_Entity_Service::get_instance();

		$entity_name = uniqid( 'entity', true );
		
		$new_entity_uri = sprintf( '%s/%s/%s', 
			wl_configuration_get_redlink_dataset_uri(), 
			Wordlift_Entity_Service::TYPE_NAME, 
			wl_sanitize_uri_path( $entity_name )
		); 

		// Create the first entity
		$entity_id = wl_create_post( '', 'entity-1', $entity_name, 'draft', 'entity' );
		$schema_type   = wl_entity_type_taxonomy_get_type( $entity_id );

		$this->assertEquals( $schema_type[ 'css_class' ], 'wl-thing' );
		
		// Check the new entity uri
		$this->assertEquals( $new_entity_uri, wl_get_entity_uri( $entity_id ) );
		// Check that the uri for a new entity contains a numeric suffix
		$this->assertEquals( 
			$new_entity_uri, 
			$entity_service->build_uri( $entity_name, Wordlift_Entity_Service::TYPE_NAME, 'wl-thing' ) 
		);
		      
	}

	/**
	 * Test the {@link get_classification_scope_for} function 
	 *
	 * @since 3.5.0
	 */
	function test_get_classification_scope_for() {

		$entity_service = Wordlift_Entity_Service::get_instance();

		$post_id = wl_create_post( '', 'post-1', uniqid( 'post', true ), 'draft', 'post' );
		$this->assertNull( $entity_service->get_classification_scope_for( 'post_id' ) );
		$entity_id = wl_create_post( '', 'entity-1', uniqid( 'entity', true ), 'draft', 'entity' );
		$this->assertEquals( 'what', $entity_service->get_classification_scope_for( $entity_id ) );
		wl_set_entity_main_type( $entity_id, 'http://schema.org/Thing' );
        $this->assertEquals( 'what', $entity_service->get_classification_scope_for( $entity_id ) );
		wl_set_entity_main_type( $entity_id, 'http://schema.org/CreativeWork' );
        $this->assertEquals( 'what', $entity_service->get_classification_scope_for( $entity_id ) );
		wl_set_entity_main_type( $entity_id, 'http://schema.org/Place' );
        $this->assertEquals( 'where', $entity_service->get_classification_scope_for( $entity_id ) );
		wl_set_entity_main_type( $entity_id, 'http://schema.org/Event' );
        $this->assertEquals( 'when', $entity_service->get_classification_scope_for( $entity_id ) );
		wl_set_entity_main_type( $entity_id, 'http://schema.org/Person' );
        $this->assertEquals( 'who', $entity_service->get_classification_scope_for( $entity_id ) );
		wl_set_entity_main_type( $entity_id, 'http://schema.org/Organization' );
        $this->assertEquals( 'who', $entity_service->get_classification_scope_for( $entity_id ) );
		wl_set_entity_main_type( $entity_id, 'http://schema.org/LocalBusiness' );
        $this->assertEquals( 'who', $entity_service->get_classification_scope_for( $entity_id ) );
			
	}
}
