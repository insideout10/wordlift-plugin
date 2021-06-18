<?php
/**
 * Tests: Metabox for Recipes.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

use Wordlift\Metabox\Wl_Metabox;

/**
 * Define the {@link WL_Metabox_Recipe_Test} class.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 * @group metabox
 */
class WL_Metabox_Recipe_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test instantiating fields on a Recipe.
	 *
	 * @since 3.14.0
	 * @group metabox
	 */
	function test_instantiate_fields() {

		// Create a Recipe.
		$entity_post_id = $this->entity_factory->create( array(
			'post_title' => 'Test Metabox Recipe test_instantiate_fields'
		) );
		$this->entity_type_service->set( $entity_post_id, 'http://schema.org/Recipe' );

		// Create a Metabox instance and load the fields.
		$metabox = new WL_Metabox();
		$metabox->instantiate_fields( $entity_post_id, \Wordlift\Metabox\Wl_Abstract_Metabox::POST );

		// Check that the fields are all `WL_Metabox_Field`s.
		$this->assertContainsOnlyInstancesOf( 'Wordlift\Metabox\Field\WL_Metabox_Field', $metabox->fields );

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
	 * @param array  $fields An array of {@link Wl_Metabox_Field}s.
	 * @param string $name   The meta name.
	 *
	 * @return WL_Metabox_Field|null The {@link Wl_Metabox_Field} or null if not found.
	 *
	 * @group metabox
	 *@since 3.14.0
	 *
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
