<?php
/**
 * Define the Wordlift_Mapping_Jsonld_Converter class to add JSON-LD generated from mappings.
 *
 * @since   3.25.0
 * @package Wordlift
 */

/**
 * This class takes the output from json-ld service and alter depends on the
 * rule and properties defined in sync mappings.
 *
 * @since 3.25.0
 */
class Wordlift_Mapping_Jsonld_Converter {
	/**
	 * The {@link Wordlift_Mapping_Validator} instance to test.
	 *
	 * @since  3.25.0
	 * @access private
	 * @var \Wordlift_Mapping_Validator $validator The {@link Wordlift_Mapping_Validator} instance.
	 */
	private $validator;
	/**
	 * The {@link Wordlift_Mapping_Transform_Function_Registry} instance.
	 *
	 * @since  3.25.0
	 * @access private
	 * @var \Wordlift_Mapping_Transform_Function_Registry $transform_functions_registry The {@link Wordlift_Mapping_Transform_Function_Registry} instance.
	 */
	private $transform_functions_registry;

	/**
	 * The variable to hold the json-ld data
	 *
	 * @since  3.25.0
	 * @access private
	 * @var Array $jsonld_data The array of json-ld data
	 */
	private $jsonld_data;

	/**
	 * The variable to hold the post_id
	 *
	 * @since  3.25.0
	 * @access private
	 * @var Int $post_id The id of the post.
	 */
	private $post_id;

	/**
	 * Initialize all dependancies required.
	 *
	 * @param Int   $post_id The Id of the post where the mappings should be validated.
	 * @param Array $jsonld_data An Array of json-ld data from {@link \Wordlift_Jsonld_Service} class.
	 */
	public function __construct( $post_id, $jsonld_data ) {
		$this->validator                    = new Wordlift_Mapping_Validator();
		$this->transform_functions_registry = new Wordlift_Mapping_Transform_Function_Registry();
		$this->jsonld_data                  = $jsonld_data;
		$this->post_id                      = $post_id;
	}

	/**
	 * Returns Json-LD data after applying transformation functions.
	 *
	 * @return Array Array of json-ld data.
	 */
	public function get_jsonld_data() {
		// Validate the post id here.
		$this->validator->validate( $this->post_id );

		$json_ld_data_array = $this->jsonld_data;
		$properties         = $this->validator->get_valid_properties();

		foreach ( $properties as $property ) {
			$transform_instance = $this->transform_functions_registry->get_transform_function( $property['transform_help_text'] );
			if ( null !== $transform_instance ) {
				$transformed_data = $transform_instance->transform_data( $this->post_id, $property );
				foreach ( $json_ld_data_array as &$jsonld_data ) {
					$jsonld_data[ $transformed_data['key'] ] = $transformed_data['value'];
				}
			}
			else {
				// No transform function exists, do 1 to 1 mapping, just map the string value to the key.
				foreach ( $json_ld_data_array as &$jsonld_data ) {
					$jsonld_data[ $property['property_help_text'] ] = $property['field_help_text'];
				}
			}
		}
		return $json_ld_data_array;
	}

}
