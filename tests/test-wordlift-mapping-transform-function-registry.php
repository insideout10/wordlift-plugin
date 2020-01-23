<?php
/**
 * Tests: Mappings_Transform_Function_Registry Test.
 *
 * This file contains the tests for the {@link Mappings_Transform_Functions_Registry } class.
 *
 * @since   3.25.0
 * @package Wordlift
 */

use Wordlift\Mappings\Mappings_Transform_Function;
use Wordlift\Mappings\Mappings_Transform_Functions_Registry;

/**
 * Define the test class.
 *
 * @group mappings
 *
 * @since   3.25.0
 * @package Wordlift
 */
class Wordlift_Mock_Transformation_Function implements Mappings_Transform_Function {
	public function get_name() {
		return 'foo';
	}

	public function get_label() {
		return 'foo label';
	}

	public function transform_data( $data, $jsonld, &$references, $post_id ) {
		return $data;
	}
}

class Mappings_Transform_Function_Registry_Test extends Wordlift_Unit_Test_Case {
	private static function add_transformation_function_to_hook() {
		// Emulating this from a external plugin, that plugin should add this to the hook.
		add_filter(
			'wl_mappings_transformation_functions',
			function ( $transformation_functions ) {
				array_push( $transformation_functions, new Wordlift_Mock_Transformation_Function() );

				return $transformation_functions;
			}
		);
	}

	public function setUp() {
		parent::setUp();
	}

	public function test_can_add_transformation_function_via_filter() {
		self::add_transformation_function_to_hook();
		// Check if the registry has this instance.
		$registry                = new Mappings_Transform_Functions_Registry();
		$transformation_function = $registry->get_transform_function( 'foo' );
		$this->assertNotNull( $transformation_function, 'Transformation function should be present' );
	}

	/**
	 * If the plugin is loaded after wordlift, it should still load the transformation function.
	 */
	public function test_if_the_plugin_is_activated_after_wl_should_have_tf() {
		$registry = new Mappings_Transform_Functions_Registry();
		// Emulate loading transformation function plugin after wordlift.
		self::add_transformation_function_to_hook();
		$transformation_function = $registry->get_transform_function( 'foo' );
		$this->assertNotNull( $transformation_function, 'Transformation function should be present' );
		// We now have also the URL to Entity transform function.
		$this->assertEquals( 2, $registry->get_transform_function_count() );
	}

	/**
	 * If the plugin tries to register the same transformation function multiple times, dont allow it.
	 */
	public function test_can_prevent_duplication_of_transformation_function() {
		$registry = new Mappings_Transform_Functions_Registry();
		// Emulate loading transformation function plugin after wordlift.
		self::add_transformation_function_to_hook();
		// Trying to duplicate the transformation function, registry should prevent this from happening.
		self::add_transformation_function_to_hook();
		$transformation_function = $registry->get_transform_function( 'foo' );
		$this->assertNotNull( $transformation_function, 'Transformation function should be present' );
		// The mock function and our URL to Entity Function.
		$this->assertEquals( 2, $registry->get_transform_function_count() );
	}

}
