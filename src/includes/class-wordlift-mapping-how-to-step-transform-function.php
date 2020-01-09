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
					'@type' => 'HowToStep',
					'name'  => $section_item['step_name'],
					'text'  => wp_strip_all_tags( $section_item['step_text'] ),
				)
			);
		}
		return $schema_section_items;
	}
	/**
	 * {@inheritdoc}
	 */
	public function map_data_to_schema_properties( $data ) {
		$acf_steps    = $data['value'];
		$schema_steps = array();
		foreach ( $acf_steps as $step ) {
			$single_schema_step = array();
			if ( array_key_exists( 'type', $step ) ) {
				$single_schema_step['@type'] = $step['type'];
			}
			if ( array_key_exists( 'text', $step ) ) {
				$single_schema_step['text'] = wp_strip_all_tags( $step['text'] );
			}
			if ( array_key_exists( 'name', $step ) ) {
				$single_schema_step['name'] = $step['name'];
			}
			if ( array_key_exists( 'image', $step ) ) {
				$single_schema_step['image'] = $step['image'];
			}
			if ( array_key_exists( 'section_item', $step ) ) {
				$single_schema_step['itemListElement'] 
				= $this->convert_acf_section_items_to_schema_section_items( (array) $step['section_item'] );
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

