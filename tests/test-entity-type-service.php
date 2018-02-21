<?php

/**
 */
class Wordlift_Entity_Type_Service_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Check that the specified slug is used.
	 */
	public function test_vocabulary_slug() {

		$entity_type_service = new Wordlift_Entity_Post_Type_Service( 'entity', 'vocabulary' );

		$this->assertEquals( 'vocabulary', $entity_type_service->get_slug() );

	}

	/**
	 * Check that, when an empty slug is used, the default entity post type name is used.
	 */
	public function test_empty_slug() {

		$entity_type_service = new Wordlift_Entity_Post_Type_Service( 'entity', '' );

		$this->assertEquals( 'entity', $entity_type_service->get_slug() );

	}

	/**
	 * Check that the default entity type is `Thing`.
	 *
	 * @since 3.18.0
	 */
	public function test_entity_with_default_entity_type() {

		$entity_type_service = $this->get_wordlift_test()->get_entity_type_service();

		$entity_id = $this->factory->post->create( array(
			'post_type' => 'entity',
		) );

		$schema = $entity_type_service->get( $entity_id );

		$this->assertEquals( 'http://schema.org/Thing', $schema['uri'] );
	}

	/**
	 * Check that the entity type with set `wl_entity_type` is working.
	 *
	 * @since 3.18.0
	 */
	public function test_entity_with_set_entity_type() {

		$entity_type_service = $this->get_wordlift_test()->get_entity_type_service();

		$entity_id = $this->factory->post->create( array(
			'post_type' => 'entity',
		) );

		wp_set_object_terms( $entity_id, 'person', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		$schema = $entity_type_service->get( $entity_id );

		$this->assertEquals( 'http://schema.org/Person', $schema['uri'] );
	}

	/**
	 * Check that the posts are assigned `articles` by default.
	 *
	 * @since 3.18.0
	 */
	public function test_post_with_default_entity_type() {

		$entity_type_service = $this->get_wordlift_test()->get_entity_type_service();

		$post_id = $this->factory->post->create();

		$schema = $entity_type_service->get( $post_id );

		$this->assertEquals( 'http://schema.org/Article', $schema['uri'] );
	}

	/**
	 * Check that the posts with set `wl_entity_type` are working.
	 *
	 * @since 3.18.0
	 */
	public function test_post_with_set_entity_type() {

		$entity_type_service = $this->get_wordlift_test()->get_entity_type_service();

		$post_id = $this->factory->post->create();

		wp_set_object_terms( $post_id, 'thing', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		$schema = $entity_type_service->get( $post_id );

		$this->assertEquals( 'http://schema.org/Thing', $schema['uri'] );
	}

	/**
	 * Check that the custom post types are assigned `webpage`.
	 *
	 * @since 3.18.0
	 */
	public function test_custom_post_type_with_default_entity_type() {

		$entity_type_service = $this->get_wordlift_test()->get_entity_type_service();

		$post_id = $this->factory->post->create( array(
			'post_type' => 'property',
		) );

		$schema = $entity_type_service->get( $post_id );

		$this->assertEquals( 'http://schema.org/WebPage', $schema['uri'] );
	}

	/**
	 * Check that the custom post types with set `wl_entity_tyoe `are assigned `webpage` too.
	 *
	 * This is true, because the CPT is not valid entity type
	 * and the `wl_entity_type` is not taken into consideration.
	 *
	 * @since 3.18.0
	 */
	public function test_custom_post_type_with_set_entity_type() {

		$entity_type_service = $this->get_wordlift_test()->get_entity_type_service();

		$post_id = $this->factory->post->create( array(
			'post_type' => 'property',
		) );

		wp_set_object_terms( $post_id, 'thing', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		$schema = $entity_type_service->get( $post_id );

		// The `property` post type is not valid entity type, so setting entity type
		// shouldn't change the schema type.
		$this->assertEquals( 'http://schema.org/WebPage', $schema['uri'] );
	}

	/**
	 * Check that extending the wl_valid_entity_post_types with custom post type
	 * still return `article` entity type.
	 *
	 * @since 3.18.0
	 */
	public function test_custom_post_type_entity_with_default_entity_type() {

		$entity_type_service = $this->get_wordlift_test()->get_entity_type_service();

		add_filter( 'wl_valid_entity_post_types', array(
			$this,
			'extend_default_entity_types',
		), 10, 1 );

		$post_id = $this->factory->post->create( array(
			'post_type' => 'property',
		) );

		$schema = $entity_type_service->get( $post_id );
		// The custom post types are automatically assigned to `articles` entity type.
		$this->assertEquals( 'http://schema.org/Article', $schema['uri'] );
	}

	/**
	 * Check that extending the wl_valid_entity_post_types with custom post type
	 * and assigning `wl_entity_type` is working.
	 * 
	 * @since 3.18.0
	 */
	public function test_custom_post_type_entity_with_set_entity_type() {

		$entity_type_service = $this->get_wordlift_test()->get_entity_type_service();

		add_filter( 'wl_valid_entity_post_types', array(
			$this,
			'extend_default_entity_types',
		), 10, 1 );

		$post_id = $this->factory->post->create( array(
			'post_type' => 'property',
		) );

		wp_set_object_terms( $post_id, 'person', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		$schema = $entity_type_service->get( $post_id );

		// The `property` post type is not valid entity type, so setting entity type
		// shouldn't change the schema type.
		$this->assertEquals( 'http://schema.org/Person', $schema['uri'] );
	}

	function extend_default_entity_types( $types ) {

		// Add `property` custom post type to wl_entity_types
		$types[] = 'property';

		return $types;
	}

}
