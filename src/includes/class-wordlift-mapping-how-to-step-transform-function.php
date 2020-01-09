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
class Wordlift_Mapping_How_To_Step_Transform_Function extends Wordlift_Mapping_Transform_Function {

	/**
	 * {@inheritdoc}
	 */
	public function get_name() {
		return 'how_to_step_transform_function';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_label() {
		return __( 'HowToStep Transform function', 'wordlift' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_data_from_data_source( $post_id, $property_data ) {
		$key   = $property_data['property_name'];
		$value = null;
		// Check ACF is loaded.
		if ( function_exists( 'get_field' ) ) {
			$value = get_field( $key, $post_id );
		}	
		return array(
			'key'   => $key,
			'value' => $value,
		);
	}
	/**
	 * Takes ACF section items and convert to schema section items.
	 *
	 * @param Array $section_items Array of ACF Section items.
	 * @return Array $schema_section_items Array of schema section items.
	 */
	private function convert_acf_section_items_to_schema_section_items( $section_items ) {
		$schema_section_items = array();
		foreach ( $section_items as $section_item ) {
			array_push(
				$schema_section_items,
				array(
					'@type' => $section_item['type'],
					'name'  => $section_item['step_name'],
					'text'  => wp_strip_all_tags( $section_item['step_text'] ),
				)
			);
		}
		return $schema_section_items;
	}
	/**
	 * Takes ACF step items and convert to schema step items.
	 *
	 * @param Array $step_items Array of ACF step items.
	 * @return Array $schema_step_items Array of schema step items.
	 */
	private function convert_acf_step_items_to_schema_step_items( $step_items ) {
		$schema_step_items = array();
		foreach ( $step_items as $step_item ) {
			array_push(
				$schema_step_items,
				array(
					'@type' => $step_item['step_type'],
					'text'  => wp_strip_all_tags( $step_item['step_text'] ),
				)
			);
		}
		return $schema_step_items;
	}
	/**
	 * Returns true if the value is not empty, if the value
	 * is array then it should contain atleast one element.
	 *
	 * @param String $key Key of the array element.
	 * @param Array  $source Source Array.
	 * @return Boolean.
	 */
	private static function array_key_not_empty( $key, $source ) {
		if ( array_key_exists( $key, $source ) ) {
			$value = $source[ $key ];
			if ( is_array( $value ) ) {
				// Should contain atleast one element.
				return 1 <= count( $value );
			}
			elseif ( is_bool( $value ) ) {
				return $value;
			}
			else {
				return null !== $value && '' !== $value;
			}
		}
		else {
			return false;
		}
	}
	/**
	 * {@inheritdoc}
	 */
	public function map_data_to_schema_properties( $data ) {
		$acf_steps    = $data['value'];
		$schema_steps = array();
		foreach ( $acf_steps as $step ) {
			$single_schema_step = array();
			if ( self::array_key_not_empty( 'type', $step ) ) {
				$single_schema_step['@type'] = $step['type'];
			}
			if ( self::array_key_not_empty( 'text', $step ) ) {
				$single_schema_step['text'] = wp_strip_all_tags( $step['text'] );
			}
			if ( self::array_key_not_empty( 'name', $step ) ) {
				$single_schema_step['name'] = $step['name'];
			}
			if ( self::array_key_not_empty( 'image', $step ) ) {
				$single_schema_step['image'] = $step['image'];
			}
			if ( self::array_key_not_empty( 'section_item', $step ) ) {
				$single_schema_step['itemListElement'] = $this->convert_acf_section_items_to_schema_section_items( $step['section_item'] );
			}
			if ( self::array_key_not_empty( 'step_item', $step ) ) {
				// Step items can be HowToTip or HowToDirection.
				$single_schema_step['itemListElement'] = $this->convert_acf_step_items_to_schema_step_items( $step['step_item'] );
			}
			array_push(
				$schema_steps,
				$single_schema_step
			);
		}
		$data['value'] = $schema_steps;
		return $data;
	}
}

