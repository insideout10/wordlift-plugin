<?php
/**
 * Tests: Entity Type Service Test.
 *
 * Test the {@link Wordlift_Entity_Type_Service} class.
 *
 * @since 3.18.0
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the Wordlift_Entity_Type_Service_Test class.
 *
 * @since      3.18.0
 */
class Wordlift_Entity_Type_Service_Test extends Wordlift_Unit_Test_Case {

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {

		$this->set_current_screen( 'admin' );

		$this->assertTrue( defined( 'WL_ALL_ENTITY_TYPES' ) );
		$this->assertTrue( WL_ALL_ENTITY_TYPES );

		parent::setUp();

		// Ensure `All Entity Types` are installed.
		$sync = Wordlift_Schemaorg_Sync_Batch_Operation::get_instance();
		$sync->process( 0, $sync->count() );

	}

	/**
	 * This test function gathers together all the tests, because loading the `All Entity Types` is an
	 * expensive operation, we want to avoid to repeat it for each separate test.
	 *
	 * @since 3.20.0
	 */
	public function test() {

		$this->do_test_vocabulary_slug();
		$this->do_test_empty_slug();
		$this->do_test_entity_with_default_entity_type();
		$this->do_test_entity_with_set_entity_type();
		$this->do_test_post_with_default_entity_type();
		$this->do_test_post_with_set_entity_type();
		$this->do_test_custom_post_type_with_default_entity_type();
		$this->do_test_custom_post_type_with_set_entity_type();
		$this->do_test_custom_post_type_entity_with_default_entity_type();
		$this->do_test_custom_post_type_entity_with_set_entity_type();
		$this->do_test_vocabulary_slug();
		$this->do_test_empty_slug();
		$this->do_test_entity_with_default_entity_type();
		$this->do_test_entity_with_set_entity_type();
		$this->do_test_post_with_default_entity_type();
		$this->do_test_post_with_set_entity_type();
		$this->do_test_custom_post_type_with_default_entity_type();
		$this->do_test_custom_post_type_with_set_entity_type();
		$this->do_test_custom_post_type_entity_with_default_entity_type();
		$this->do_test_custom_post_type_entity_with_set_entity_type();
		$this->do_test_get_835();
		$this->do_test_get_ids_names();
		$this->do_test_set_835();

	}

	/**
	 * Check that the specified slug is used.
	 *
	 * @since 3.18.0
	 */
	private function do_test_vocabulary_slug() {

		$entity_type_service = new Wordlift_Entity_Post_Type_Service( 'entity', 'vocabulary' );

		$this->assertEquals( 'vocabulary', $entity_type_service->get_slug() );

	}

	/**
	 * Check that, when an empty slug is used, the default entity post type name is used.
	 *
	 * @since 3.18.0
	 */
	private function do_test_empty_slug() {

		$entity_type_service = new Wordlift_Entity_Post_Type_Service( 'entity', '' );

		$this->assertEquals( 'entity', $entity_type_service->get_slug() );

	}

	/**
	 * Check that the default entity type is `Thing`.
	 *
	 * @since 3.18.0
	 */
	private function do_test_entity_with_default_entity_type() {

		$entity_type_service = $this->get_wordlift_test()->get_entity_type_service();

		$entity_id = $this->factory()->post->create( array(
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
	private function do_test_entity_with_set_entity_type() {

		$entity_type_service = $this->get_wordlift_test()->get_entity_type_service();

		$entity_id = $this->factory()->post->create( array(
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
	private function do_test_post_with_default_entity_type() {

		$entity_type_service = $this->get_wordlift_test()->get_entity_type_service();

		$post_id = $this->factory()->post->create();

		$schema = $entity_type_service->get( $post_id );

		$this->assertEquals( 'http://schema.org/Article', $schema['uri'] );
	}

	/**
	 * Check that the posts with set `wl_entity_type` are working.
	 *
	 * @since 3.18.0
	 */
	private function do_test_post_with_set_entity_type() {

		$entity_type_service = $this->get_wordlift_test()->get_entity_type_service();

		$post_id = $this->factory()->post->create();

		wp_set_object_terms( $post_id, 'thing', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		$schema = $entity_type_service->get( $post_id );

		$this->assertEquals( 'http://schema.org/Thing', $schema['uri'] );
	}

	/**
	 * Check that the custom post types are assigned `WebPage`.
	 *
	 * @since 3.18.0
	 */
	private function do_test_custom_post_type_with_default_entity_type() {

		$entity_type_service = $this->get_wordlift_test()->get_entity_type_service();

		$post_id = $this->factory()->post->create( array(
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
	private function do_test_custom_post_type_with_set_entity_type() {

		$entity_type_service = $this->get_wordlift_test()->get_entity_type_service();

		$post_id = $this->factory()->post->create( array(
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
	private function do_test_custom_post_type_entity_with_default_entity_type() {

		$entity_type_service = $this->get_wordlift_test()->get_entity_type_service();

		add_filter( 'wl_valid_entity_post_types', array( $this, 'extend_default_entity_types', ) );

		$post_id = $this->factory()->post->create( array(
			'post_type' => 'property',
		) );

		$schema = $entity_type_service->get( $post_id );

		// The custom post types are automatically assigned to `articles` entity type.
		$this->assertEquals( 'http://schema.org/Article', $schema['uri'] );

		remove_filter( 'wl_valid_entity_post_types', array( $this, 'extend_default_entity_types', ) );

	}

	/**
	 * Check that extending the wl_valid_entity_post_types with custom post type
	 * and assigning `wl_entity_type` is working.
	 *
	 * @since 3.18.0
	 */
	private function do_test_custom_post_type_entity_with_set_entity_type() {

		$entity_type_service = $this->get_wordlift_test()->get_entity_type_service();

		add_filter( 'wl_valid_entity_post_types', array( $this, 'extend_default_entity_types', ) );

		$post_id = $this->factory()->post->create( array(
			'post_type' => 'property',
		) );

		wp_set_object_terms( $post_id, 'person', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		$schema = $entity_type_service->get( $post_id );

		// The `property` post type is not valid entity type, so setting entity type
		// shouldn't change the schema type.
		$this->assertEquals( 'http://schema.org/Person', $schema['uri'] );

		remove_filter( 'wl_valid_entity_post_types', array( $this, 'extend_default_entity_types', ) );

	}

	/**
	 * Add the `property` cpt to the entity types.
	 *
	 * @param array $types Custom post types.
	 *
	 * @return array Custom post types plus `property`.
	 * @since 3.20.0
	 *
	 */
	public function extend_default_entity_types( $types ) {

		// Add `property` custom post type to wl_entity_types
		$types[] = 'property';

		return $types;
	}

	/**
	 * When adding support for `All Entity Types`, we need to maintain compatibility for the existing
	 * function `get` which must return only one term.
	 *
	 * In this context we return the first entity type defined via the {@link Wordlift_Schema_Service}.
	 *
	 * @see https://github.com/insideout10/wordlift-plugin/issues/844
	 *
	 * @since 3.20.0
	 */
	private function do_test_get_835() {

		// Entity with one Wordlift_Schema_Service defined type and others.
		$post_1_id = $this->factory()->post->create( array(
			'post_type' => 'entity',
		) );
		wp_set_object_terms( $post_1_id, array(
			'organization',
			'hospital',
		), Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		$type_1 = $this->entity_type_service->get( $post_1_id );

		$this->assertArrayHasKey( 'uri', $type_1, 'The type must have the `uri` key.' );
		$this->assertEquals( 'http://schema.org/Organization', $type_1['uri'], "The type's uri must be `Organization`." );

		// Entity with only other types, we expect `thing`.
		$post_2_id = $this->factory()->post->create( array(
			'post_type' => 'entity',
		) );

		wp_set_object_terms( $post_2_id, array(
			'hospital',
			'medical-condition',
		), Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		$type_2 = $this->entity_type_service->get( $post_2_id );

		$this->assertArrayHasKey( 'uri', $type_2, 'The type must have the `uri` key.' );
		$this->assertEquals( 'http://schema.org/Thing', $type_2['uri'], "The type's label must be `Thing`." );

	}

	/**
	 * Test the `get_ids` and `get_names` function.
	 *
	 * @since 3.20.0
	 */
	private function do_test_get_ids_names() {

		$post_id = $this->factory()->post->create( array(
			'post_type' => 'entity',
		) );
		wp_set_object_terms( $post_id, array(
			'organization',
			'hospital',
		), Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		$ids = $this->entity_type_service->get_ids( $post_id );

		$this->assertCount( 2, $ids, 'There must be 2 ids.' );

		$organization_term = get_term_by( 'slug', 'organization', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
		$this->assertNotFalse( $organization_term, '`organization` term must exit.' );
		$this->assertContains( $organization_term->term_id, $ids, 'The `organization` term id must be present.' );

		$hospital_term = get_term_by( 'slug', 'hospital', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
		$this->assertNotFalse( $hospital_term, '`hospital` term must exit.' );
		$this->assertContains( $hospital_term->term_id, $ids, 'The `hospital` term id must be present, got ' . var_export( $ids, true ) );

		$hospital_term_name = get_term_meta( $hospital_term->term_id, Wordlift_Schemaorg_Class_Service::NAME_META_KEY, true );
		$this->assertEquals( 'Hospital', $hospital_term_name, 'Expect `hospital` name to be `Hospital`.' );

		$names = $this->entity_type_service->get_names( $post_id );
		$this->assertCount( 2, $names, 'There must be 2 names.' );
		$this->assertContains( 'Organization', $names, '`Organization` must be present, got ' . var_export( $names, true ) );
		$this->assertContains( 'Hospital', $names, '`Hospital` must be present, got ' . var_export( $names, true ) );

	}

	/**
	 * Test the `set` function compatibility after #835.
	 *
	 * @since 3.20.0
	 */
	private function do_test_set_835() {

		$post_id = $this->factory()->post->create( array(
			'post_type' => 'entity',
		) );

		// Set via URI.
		$this->entity_type_service->set( $post_id, 'http://schema.org/Hospital' );

		$terms_1 = wp_get_object_terms( $post_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, array( 'fields' => 'slugs' ) );

		$this->assertCount( 1, $terms_1, 'There must be 1 term.' );
		$this->assertContains( 'hospital', $terms_1, 'Terms must contain `hospital`, got ' . var_export( $terms_1, true ) );

		// Set via css class.
		$this->entity_type_service->set( $post_id, 'wl-person' );

		$terms_2 = wp_get_object_terms( $post_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, array( 'fields' => 'slugs' ) );

		$this->assertCount( 1, $terms_2, 'There must be 1 term.' );
		$this->assertContains( 'person', $terms_2, 'Terms must contain `person`.' );

		// Add via URI.
		$this->entity_type_service->set( $post_id, 'http://schema.org/Hospital', false );

		$terms_3 = wp_get_object_terms( $post_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, array( 'fields' => 'slugs' ) );

		$this->assertContains( 'hospital', $terms_3, 'Terms must contain `hospital`, got ' . var_export( $terms_3, true ) );
		$this->assertContains( 'person', $terms_3, 'Terms must contain `person`, got ' . var_export( $terms_3, true ) );

	}

}
