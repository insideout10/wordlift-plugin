<?php
require_once 'class-wordlift-mapping-transform-function.php';
/**
 * Define the Wordlift_Mapping_Acf_Transform_Function Class
 *
 * @since   3.25.0
 * @package Wordlift
 */

/**
 * This class extends the interface { @link \Wordlift_Mapping_Transform_Function }
 * and creates transform function.
 *
 * @since 3.25.0
 */
class Wordlift_Mapping_How_To_Tool_Transform_Function extends Wordlift_Mapping_Transform_Function {

	/**
	 * {@inheritdoc}
	 */
	public function get_name() {
		return 'how_to_tool_transform_function';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_label() {
		return __( 'HowToTool Transform function', 'wordlift' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_data_from_data_source( $post_id, $property_data ) {
		$key   = $property_data['property_name'];
		$value = null;
		// Check ACF is loaded.
		if ( function_exists( 'get_field' ) ) {
			$value = get_field( $property_data['field_name'], $post_id );
		}	
		return array(
			'key'   => $key,
			'value' => $value,
		);
	}
	/**
	 * {@inheritdoc}
	 */
	public function map_data_to_schema_properties( $data ) {
		var_dump( $data );
		$acf_supply_items    = $data['value'];
		$schema_supply_items = array();
		foreach ( $acf_supply_items as $supply_item ) {
			array_push(
				$schema_supply_items,
				array(
					'@type' => $supply_item['type'],
					'name'  => wp_strip_all_tags( $supply_item['name'] ),
				)
			);
		}
		$data['value'] = $schema_supply_items;
		return $data;
	}
}
