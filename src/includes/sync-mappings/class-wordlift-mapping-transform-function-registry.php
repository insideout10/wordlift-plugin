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
			new Wordlift_Mapping_Acf_Transform_Function(),
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
}
