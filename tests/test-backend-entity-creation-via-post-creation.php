<?php

/**
 * This file covers tests related entity creation via post creation.
 * @group backend
 */
class EntityCreationViaPostCreationTest extends Wordlift_Unit_Test_Case {

	/**
	 * Set up the test.
	 */
	function setUp() {
		parent::setUp();
		add_filter( 'pre_http_request', array( $this, '_mock_api' ), 10, 3 );
	}

	function tearDown() {
		remove_filter( 'pre_http_request', array( $this, '_mock_api' ) );

		parent::tearDown();
	}


	function _mock_api( $value, $request, $url ) {
		$method = $request['method'];
		if ( 'GET' === $method && 'http://upload.wikimedia.org/wikipedia/commons/a/a2/Goya_Caprichos3.jpg' === $url ) {
			return array(
				'response' => array( 'code' => 200 ),
				'body'     => '@@mock_image@@'
			);
		}

		return $value;
	}

	// This test simulate the standard workflow from disambiguation widget:
	// A create a post having in $_POST one external entity related as 'what'
	// Please notice here the entity is properly referenced by post content
	function testEntityIsCreatedAndLinkedToThePost() {

		Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$entity_service = Wordlift_Entity_Service::get_instance();

		$fake  = $this->prepare_fake_global_post_array_from_file(
			'/assets/fake_global_post_array_with_one_entity_linked_as_what.json'
		);
		$_POST = $fake;

		// Retrieve the entity uri (the first key in wl_entities associative aray)
		$original_entity_uri = current( array_keys( $fake['wl_entities'] ) );

		// Reference the entity to the post content
		$content = <<<EOF
    <span class="textannotation disambiguated" itemid="$original_entity_uri">My entity</span>
EOF;
		// Be sure that the entity does not exist yet.
		$entity = $entity_service->get_entity_post_by_uri( $original_entity_uri );
		$this->assertNull( $entity );

		// Create a post referencing to the created entity.
		$post_id = wl_create_post( $content, 'my-post', 'A post', 'draft' );

		// Here the entity should be created instead.
		$entity = $entity_service->get_entity_post_by_uri( $original_entity_uri );
		$this->assertNotNull( $entity, "Can't find an entity by URI $original_entity_uri [ post_id :: $post_id ]." );

		// Here the original uri should be properly as same_as
		$same_as = wl_schema_get_value( $entity->ID, 'sameAs' );
		$this->assertContains( $original_entity_uri, $same_as );
		// The entity url should be the same we expect
		$raw_entity          = current( array_values( $fake['wl_entities'] ) );
		$expected_entity_uri = $this->buildEntityUriForLabel( $raw_entity['label'] );
		$entity_uri          = wl_get_entity_uri( $entity->ID );
		$this->assertEquals( $entity_uri, $expected_entity_uri );

		// And it should be related to the post as what predicate
		$related_entity_ids = wl_core_get_related_entity_ids( $post_id, array( "predicate" => "what" ) );
		$this->assertCount( 1, $related_entity_ids );

		$this->assertContains( $entity->ID, $related_entity_ids );
		// Ensure there are no other relation instances
		$relation_instances = wl_tests_get_relation_instances_for( $post_id );
		$this->assertCount( 1, $relation_instances );

	}

	function testEntityWithEscapedCharsInUriIsCreatedAndLinkedToThePost2() {

		// Create a post referincing to the created entity
		$existing_entity_id = wl_create_post( '', 'gran-sasso', 'Gran Sasso', 'draft', 'entity' );

		wl_set_entity_main_type( $existing_entity_id, 'wl-place' );

		$fake  = $this->prepare_fake_global_post_array_from_file(
			'/assets/fake_global_post_array_with_gran_sasso_linked_as_where.json'
		);
		$_POST = $fake;
		// Retrieve the entity uri (the first key in wl_entities associative aray)
		$original_entity_uri = current( array_keys( $fake['wl_entities'] ) );
		// Reference the entity to the post content

		$content = <<<EOF
    <span class="textannotation disambiguated" itemid="$original_entity_uri">Gran Sasso</span>
EOF;

		// Create a post referencing to the created entity
		$post_id = wl_create_post( $content, 'my-post', 'A post', 'draft' );
		// Here the entity should be created instead



		$entity = Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( $original_entity_uri );

		// And it should be related to the post as where predicate
		$related_entity_ids = wl_core_get_related_entity_ids( $post_id, array( "predicate" => "where" ) );
		$this->assertCount( 1, $related_entity_ids );

		$this->assertNotContains( $existing_entity_id, $related_entity_ids );
		$this->assertContains( $entity->ID, $related_entity_ids );
		// Ensure there are no other relation instances
		$relation_instances = wl_tests_get_relation_instances_for( $post_id );
		$this->assertCount( 1, $relation_instances );

	}

	function testEntityWithEscapedCharsInUriIsCreatedAndLinkedToThePost() {

		$fake  = $this->prepare_fake_global_post_array_from_file(
			'/assets/fake_global_post_array_with_gran_sasso_linked_as_where.json'
		);
		$_POST = $fake;
		// Retrieve the entity uri (the first key in wl_entities associative aray)
		$original_entity_uri = current( array_keys( $fake['wl_entities'] ) );
		// Reference the entity to the post content
		$content = <<<EOF
    <span class="textannotation disambiguated" itemid="$original_entity_uri">Gran Sasso</span>
EOF;
		// Create a post referincing to the created entity
		$post_id = wl_create_post( $content, 'my-post', 'A post', 'draft' );
		// Here the entity should be created instead

		$entity = Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( $original_entity_uri );
		$this->assertNotNull( $entity );
		// Here the original uri should be properly as same_as
		$same_as = wl_schema_get_value( $entity->ID, 'sameAs' );
		$this->assertContains( $original_entity_uri, $same_as );

		// And it should be related to the post as where predicate
		$related_entity_ids = wl_core_get_related_entity_ids( $post_id, array( "predicate" => "where" ) );
		$this->assertCount( 1, $related_entity_ids );
		$this->assertContains( $entity->ID, $related_entity_ids );
		// Ensure there are no other relation instances
		$relation_instances = wl_tests_get_relation_instances_for( $post_id );
		$this->assertCount( 1, $relation_instances );

	}

	// In this case we are testing this workflow:
	// 1 entity with the same label and type of an existing one
	// is created through the disambiguation workflow
	function testEntitiesWithSameLabelAndTypeOverride() {

		Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		// Create a post referencing to the created entity
		$entity_id = wl_create_post( '', 'tex-willer', 'Tex Willer', 'draft', 'entity' );
		wl_set_entity_main_type( $entity_id, 'wl-person' );

		$terms = wp_get_post_terms( $entity_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
		$this->assertCount( 1, $terms );
		$this->assertEquals( 'person', $terms[0]->slug );

		$this->assertEquals( 'http://data.example.org/data/entity/tex-willer', wl_get_entity_uri( $entity_id ) );

		$fake  = $this->prepare_fake_global_post_array_from_file(
			'/assets/fake_global_post_array_with_tex_willer_as_who.json'
		);
		$_POST = $fake;

		$content = '<span class="textannotation disambiguated" itemid="local-entity-n3n5c5ql1yycik9zu55mq0miox0f6rgt">Tex Willer</span>';

		// Create a post referencing to the created entity
		$post_id = wl_create_post( $content, 'my-post', 'A post', 'draft' );
		wl_write_log( "+++ Post id $post_id" );

		$related_entity_ids = wl_core_get_related_entity_ids( $post_id, array( "predicate" => 'who', ) );
		$this->assertCount( 1
			, $related_entity_ids
			, '$related_entity_ids count doesn`t match: ' . var_export( $related_entity_ids, true ) );

		$relation_instances = wl_tests_get_relation_instances_for( $post_id );
		$this->assertCount( 1
			, $relation_instances
			, '$related_entity_ids count doesn`t match: ' . var_export( $related_entity_ids, true ) );

		$this->assertEquals( wl_get_entity_uri( $entity_id ), wl_get_entity_uri( $related_entity_ids[0] ) );

		// Check the already existing entity is linked
		$this->assertContains( $entity_id, $related_entity_ids );


	}

	// In this case we are testing this workflow:
	// 3 entities with the same label but different types are
	// created trough the disambiguation workflow
	// We expect they are properly created and linked to the post
	function testThreeEntitiesWithTheSameLabelsAreProperlyCreatedAndLinkedToThePost() {

		Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$fake  = $this->prepare_fake_global_post_array_from_file(
			'/assets/fake_global_post_array_with_two_entities_with_same_label_and_different_types.json'
		);
		$_POST = $fake;

		$content = <<<EOF
    <span class="textannotation disambiguated" itemid="local-entity-n3n5c5ql1yycik9zu55mq0miox0f6rgt">Ryan Carson</span>
    <span class="textannotation disambiguated" itemid="local-entity-ld7uu78v23z69a4iivmf1io4m2h5b3xr">Ryan Carson</span>
    <span class="textannotation disambiguated" itemid="http://dbpedia.org/resource/Ryan_Carson">Ryan Carson</span>
EOF;

		// Create a post referincing to the created entity
		$post_id = wl_create_post( $content, 'my-post', 'A post', 'draft' );

		$new_entity_uri = sprintf( '%s/%s/%s',
			untrailingslashit( wl_configuration_get_redlink_dataset_uri() ),
			Wordlift_Entity_Service::TYPE_NAME,
			sanitize_title_with_dashes( 'Ryan Carson' )
		);

		// And it should be related to the post as what predicate
		$related_entity_ids = wl_core_get_related_entity_ids( $post_id, array( "predicate" => "who" ) );
		$related_entity_ids = array_merge(
			$related_entity_ids,
			wl_core_get_related_entity_ids( $post_id, array( "predicate" => "what" ) )
		);

		$this->assertCount( 3, $related_entity_ids );
		// Ensure there are no other relation instances
		$relation_instances = wl_tests_get_relation_instances_for( $post_id );
		$this->assertCount( 3, $relation_instances );

		$entity_1 = Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri(
			$new_entity_uri
		);
		$this->assertNotNull( $entity_1 );
		$this->assertContains( $entity_1->ID, $related_entity_ids );

		$entity_2 = Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri(
			$new_entity_uri . '-2'
		);
		$this->assertNotNull( $entity_2 );
		$this->assertContains( $entity_2->ID, $related_entity_ids );

		$entity_3 = Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri(
			$new_entity_uri . '-3'
		);
		$this->assertNotNull( $entity_3 );
		$this->assertContains( $entity_3->ID, $related_entity_ids );

		$this->assertNotEquals( $entity_1->ID, $entity_2->ID );
		$this->assertNotEquals( $entity_2->ID, $entity_3->ID );
		$this->assertNotEquals( $entity_1->ID, $entity_3->ID );

	}

	// Same test of the previous one but with escaped chars in the entity label
	function testNewEntityWithEscapedCharsIsCreatedAndLinkedToThePost() {

		Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$fake  = $this->prepare_fake_global_post_array_from_file(
			'/assets/fake_global_post_array_with_a_new_entity_linked_with_escaped_chars.json'
		);
		$_POST = $fake;
		// Retrieve the entity uri (the first key in wl_entities associative aray)
		$entity_uri = current( array_keys( $fake['wl_entities'] ) );
		// Retrieve the label
		$raw_entity          = current( array_values( $fake['wl_entities'] ) );
		$expected_entity_uri = $this->buildEntityUriForLabel( $raw_entity['label'] );
		// Reference the entity to the post content
		$content = <<<EOF
    <span class="textannotation disambiguated" itemid="$entity_uri">My entity</span>
EOF;
		// Be sure that the entity does not exist yet
		$entity = Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( $expected_entity_uri );

		$this->assertNull( $entity );
		// Create a post referincing to the created entity
		$post_id = wl_create_post( $content, 'my-post', 'A post', 'draft' );
		// Here the entity should be created instead
		$entity = Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( $expected_entity_uri );
		$this->assertNotNull( $entity );

		// Check if the content was properly fixed
		$expected_content = <<<EOF
    <span class="textannotation disambiguated" itemid="$expected_entity_uri">My entity</span>
EOF;
		$post             = get_post( $post_id );
		$this->assertEquals( $post->post_content, $expected_content );
		// And it should be related to the post as what predicate
		$related_entity_ids = wl_core_get_related_entity_ids( $post_id, array( "predicate" => "who" ) );
		$this->assertCount( 1, $related_entity_ids );
		$this->assertContains( $entity->ID, $related_entity_ids );
		// Ensure there are no other relation instances
		$relation_instances = wl_tests_get_relation_instances_for( $post_id );
		$this->assertCount( 1, $relation_instances );

	}

	// Same test of the previous one but with utf8 chars in the entity label
	function testNewEntityWithUtf8CharsIsCreatedAndLinkedToThePost() {

		Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$fake  = $this->prepare_fake_global_post_array_from_file(
			'/assets/fake_global_post_array_with_a_new_entity_linked_with_utf8_chars.json'
		);
		$_POST = $fake;
		// Retrieve the entity uri (the first key in wl_entities associative aray)
		$entity_uri = current( array_keys( $fake['wl_entities'] ) );
		// Retrieve the label
		$expected_entity_uri = 'http://data.example.org/data/entity/loreal';
		// Reference the entity to the post content
		$content = <<<EOF
    <span class="textannotation disambiguated" itemid="$entity_uri">My entity</span>
EOF;
		// Be sure that the entity does not exist yet
		$entity = Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( $expected_entity_uri );

		$this->assertNull( $entity );

		// Create a post referincing to the created entity
		$post_id = wl_create_post( $content, 'my-post', 'A post', 'draft' );
		// Here the entity should be created instead
		$entity = Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( $expected_entity_uri );
		$this->assertNotNull( $entity );

		// Check if the content was properly fixed
		$expected_content = <<<EOF
    <span class="textannotation disambiguated" itemid="$expected_entity_uri">My entity</span>
EOF;
		$post             = get_post( $post_id );
		$this->assertEquals( $post->post_content, $expected_content );
		// And it should be related to the post as what predicate
		$related_entity_ids = wl_core_get_related_entity_ids( $post_id, array( "predicate" => "who" ) );
		$this->assertCount( 1, $related_entity_ids );
		$this->assertContains( $entity->ID, $related_entity_ids );
		// Ensure there are no other relation instances
		$relation_instances = wl_tests_get_relation_instances_for( $post_id );
		$this->assertCount( 1, $relation_instances );

	}

	// This test simulates the standard workflow from disambiguation widget:
	// Create a post having in $_POST a NEW entity related as 'who'
	// Please notice that new entities are a tmp uri with 'local-entity-' prefix
	// that needs to be processed before the save entity routine
	// Ea: local-entity-n3n5c5ql1yycik9zu55mq0miox0f6rgt
	function testNewEntityIsCreatedAndLinkedToThePost() {

		Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$fake  = $this->prepare_fake_global_post_array_from_file(
			'/assets/fake_global_post_array_with_a_new_entity_linked_as_who.json'
		);
		$_POST = $fake;
		// Retrieve the entity uri (the first key in wl_entities associative aray)
		$entity_uri = current( array_keys( $fake['wl_entities'] ) );
		// Retrieve the label
		$raw_entity          = current( array_values( $fake['wl_entities'] ) );
		$expected_entity_uri = $this->buildEntityUriForLabel( $raw_entity['label'] );
		// Reference the entity to the post content
		$content = <<<EOF
    <span class="textannotation disambiguated" itemid="$entity_uri">My entity</span>
EOF;
		// Be sure that the entity does not exist yet
		$entity = Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( $expected_entity_uri );
		$this->assertNull( $entity );
		// Create a post referincing to the created entity
		$post_id = wl_create_post( $content, 'my-post', 'A post', 'draft' );
		// Here the entity should be created instead
		$entity = Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( $expected_entity_uri );
		$this->assertNotNull( $entity );
		// Check if the content was properly fixed
		$expected_content = <<<EOF
    <span class="textannotation disambiguated" itemid="$expected_entity_uri">My entity</span>
EOF;
		$post             = get_post( $post_id );
		$this->assertEquals( $post->post_content, $expected_content );
		// And it should be related to the post as what predicate
		$related_entity_ids = wl_core_get_related_entity_ids( $post_id, array( "predicate" => "who" ) );
		$this->assertCount( 1, $related_entity_ids );
		$this->assertContains( $entity->ID, $related_entity_ids );
		// Ensure there are no other relation instances
		$relation_instances = wl_tests_get_relation_instances_for( $post_id );
		$this->assertCount( 1, $relation_instances );

	}
	// This test simulate the standard workflow from disambiguation widget:
	// A create a post having in $_POST one entity related as 'what' and 'who'
	// Please notice here the entity is properly referenced by post content
	function testEntityIsCreatedAndLinkedWithMultiplePredicatesToThePost() {

		$fake = $this->prepare_fake_global_post_array_from_file(
			'/assets/fake_global_post_array_with_one_entity_linked_as_what_and_who.json'
		);

		$_POST = $fake;
		// Retrieve the entity uri (the first key in wl_entities associative aray)
		$entity_uri = current( array_keys( $fake['wl_entities'] ) );
		// Reference the entity to the post content
		$content = <<<EOF
    <span class="textannotation disambiguated" itemid="$entity_uri">My entity</span>
EOF;
		// Be sure that the entity does not exist yet
		$entity = Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( $entity_uri );
		$this->assertNull( $entity );
		// Create a post referincing to the created entity
		$post_id = wl_create_post( $content, 'my-post', 'A post', 'draft' );
		// Here the entity should be created instead
		$entity = Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( $entity_uri );
		$this->assertNotNull( $entity );
		// And it should be related to the post as what predicate
		$related_entity_ids = wl_core_get_related_entity_ids( $post_id, array( "predicate" => "what" ) );
		$this->assertCount( 1, $related_entity_ids );
		$this->assertContains( $entity->ID, $related_entity_ids );
		// But it should NOT be related to the post as what predicate
		$related_entity_ids = wl_core_get_related_entity_ids( $post_id, array( "predicate" => "who" ) );
		$this->assertCount( 0, $related_entity_ids );
		// Ensure there are no other relation instances
		$relation_instances = wl_tests_get_relation_instances_for( $post_id );
		$this->assertCount( 1, $relation_instances );

	}

	// This test simulate the standard workflow from disambiguation widget:
	// A create a post having in $_POST one entity related as 'what'
	// Please notice here the entity is NOT properly referenced by post content
	function testEntityIsCreatedButNotLinkedToThePost() {

		$fake  = $this->prepare_fake_global_post_array_from_file(
			'/assets/fake_global_post_array_with_one_entity_linked_as_what.json'
		);
		$_POST = $fake;
		// Retrieve the entity uri (the first key in wl_entities associative aray)
		$entity_uri = current( array_keys( $fake['wl_entities'] ) );
		// Here I DON'T reference the entity to the post content
		$content = <<<EOF
    <span>My entity</span>
EOF;
		// Be sure that the entity does not exist yet
		$entity = Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( $entity_uri );
		$this->assertNull( $entity );
		// Create a post referencing to the created entity
		$post_id = wl_create_post( $content, 'my-post', 'A post', 'draft' );
		// Here the entity should be existing instead
		$entity = Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( $entity_uri );
		$this->assertNotNull( $entity );
		// And it should be related to the post as what predicate
		$related_entity_ids = wl_core_get_related_entity_ids( $post_id );
		$this->assertCount( 0, $related_entity_ids );

	}


	// If an entity with status 'public' is related to a post in draft
	// I expect that the 'public' status is properly preserved
	function testPublicEntityStatusIsPreservedWhenLinkedToDraftPost() {

		Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$fake  = $this->prepare_fake_global_post_array_from_file(
			'/assets/fake_global_post_array_with_one_entity_linked_as_what.json'
		);
		$_POST = $fake;
		// Retrieve the entity uri (the first key in wl_entities associative aray)
		$original_entity_uri = current( array_keys( $fake['wl_entities'] ) );
		// Reference the entity to the post content
		$content = <<<EOF
    <span class="textannotation disambiguated" itemid="$original_entity_uri">My entity</span>
EOF;
		// Be sure that the entity does not exist yet
		$entity = Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( $original_entity_uri );
		$this->assertNull( $entity );
		// Create a post referincing to the created entity
		$post_1_id = wl_create_post( $content, 'my-post', 'A post', 'draft' );
		// Here the entity should be created instead
		$entity = Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( $original_entity_uri );
		$this->assertNotNull( $entity );
		$this->assertEquals( 'draft', $entity->post_status );
		// Update post status and check if also entity status changed accrdingly
		wl_update_post_status( $post_1_id, 'publish' );
		$entity = Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( $original_entity_uri );
		$this->assertEquals( 'publish', $entity->post_status );
		// Retrieve the internal entity uri
		$entity_uri = wl_get_entity_uri( $entity->ID );
		// Build fake obj to simulate save same entity again on a new post
		$original_entity        = current( array_values( $fake['wl_entities'] ) );
		$original_entity['uri'] = $entity_uri;

		$fake_2 = array(
			'wl_entities' => array( $entity_uri => $original_entity ),
			'wl_boxes'    => array( 'what' => array( $entity_uri ) ),
		);
		$_POST  = $fake_2;

		// Reference the entity to the post content
		$content_2 = <<<EOF
    <span class="textannotation disambiguated" itemid="$entity_uri">My entity</span>
EOF;

		// Create a post referincing to the created entity
		$post_2_id       = wl_create_post( $content_2, 'my-post-2', 'Another post', 'draft' );
		$entity_reloaded = Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( $entity_uri );
		// Check is the same entity for WP
		$this->assertEquals( $entity->ID, $entity_reloaded->ID );
		// Here I expect entity status is still public
		$this->assertEquals( 'publish', $entity_reloaded->post_status );

	}

	// This test simulate entity type-specific properties (latitude, startDate, etc.) are saved trough the disambiguation widget
	function testEntityAdditionalPropertiesAreSaved() {

		$fake  = $this->prepare_fake_global_post_array_from_file(
			'/assets/fake_global_post_array_with_a_new_entity_linked_as_where_with_coordinates.json'
		);
		$_POST = $fake;

		// Retrieve the entity uri (the first key in wl_entities associative aray)
		$entity_uri = current( array_keys( $fake['wl_entities'] ) );
		// Retrieve the label and compose expected uri
		$raw_entity          = current( array_values( $fake['wl_entities'] ) );
		$expected_entity_uri = $this->buildEntityUriForLabel( $raw_entity['label'] );

		// Reference the entity to the post content
		$content = <<<EOF
    <span class="textannotation disambiguated" itemid="$entity_uri">My entity</span>
EOF;
		// Create a post referincing to the created entity
		$post_id = wl_create_post( $content, 'my-post', 'A post', 'draft' );

		// Here the entity should have been created
		$entity = Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( $expected_entity_uri );
		$this->assertNotNull( $entity );

		// Verify association to post as where
		$related_entity_ids = wl_core_get_related_entity_ids( $post_id, array( "predicate" => "where" ) );
		$this->assertEquals( array( $entity->ID ), $related_entity_ids );

		// Verify schema type
		$this->assertEquals( array( 'Place' ), Wordlift_Entity_Type_Service::get_instance()->get_names( $entity->ID ) );

		// Verify coordinates
		$this->assertEquals( array( 43.21 ), wl_schema_get_value( $entity->ID, 'latitude' ) );
		$this->assertEquals( array( 12.34 ), wl_schema_get_value( $entity->ID, 'longitude' ) );
	}

	// Given an entity with at least 1 alternative label
	// This test checks that the entity is properly updated keeping
	// WP and RL in synch when the this entity is used in disambiguation
	// See https://github.com/insideout10/wordlift-plugin/issues/221
	function testEntityWithAlternativeLabelIsProperlyOverridden() {

		$original_label = uniqid( 'entity-original', true );
		// Create an entity
		$entity_id = wl_create_post( '', 'entity-1', $original_label, 'draft', 'entity' );

		$expected_entity_uri = 'https://data.localdomain.localhost/dataset/entity/entity-1';
		$entity_uri          = wl_get_entity_uri( $entity_id );
		$this->assertEquals( $entity_uri, $expected_entity_uri );

		// Check that there are no related posts for the entity
		$related_post_ids = wl_core_get_related_post_ids( $entity_id, array( "predicate" => "what" ) );
		$this->assertCount( 0, $related_post_ids );
		// Generate e label and set it as alternative label for the new entity
		$alternative_label = "test synonym";
		Wordlift_Entity_Service::get_instance()->set_alternative_labels( $entity_id, $alternative_label );
		// Check that the alternative label is properly set
		$labels = Wordlift_Entity_Service::get_instance()->get_alternative_labels( $entity_id );
		$this->assertCount( 1, $labels );
		$this->assertContains( $alternative_label, $labels );
		// Force post status to publish: this triggers the save_post hook
		wl_update_post_status( $entity_id, 'publish' );
		// Check that entity label is properly mapped on entity post title
		$this->assertEquals( $original_label, get_post( $entity_id )->post_title );

		// Notice that the uri is generated trough the original label
		// while the current label is the alternative one
		$fake = $this->prepare_fake_global_post_array_from_file(
			'/assets/fake_global_post_array_with_one_existing_entity_linked_as_what.json',
			array(
				'CURRENT_URI'   => 'https://data.localdomain.localhost/dataset/entity/entity-1',
				'CURRENT_LABEL' => $alternative_label,
			)
		);

		$_POST = $fake;
		// Retrieve the entity uri (the first key in wl_entities associative aray)
		$original_entity_uri = current( array_keys( $fake['wl_entities'] ) );
		// Reference the entity to the post content trough its alternative label
		$content = <<<EOF
    <span class="textannotation disambiguated" itemid="$original_entity_uri">$alternative_label</span>
EOF;
		// Create a post referencing to the created entity
		$post_id = wl_create_post( $content, 'my-post', 'A post', 'draft' );
		// Check that entity label is STILL properly mapped on entity post title
		$this->assertEquals( $original_label, get_post( $entity_id )->post_title );

		// And it should be related to the post as what predicate
		$related_entity_ids = wl_core_get_related_entity_ids( $post_id, array( "predicate" => "what" ) );
		$this->assertCount( 1, $related_entity_ids );
		// @todo re-look into this failing asset
		// $this->assertContains( $entity_id, $related_entity_ids );

	}

	function prepare_fake_global_post_array_from_file( $fileName, $placeholders = array() ) {
		$json_data = file_get_contents( dirname( __FILE__ ) . $fileName );
		$json_data = preg_replace(
			'/{{REDLINK_ENDPOINT}}/',
			wl_configuration_get_redlink_dataset_uri(),
			$json_data
		);

		foreach ( $placeholders as $ph => $value ) {
			$json_data = preg_replace(
				sprintf( '/{{%s}}/', $ph ), $value, $json_data
			);
		}

		$data = json_decode( $json_data, true );

		return $data;

	}

	function buildEntityUriForLabel( $label ) {
		return sprintf( '%s/%s/%s',
			untrailingslashit( wl_configuration_get_redlink_dataset_uri() ),
			'entity', sanitize_title_with_dashes( $label ) );
	}

	function getThumbs( $post_id ) {

		$attatchments      = get_attached_media( 'image', $post_id );
		$attatchments_uris = array();
		foreach ( $attatchments as $attch ) {
			$attatchments_uris[] = wp_get_attachment_url( $attch->ID );
		}

		return $attatchments_uris;
	}

}
