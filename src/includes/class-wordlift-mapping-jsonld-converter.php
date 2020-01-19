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
     * Returns data from data source.
     *
     * @param int   $post_id Id of the post.
     * @param array $property_data The property data for the post_id.
     *
     * @return array Returns key, value array, if the value is not found, then it
     * returns null.
     */
    final public function get_data_from_data_source( $post_id, $property_data ) {
        $value = $property_data['field_name'];
        // Do 1 to 1 mapping and return result.
        if ( 'acf' === $property_data['field_type'] && function_exists( 'get_field' ) ) {
            $value = get_field( $property_data['field_name'], $post_id );
            $value = ( null !== $value ) ? $value : '';
        }
        return $value;
    }
	/**
	 * Returns Json-LD data after applying transformation functions.
	 *
	 * @return array Array of json-ld data.
	 */
	public function get_jsonld_data() {
		// Validate the post id here.
		$this->validator->validate( $this->post_id );
		$json_ld_item = $this->jsonld_data;
		$properties         = $this->validator->get_valid_properties();
		foreach ( $properties as $property ) {
			$transform_instance = $this->transform_functions_registry->get_transform_function( $property['transform_function'] );
			if ( null !== $transform_instance ) {
			    $data  = $this->get_data_from_data_source( $this->post_id, $property );
				$json_ld_item[ $property['property_name'] ] = $transform_instance->transform_data( $data );
			} else {
				$json_ld_item[ $property['property_name'] ] = $property['field_name'];
			}
		}
		return $json_ld_item;
	}
	/**
	 * @param array $jsonld_item An associative array of entity
	 * @param int $post_id The Post Id.
	 *
	 * @return array A single jsonld item.
	 */
	public static function create_converter_instance_and_convert( $jsonld_item, $post_id ) {
		$mapping_converter = new Wordlift_Mapping_Jsonld_Converter( $post_id, $jsonld_item );
		$jsonld_item       = $mapping_converter->get_jsonld_data();
		return $jsonld_item;
	}

}

add_filter( 'wl_post_jsonld', function( $jsonld_item, $post_id, $references ) {
	return Wordlift_Mapping_Jsonld_Converter::create_converter_instance_and_convert( $jsonld_item, $post_id);
}, 10, 3);

add_filter( 'wl_entity_jsonld', function( $jsonld_item, $post_id, $references ) {
	return Wordlift_Mapping_Jsonld_Converter::create_converter_instance_and_convert( $jsonld_item, $post_id);
}, 10, 3);