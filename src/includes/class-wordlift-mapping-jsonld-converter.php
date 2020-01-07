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
	 * Initialize all dependancies required.
	 *
	 * @param Int   $post_id The Id of the post where the mappings should be validated.
	 * @param Array $jsonld_data An Array of json-ld data from {@link \Wordlift_Jsonld_Service} class.
	 */
	public function __construct( $post_id, $jsonld_data ) {
		$this->validator = new Wordlift_Mapping_Validator();
		// Validate the post id here.
		$this->validator->validate( $post_id );
		$this->transform_functions_registry = new Wordlift_Mapping_Transform_Function_Registry();
		$this->jsonld_data                  = $jsonld_data;
	}

	/**
	 * Returns Json-LD data after validating the mappings.
	 * @return Array Array of json-ld data.
	 */
	public function get_jsonld_data() {
		$properties = $this->validator->get_valid_properties();
		foreach ( $properties as $property ) {

		}
		return $this->jsonld_data;
	}

}
