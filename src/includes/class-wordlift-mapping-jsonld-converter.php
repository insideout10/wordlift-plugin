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
	 * @var array $jsonld_data The array of json-ld data
	 */
	private $jsonld_data;

	/**
	 * The variable to hold the post_id
	 *
	 * @since  3.25.0
	 * @access private
	 * @var int $post_id The id of the post.
	 */
	private $post_id;

	/**
	 * Initialize all dependencies required.
	 *
	 * @param int $post_id The Id of the post where the mappings should be validated.
	 * @param array $jsonld_data An Array of json-ld data from {@link \Wordlift_Jsonld_Service} class.
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
	 * @return array Array of json-ld data.
	 */
	public function get_jsonld_data() {

		// Validate the post id here.
		$this->validator->validate( $this->post_id );
		$json_ld_data_array = $this->jsonld_data;
		$properties         = $this->validator->get_valid_properties();
		$json_ld_item       = array();
		foreach ( $properties as $property ) {
			$transform_instance = $this->transform_functions_registry->get_transform_function( $property['transform_function'] );
			if ( null !== $transform_instance ) {
				$transformed_data                         = $transform_instance->get_transformed_data( $this->post_id, $property );
				$json_ld_item[ $transformed_data['key'] ] = $transformed_data['value'];
			} else {
				$json_ld_item[ $property['property_name'] ] = $property['field_name'];
			}
		}
		// Only if keys of json ld item is greater than 0 then push it to array.
		if ( 0 < count( array_keys( $json_ld_item ) ) ) {
			array_push( $json_ld_data_array, $json_ld_item );
		}

		return $json_ld_data_array;
	}

}
