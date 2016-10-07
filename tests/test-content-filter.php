<?php

/**
 * This file covers tests related to the microdata printing routines.
 */

require_once 'functions.php';

class ContentFilterTest extends WP_UnitTestCase {

	/**
	 * Set up the test.
	 */
	function setUp() {
		parent::setUp();

		wl_configure_wordpress_test();
		wl_empty_blog();
		rl_empty_dataset();
	}

	// Test <span> markup is cleaned out when referring to a non-existent entity
	function testMicrodataCompilingForANonExistentEntity() {

		// Create a fake and randomic annotation id
		$annotation_id = uniqid('urn:');
		// Create content for a post referencing a non existent entity
		$content = <<<EOF
                Let's talk about <span id="$annotation_id" class="textannotation disambiguated" itemid="http://nonExistent.yeah">Watzlawick</span>
EOF;

		// Create a post with above content
		$post_id = wl_create_post( $content, 'post', 'A post', 'publish', 'post' );

		// Verify the span tag does not appear on the frontend, but content is preserved.
		$compiled_markup = _wl_content_embed_microdata( $post_id, $content );
		$this->assertEquals(
			$this->prepareMarkup( "Let's talk about Watzlawick" ),
			$this->prepareMarkup( $compiled_markup )
		);
	}

	// Test <span> markup is cleaned out when referring to a non-published entity
	function testMicrodataCompilingForANonPublishedEntity() {

		$entity_id  = wl_create_post( 'A trashed entity about Paul Watzlawick', 'watzlawick', 'Paul Watzlawick', 'draft', 'entity' );
		$entity_uri = wl_get_entity_uri( $entity_id );

		// Create a fake and randomic annotation id
		$annotation_id = uniqid('urn:');
		// Create content for a post referencing the entity
		$content = <<<EOF
                Let's talk about <span id="$annotation_id" class="textannotation disambiguated" itemid="$entity_uri">Watzlawick</span>
EOF;

		// Create a post with above content
		$post_id = wl_create_post( $content, 'post', 'A post', 'publish', 'post' );

		// Verify the span tag does not appear on the frontend
		$compiled_markup = _wl_content_embed_microdata( $post_id, $content );
		$this->assertEquals(
			$this->prepareMarkup( "Let's talk about Watzlawick" ),
			$this->prepareMarkup( $compiled_markup )
		);
	}

	// Test if the microdata compiling does not fail on an entity with an undefined schema.org type
	function testMicrodataCompilingForAnEntityWithUndefinedType() {
		// Create an entity without defining the schema.org type. WordLift will assume it is a Thing
		$entity_id  = wl_create_post( 'Just a place', 'my-place', 'MyPlace', 'publish', 'entity' );
		$entity_uri = wl_get_entity_uri( $entity_id );
		
		// Create a fake and randomic annotation id
		$annotation_id = uniqid('urn:');
		$content    = <<<EOF
    <span id="$annotation_id" class="textannotation disambiguated" itemid="$entity_uri">MyPlace</span>
EOF;
		// Create a post referincing to the created entity
		$post_id = wl_create_post( $content, 'my-post', 'A post' );
		// Compile markup for the given content
		$compiled_markup = _wl_content_embed_microdata( $post_id, $content );
		// Expected markup
		$expected_markup = file_get_contents( dirname( __FILE__ ) .
		                                      '/assets/microdata_compiling_for_an_entity_with_undefined_type.txt' );

		// Verify correct markup
		$this->assertEquals(
			$this->prepareMarkup( $expected_markup ),
			$this->prepareMarkup( $compiled_markup )
		);
	}

	// Test if the microdata compiling does not fail on an entity with defined type and undefined custom fields
	function testMicrodataCompilingForAnEntityWithDefinedTypeAndUndefinedCustomFields() {

		// Create an entity without defining the schema.org type properly
		$entity_id = wl_create_post( 'Just a place', 'my-place', 'MyPlace', 'publish', 'entity' );
		wl_set_entity_main_type( $entity_id, 'http://schema.org/Place' );
		$entity_uri = wl_get_entity_uri( $entity_id );

		// Create a fake and randomic annotation id
		$annotation_id = uniqid('urn:');
		$content    = <<<EOF
    <span id="$annotation_id" class="textannotation disambiguated" itemid="$entity_uri">MyPlace</span>
EOF;
		// Create a post referencing to the created entity
		$post_id = wl_create_post( $content, 'my-post', 'A post' );

		// Compile markup for the given content
		$compiled_markup = _wl_content_embed_microdata( $post_id, $content );
		// Expected markup
		$expected_markup = file_get_contents( dirname( __FILE__ ) .
		                                      '/assets/microdata_compiling_for_an_entity_with_defined_type_and_undefined_custom_fields.txt' );

		// Verify correct markup
		$this->assertEquals(
			$this->prepareMarkup( $expected_markup ),
			$this->prepareMarkup( $compiled_markup )
		);
	}

	// Test if the microdata compiling does not fail on an entity with an unexpected custom field
	function testMicrodataCompilingForAnEntityWithUnexpectedCustomField() {
		
		// Create an entity without defining the schema.org type properly
		$entity_id = wl_create_post( 'Just a place', 'my-place', 'MyPlace', 'publish', 'entity' );
		wl_set_entity_main_type( $entity_id, 'http://schema.org/Place' );
		// The field 'foo' is not included in the 'Place' type definition
		add_post_meta( $entity_id, "foo", "bar", true );
		$entity_uri = wl_get_entity_uri( $entity_id );
		
		// Create a fake and randomic annotation id
		$annotation_id = uniqid( 'urn:' );
		$content    = <<<EOF
    <span id="$annotation_id" class="textannotation disambiguated" itemid="$entity_uri">MyPlace</span>
EOF;
		// Create a post referincing to the created entity
		$post_id = wl_create_post( $content, 'my-post', 'A post' );

		// Compile markup for the given content
		$compiled_markup = _wl_content_embed_microdata( $post_id, $content );
		// Expected markup
		$expected_markup = file_get_contents( dirname( __FILE__ ) .
		                                      '/assets/microdata_compiling_for_an_entity_with_unexpected_custom_fields.txt' );

		// Verify correct markup
		$this->assertEquals(
			$this->prepareMarkup( $expected_markup ),
			$this->prepareMarkup( $compiled_markup )
		);
	}

	// Test microdata compiling on a well-formed entity without a nested entity
	function testMicrodataCompilingProperlyForAnEntityWithoutNestedEntities() {
		// Create an entity without defining the schema.org type properly
		$entity_id = wl_create_post( 'Just a place', 'my-place', 'MyPlace', 'publish', 'entity' );
		wl_set_entity_main_type( $entity_id, 'http://schema.org/Place' );

		// Trying out both the schema API and the classic WP method
		add_post_meta( $entity_id, Wordlift_Schema_Service::FIELD_GEO_LATITUDE, 40.12, true );
		wl_schema_set_value( $entity_id, 'longitude', 72.3 );
		wl_schema_set_value( $entity_id, 'streetAddress', 'via del ciuccio 23' );

		$entity_uri = wl_get_entity_uri( $entity_id );
		// Create a fake and randomic annotation id
		$annotation_id = uniqid( 'urn:' );
		$content    = <<<EOF
    <span id="$annotation_id" class="textannotation disambiguated" itemid="$entity_uri">MyPlace</span>
EOF;
		// Create a post referencing to the created entity
		$post_id = wl_create_post( $content, 'my-post', 'A post' );

		// Compile markup for the given content
		$compiled_markup = _wl_content_embed_microdata( $post_id, $content );
		// Expected markup
		$expected_markup = file_get_contents( dirname( __FILE__ ) .
		                                      '/assets/microdata_compiling_for_an_entity_without_nested_entities.txt' );

		// Verify correct markup
		$this->assertEquals(
			$this->prepareMarkup( $expected_markup ),
			$this->prepareMarkup( $compiled_markup )
		);
	}

	// Check if nested entities microdata compiling works on nested entities
	function testMicrodataCompilingProperlyForAnEntityWithNestedEntities() {

		// A place
		$place_id = wl_create_post( 'Just a place', 'my-place', 'MyPlace', 'publish', 'entity' );
		wl_set_entity_main_type( $place_id, 'http://schema.org/Place' );

		// Trying out both the schema API and the classic WP method
		wl_schema_set_value( $place_id, 'latitude', 40.12 );
		add_post_meta( $place_id, Wordlift_Schema_Service::FIELD_GEO_LONGITUDE, 72.3, true );

		// An Event having as location the place above
		$event_id = wl_create_post( 'Just an event', 'my-event', 'MyEvent', 'publish', 'entity' );
		wl_set_entity_main_type( $event_id, Wordlift_Schema_Service::SCHEMA_EVENT_TYPE );

		// Trying out both the schema API and the classic WP method
		add_post_meta( $event_id, Wordlift_Schema_Service::FIELD_DATE_START, '2014-10-21', true );
		wl_schema_set_value( $event_id, 'endDate', '2015-10-26' );

		wl_schema_set_value( $event_id, 'sameAs', 'http://dbpedia.org/resource/my-event' );
		wl_schema_add_value( $event_id, 'sameAs', 'http://rdf.freebase.com/my-event' );

		// Create an annotated post containing the entities
		$entity_uri = wl_get_entity_uri( $event_id );
		// Create a fake and randomic annotation id
		$annotation_id = uniqid( 'urn:' );
		$content    = <<<EOF
    <span id="$annotation_id" class="textannotation disambiguated" itemid="$entity_uri">MyEvent</span>
EOF;
		$post_id    = wl_create_post( $content, 'post', 'A post' );

		// Case 1 - Nested entity is referenced trough the wordpress entity ID
		add_post_meta( $event_id, Wordlift_Schema_Service::FIELD_LOCATION, $place_id, true );

		// Set the recursion limit.
		$this->setRecursionDepthLimit( 1 );

		// Compile markup for the given content
		$compiled_markup = _wl_content_embed_microdata( $post_id, $content );
		$expected_markup = file_get_contents( dirname( __FILE__ ) .
		                                      '/assets/microdata_compiling_for_an_entity_with_nested_entities.txt' );

		// Verify correct markup
		$this->assertEquals(
			$this->prepareMarkup( $expected_markup ),
			$this->prepareMarkup( $compiled_markup ),
			"Error on comparing markup when the entity type is not defined"
		);

		delete_post_meta( $event_id, Wordlift_Schema_Service::FIELD_LOCATION );
		// Check if meta were deleted properly
		$this->assertEquals( array(), get_post_meta( $event_id, Wordlift_Schema_Service::FIELD_LOCATION ) );
		// Case 2 - Nested entity is referenced trough the an uri
		add_post_meta( $event_id, Wordlift_Schema_Service::FIELD_LOCATION, wl_get_entity_uri( $place_id ), true );

		$expected_markup = file_get_contents( dirname( __FILE__ ) .
		                                      '/assets/microdata_compiling_for_an_entity_with_nested_entities.txt' );

		// Verify correct markup
		$this->assertEquals(
			$this->prepareMarkup( $expected_markup ),
			$this->prepareMarkup( $compiled_markup )
		);

	}

	// Check if nested entities microdata compiling works on nested entities
	function testMicrodataCompilingForAnEntityWithNestedBrokenEntities() {

		// An Event having as location the place above
		$event_id = wl_create_post( 'Just an event', 'my-event', 'MyEvent', 'publish', 'entity' );
		wl_set_entity_main_type( $event_id, Wordlift_Schema_Service::SCHEMA_EVENT_TYPE );
		add_post_meta( $event_id, Wordlift_Schema_Service::FIELD_DATE_START, '2014-10-21', true );
		add_post_meta( $event_id, Wordlift_Schema_Service::FIELD_DATE_END, '2015-10-26', true );
		// Set a fake uri ad entity reference
		add_post_meta( $event_id, Wordlift_Schema_Service::FIELD_LOCATION, 'http://my.fake.uri/broken/entity/linking', true );

		// Create an annotated post containing the entities
		$entity_uri = wl_get_entity_uri( $event_id );
		// Create a fake and randomic annotation id
		$annotation_id = uniqid( 'urn:' );
		$content    = <<<EOF
    <span id="$annotation_id" class="textannotation disambiguated" itemid="$entity_uri">MyEvent</span>
EOF;
		$post_id    = wl_create_post( $content, 'post', 'A post' );

		// Compile markup for the given content
		$compiled_markup = _wl_content_embed_microdata( $post_id, $content );
		$expected_markup = file_get_contents( dirname( __FILE__ ) .
		                                      '/assets/microdata_compiling_bad_referenced_entities.txt' );

		// Verify correct markup
		$this->assertEquals(
			$this->prepareMarkup( $expected_markup ),
			$this->prepareMarkup( $compiled_markup )
		);

	}

	// Check recursivity limitation feature
	function testMicrodataCompilingRecursivityLimitation() {

		// A place
		$place_id = wl_create_post( 'Just a place', 'my-place', 'MyPlace', 'publish', 'entity' );
		wl_set_entity_main_type( $place_id, 'http://schema.org/Place' );

		// Trying out both the schema API and the classic WP method
		wl_schema_set_value( $place_id, 'latitude', 40.12 );
		add_post_meta( $place_id, Wordlift_Schema_Service::FIELD_GEO_LONGITUDE, 72.3, true );

		// An Event having as location the place above
		$event_id = wl_create_post( 'Just an event', 'my-event', 'MyEvent', 'publish', 'entity' );
		wl_set_entity_main_type( $event_id, Wordlift_Schema_Service::SCHEMA_EVENT_TYPE );
		add_post_meta( $event_id, Wordlift_Schema_Service::FIELD_DATE_START, '2014-10-21', true );
		add_post_meta( $event_id, Wordlift_Schema_Service::FIELD_DATE_END, '2015-10-26', true );

		wl_schema_set_value( $event_id, 'sameAs', array(
			'http://rdf.freebase.com/my-event',
			'http://dbpedia.org/resource/my-event'
		) );
		//wl_schema_set_value($event_id, 'sameAs', 'http://dbpedia.org/resource/my-event');

		add_post_meta( $event_id, Wordlift_Schema_Service::FIELD_LOCATION, $place_id, true );

		// Create an annotated post containing the entities
		$entity_uri = wl_get_entity_uri( $event_id );
		// Create a fake and randomic annotation id
		$annotation_id = uniqid( 'urn:' );
		$content    = <<<EOF
    <span id="$annotation_id" class="textannotation disambiguated" itemid="$entity_uri">MyEvent</span>
EOF;
		$post_id    = wl_create_post( $content, 'post', 'A post' );

		// Set to 0 the recursivity limitation on entity metadata compiling
		$this->setRecursionDepthLimit( 0 );

		// Compile markup for the given content
		$compiled_markup = _wl_content_embed_microdata( $post_id, $content );
		$expected_markup = file_get_contents( dirname( __FILE__ ) .
		                                      '/assets/microdata_compiling_recursivity_limitation.txt' );

		// Verify correct markup
		$this->assertEquals(
			$this->prepareMarkup( $expected_markup ),
			$this->prepareMarkup( $compiled_markup )
		);

		$this->setRecursionDepthLimit( 1 );

		// Compile markup for the given content
		$compiled_markup = _wl_content_embed_microdata( $post_id, $content );
		$expected_markup = file_get_contents( dirname( __FILE__ ) .
		                                      '/assets/microdata_compiling_for_an_entity_with_nested_entities.txt' );

		// Verify correct markup
		$this->assertEquals(
			$this->prepareMarkup( $expected_markup ),
			$this->prepareMarkup( $compiled_markup )
		);
	}

	// If the content filter is applied to an entity, the entity's microdata must be printed too.
	function testMicrodataCompilingForAnEntityPage() {

		// A place
		$place_id = wl_create_post( '', 'my-place', 'MyPlace', 'publish', 'entity' );
		wl_set_entity_main_type( $place_id, 'http://schema.org/Place' );
		wl_schema_set_value( $place_id, 'latitude', 40.12 );
		wl_schema_set_value( $place_id, 'longitude', 72.3 );
		wl_schema_set_value( $place_id, 'streetAddress', 'via del ciuccio 23' );

		// Compile markup for the given content
		$compiled_markup = _wl_content_embed_microdata( $place_id, '' );
		$expected_markup = file_get_contents( dirname( __FILE__ ) .
		                                      '/assets/microdata_compiling_entity_page.txt' );

		// Verify correct markup
		$this->assertEquals(
			$this->prepareMarkup( $expected_markup ),
			$this->prepareMarkup( $compiled_markup )
		);
	}

	function setRecursionDepthLimit( $value ) {
		// Set the default as index.
		$options                                                          = get_option( WL_OPTIONS_NAME );
		$options[ WL_CONFIG_RECURSION_DEPTH_ON_ENTITY_METADATA_PRINTING ] = $value;
		update_option( WL_OPTIONS_NAME, $options );
	}

	function prepareMarkup( $markup ) {
		$markup = preg_replace( '/\s+/', '', $markup );
		$markup = preg_replace(
			'/{{REDLINK_ENDPOINT}}/',
			wl_configuration_get_redlink_dataset_uri(),
			$markup );

		return $markup;
	}


}

