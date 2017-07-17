<?php
/**
 * Tests: Metabox for Recipes.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the {@link WL_Metabox_Recipe_Test} class.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */
class WL_Metabox_Recipe_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Entity_Type_Service} instance.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift_Entity_Type_Service $entity_type_service The {@link Wordlift_Entity_Type_Service} instance.
	 */
	private $entity_type_service;

	/**
	 * @inheritdoc
	 */
	function setUp() {
		parent::setUp();

		$this->entity_type_service = $this->get_wordlift_test()->get_entity_type_service();

	}

	/**
	 * Test instantiating fields on a Recipe.
	 *
	 * @since 3.14.0
	 */
	function test_instantiate_fields() {

		// Create a Recipe.
		$entity_post_id = $this->entity_factory->create();
		$this->entity_type_service->set( $entity_post_id, 'http://schema.org/Recipe' );

		// Create a Metabox instance and load the fields.
		$metabox = new WL_Metabox();
		$metabox->instantiate_fields( $entity_post_id );

		// Check that the fields are all `WL_Metabox_Field`s.
		$this->assertContainsOnlyInstancesOf( 'WL_Metabox_Field', $metabox->fields );

		// Check that the recipe fields are there.
		$this->assertNotNull( $this->get_by_meta_name( $metabox->fields, 'wl_schema_recipe_cuisine' ) );
		$this->assertNotNull( $this->get_by_meta_name( $metabox->fields, 'wl_schema_recipe_ingredient' ) );
		$this->assertNotNull( $this->get_by_meta_name( $metabox->fields, 'wl_schema_recipe_instructions' ) );
		$this->assertNotNull( $this->get_by_meta_name( $metabox->fields, 'wl_schema_recipe_yield' ) );
		$this->assertNotNull( $this->get_by_meta_name( $metabox->fields, 'wl_schema_prep_time' ) );
		$this->assertNotNull( $this->get_by_meta_name( $metabox->fields, 'wl_schema_total_time' ) );
		$this->assertNotNull( $this->get_by_meta_name( $metabox->fields, 'wl_author' ) );
		$this->assertNotNull( $this->get_by_meta_name( $metabox->fields, 'wl_schema_url' ) );
		$this->assertNotNull( $this->get_by_meta_name( $metabox->fields, 'entity_same_as' ) );

	}

	/**
	 * Get a field given its meta name.
	 *
	 * @since 3.14.0
	 *
	 * @param array  $fields An array of {@link WL_Metabox_Field}s.
	 * @param string $name   The meta name.
	 *
	 * @return WL_Metabox_Field|null The {@link WL_Metabox_Field} or null if not found.
	 */
	private function get_by_meta_name( $fields, $name ) {

		foreach ( $fields as $field ) {
			if ( $name === $field->meta_name ) {
				return $field;
			}
		}

		return null;
	}

}
