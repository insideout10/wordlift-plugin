<?php
/**
 * Tests: Entity Service Test.
 *
 * Test the {@link Wordlift_Entity_Service} class.
 *
 * @since 3.2.0
 *
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

require_once( dirname( __FILE__ ) . '/../src/includes/schemaorg/class-wordlift-schemaorg-sync-batch-operation.php' );

/**
 * Define the Wordlift_Entity_Service_Test class.
 *
 * @since 3.2.0
 * @group entity
 */
class Wordlift_Entity_Service_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Entity_Service} being tested.
	 *
	 * @since  3.7.2
	 * @access private
	 * @var \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} being tested.
	 */
	private $entity_service;

	/**
	 * The {@link Wordlift_Entity_Uri_Service} instance.
	 *
	 * @since  3.16.3
	 * @access private
	 * @var \Wordlift_Entity_Uri_Service $entity_uri_service The {@link Wordlift_Entity_Uri_Service} instance.
	 */
	private $entity_uri_service;

	/**
	 * Set up the test.
	 */
	function setUp() {
		parent::setUp();

		$this->entity_service     = Wordlift_Entity_Service::get_instance();
		$this->entity_uri_service = Wordlift_Entity_Uri_Service::get_instance();

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

		// Set the request post id to simulate a call from the edit post UI.
		$_REQUEST['post_ID'] = (int) $entity_id;

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
	 * Check that the entity alternative label is not set when the entity post id
	 * is not set in the request.
	 *
	 * @see   https://github.com/insideout10/wordlift-plugin/issues/363
	 *
	 * @since 3.6.1
	 */
	function test_set_alternative_labels_post_id_not_set() {

		$entity_service = Wordlift_Entity_Service::get_instance();

		// Another entity
		$entity_id = wl_create_post( 'This is Entity 1', 'entity-1', 'Entity 1', 'publish', 'entity' );
		wl_set_entity_main_type( $entity_id, 'http://schema.org/Thing' );

		// Call save_post to set the alternative labels, mock the request first.
		$_REQUEST['wl_alternative_label'] = array( 'ABC 1', 'ABD 2' );
		$entity_service->save_post( $entity_id, null, null );

		// Check that we have no alternative labels.
		$this->assertCount( 0, $entity_service->get_alternative_labels( $entity_id ) );
		$this->assertCount( 0, wl_entity_get_by_title( 'AB', true ) );

	}

	/**
	 * Test the {@link get_entity_post_by_uri} function
	 * using external uris set as same as for an internal entity
	 *
	 * @since 3.3.2
	 */
	function test_get_entity_post_by_uri_with_external_uris() {

		\Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$entity_service = Wordlift_Entity_Service::get_instance();

		$entity_1_id = wl_create_post( '', 'entity-1', uniqid( 'entity', true ), 'draft', 'entity' );
		// Retrieve the new entity uri
		$entity_1_uri = wl_get_entity_uri( $entity_1_id );
		// Check the is an internal uri
		$this->assertTrue( $this->entity_uri_service->is_internal( $entity_1_uri ) );

		// Look for an antity with that uri
		$retrieved_entity = $entity_service->get_entity_post_by_uri( $entity_1_uri );
		// Check returned entity is not null
		$this->assertNotNull( $retrieved_entity );
		// Check returned entity is the same we expect
		$this->assertEquals( $entity_1_id, $retrieved_entity->ID );

		// Set an external uri as same as
		$external_uri = 'http://dbpedia.org/resource/berlin';
		// Check the is NOT an internal uri
		$this->assertFalse( $this->entity_uri_service->is_internal( $external_uri ) );
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

		\Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$entity_service = Wordlift_Entity_Service::get_instance();

		// Create the first entity
		$entity_1_id  = wl_create_post( '', 'entity-1', uniqid( 'entity', true ), 'draft', 'entity' );
		$entity_1_uri = wl_get_entity_uri( $entity_1_id );
		// Create the second entity
		$entity_2_id  = wl_create_post( '', 'entity-2', uniqid( 'entity', true ), 'draft', 'entity' );
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

		\Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

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

		\Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

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

		\Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

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

		\Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

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

		\Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$entity_service = Wordlift_Entity_Service::get_instance();

		$entity_name = uniqid( 'entity', true );

		$new_entity_uri = sprintf( '%s/%s/%s',
			untrailingslashit( wl_configuration_get_redlink_dataset_uri() ),
			Wordlift_Entity_Service::TYPE_NAME,
			wl_sanitize_uri_path( $entity_name )
		);

		$this->assertEquals(
			$new_entity_uri,
			Wordlift_Uri_Service::get_instance()->build_uri( $entity_name, Wordlift_Entity_Service::TYPE_NAME )
		);
	}

	/**
	 * Test the {@link build_uri} function
	 *
	 * @since 3.5.0
	 */
	function test_build_uri_when_there_is_already_an_entity_with_the_same_label() {

		\Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$entity_name = uniqid( 'entity', true );

		$new_entity_uri = 'http://data.example.org/data/entity/entity-1';

		// Create the first entity
		$entity_id = wl_create_post( '', 'entity-1', $entity_name, 'draft', 'entity' );

		// Check the new entity uri
		$this->assertEquals( $new_entity_uri, wl_get_entity_uri( $entity_id ) );

	}

	/**
	 * Test the {@link build_uri} function
	 *
	 * @since 3.5.0
	 */
	function test_build_uri_override_when_there_is_already_an_entity_with_the_same_label_and_type() {

		\Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$entity_service = Wordlift_Entity_Service::get_instance();

		$entity_name = uniqid( 'entity', true );

		$new_entity_uri = 'http://data.example.org/data/entity/entity-1';

		// Create the first entity
		$entity_id   = wl_create_post( '', 'entity-1', $entity_name, 'draft', 'entity' );
		$schema_type = Wordlift_Entity_Type_Service::get_instance()->get( $entity_id );

		$this->assertEquals( $schema_type['css_class'], 'wl-thing' );

		// Check the new entity uri
		$this->assertEquals( $new_entity_uri, wl_get_entity_uri( $entity_id ) );

	}

	/**
	 * Test the {@link get_classification_scope_for} function
	 *
	 * @since 3.5.0
	 */
	function test_get_classification_scope_for() {

		\Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$entity_service = Wordlift_Entity_Service::get_instance();

		$post_id = wl_create_post( '', 'post-1', uniqid( 'post', true ), 'draft', 'post' );
		$this->assertNull( $entity_service->get_classification_scope_for( 'post_id', null ) );
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

	/**
	 * Test URIs build out of post titles containing UTF-8 characters.
	 *
	 * @see   https://github.com/insideout10/wordlift-plugin/issues/386
	 *
	 * @since 3.7.2
	 */
	function test_utf8_post_titles() {

		// The following title has a UTF-8 character right after the 's'.
		$title = 'Mozarts﻿ Geburtshaus';

		// Check that the encoding is recognized as UTF-8.
		if ( function_exists( 'mb_detect_encoding' ) ) {
			$this->assertEquals( 'UTF-8', mb_detect_encoding( $title ) );
		}

		// Build the URI.
		$uri = Wordlift_Uri_Service::get_instance()->build_uri( $title, 'entity' );

		// Check that the URI is good.
		$this->assertStringEndsWith( '/entity/mozarts__geburtshaus', $uri );

	}

	/**
	 * A post is considered an `entity` if it's not of type `article`. We might actually remove this
	 * concept in the near future.
	 *
	 * Presently because of #835 (All Entity Types) it is enough that the `entity` has one type different
	 * from `article` to be considered an entity.
	 *
	 * @see https://github.com/insideout10/wordlift-plugin/issues/835
	 *
	 * @since 3.20.0
	 */
	function test_is_entity_835() {

		\Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		// No entity type, expect default of `Thing`.
		$post_1      = $this->factory()->post->create( array(
			'post_type' => 'entity',
		) );
		$is_entity_1 = $this->entity_service->is_entity( $post_1 );

		$this->assertTrue( $is_entity_1, 'Expect an entity post w/o entity type terms to be considered an `entity`.' );

		$post_2 = $this->factory()->post->create( array(
			'post_type' => 'entity',
		) );
		wp_set_object_terms( $post_2, 'organization', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
		$is_entity_2 = $this->entity_service->is_entity( $post_2 );

		$this->assertTrue( $is_entity_2, 'Expect an entity post w/ entity type terms different from `article` to be considered an `entity`.' );

		$post_3 = $this->factory()->post->create( array(
			'post_type' => 'entity',
		) );
		wp_set_object_terms( $post_3, array(
			'article',
			'organization',
		), Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
		$is_entity_3 = $this->entity_service->is_entity( $post_3 );

		$this->assertTrue( $is_entity_3, 'Expect an entity post w/ at least one entity type term different from `article` to be considered an `entity`.' );

		$post_4 = $this->factory()->post->create( array(
			'post_type' => 'entity',
		) );
		wp_set_object_terms( $post_4, array(
			'article',
		), Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
		$is_entity_4 = $this->entity_service->is_entity( $post_4 );

		$this->assertFalse( $is_entity_4, 'Expect an entity post w/ one entity type term equal to `article` not to be considered an `entity`.' );

	}

	public function test_996() {

		// This test applies only to legacy URLs.
		if ( apply_filters( 'wl_feature__enable__rel-item-id', false ) ) {
			$this->markTestSkipped( 'This test should be revised based on the new Content_Service.' );
		}

		$post_id = $this->factory()->post->create( array(
			'post_title' => 'Test 996'
		) );

		delete_post_meta( $post_id, WL_ENTITY_URL_META_NAME );

		$uri_1 = $this->entity_service->get_uri( $post_id );

		$this->assertEquals( 'https://data.localdomain.localhost/dataset/post/test-996', $uri_1, "$uri_1 doesn't match expected value." );

		update_post_meta( $post_id, WL_ENTITY_URL_META_NAME, '/test-post' );

		$uri_2 = $this->entity_service->get_uri( $post_id );

		$this->assertEquals( 'https://data.localdomain.localhost/dataset/post/test-996', $uri_2, "$uri_2 doesn't match expected value." );

	}

}
