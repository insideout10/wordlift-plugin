<?php
/**
 * The class to hold all the transform functions for the JSON-LD Mapping.
 * Define the {@link Wordlift_Mapping_Transform_Function_Registry} class.
 *
 * @since      3.25.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/sync-mappings
 */
class Wordlift_Mapping_Transform_Function_Registry {
	/**
	 * Holds an array of transformation functions, all the transformation
	 * functions are instance of { @link \Wordlift_Mapping_Transform_Function} Interface
	 *
	 * @since  3.25.0
	 * @access private
	 * @var \Wordlift_Mapping_Validator $validator The {@link Wordlift_Mapping_Validator} instance to test.
	 */
	private $transform_function_array = array();
	/**
	 * Construct a list of transform function array.
	 */
	public function __construct() {
		$this->transform_function_array = array(
			new Wordlift_Mapping_How_To_Step_Transform_Function(),
			new Wordlift_Mapping_Text_Transform_Function(),
			new Wordlift_Mapping_How_To_Supply_Transform_Function(),
			new Wordlift_Mapping_How_To_Tool_Transform_Function(),
		);
	}

	/**
	 * Return options required for ui
	 *
	 * @return Array An Array of transform function options.
	 */
	public function get_options() {
		$options = array();
		foreach ( $this->transform_function_array as $transform_function ) {
			array_push(
				$options,
				array(
					'label' => $transform_function->get_label(),
					'value' => $transform_function->get_name(),
				)
			);
		}
		return $options;
	}

	/**
	 * Return instance of the transform function.
	 *
	 * @param  String $transform_function_name The name of the transform function which needs to applied.
	 * @return Wordlift_Mapping_Transform_Function|null An Instance of transform function from any one of
	 * the classes extending this interface, if nothing matches null is returned.
	 */
	public function get_transform_function( $transform_function_name ) {
		foreach ( $this->transform_function_array as $transform_function_instance ) {
			if ( $transform_function_instance->get_name() === $transform_function_name ) {
				return $transform_function_instance;
			}
		}
		// Returns null if the transform function doesn't match.
		return null;
	}
}
