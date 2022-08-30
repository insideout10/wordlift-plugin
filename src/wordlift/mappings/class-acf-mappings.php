<?php
/**
 * This file defines the Acf_Mappings class which provides support for ACF fields.
 *
 * @since 3.25.0
 * @package Wordlift\Mappings
 */

namespace Wordlift\Mappings;

/**
 * Define the Acf_Mappings class.
 *
 * @package Wordlift\Mappings
 */
class Acf_Mappings {

	/**
	 * Acf_Mappings constructor.
	 *
	 * Hooks `wl_mappings_field_types` to our own function to declare support for ACF fields.
	 */
	public function __construct() {

		$that = $this;
		add_action(
			'plugins_loaded',
			function () use ( $that ) {
				$that->add_acf_option_to_mappings_ui();
			}
		);

	}

	private function add_acf_option_to_mappings_ui() {
		// Bail out if ACF is not available.
		if ( ! function_exists( 'acf_get_field_groups' ) ) {
			return array();
		}

		add_filter( 'wl_mappings_field_types', array( $this, 'wl_mappings_field_types' ) );
	}

	/**
	 * Hook to `wl_mappings_field_types` to declare support for ACF fields.
	 *
	 * @param array $field_types An array of field types.
	 *
	 * @return array The array with our ACF declaration.
	 */
	public function wl_mappings_field_types( $field_types ) {

		$field_types[] = array(
			'field_type' => 'acf',
			'label'      => __( 'ACF', 'wordlift' ),
			'value'      => self::get_acf_options(),
		);

		return $field_types;
	}

	/**
	 * Returns array of acf options.
	 *
	 * The array is composed by all the ACF groups and their fields hierarchy.
	 *
	 * @return array ACF options array.
	 */
	private static function get_acf_options() {

		$acf_options = array();

		// Get all the ACF field groups.
		$field_groups = acf_get_field_groups();

		foreach ( $field_groups as $field_group ) {
			$group_name = $field_group['title'];
			$group_key  = $field_group['key'];

			$group_fields = acf_get_fields( $group_key );

			$group_options = array();
			foreach ( $group_fields as $group_field ) {
				$group_options[] = array(
					'label' => $group_field['label'],
					'value' => $group_field['key'],
				);
			}

			$acf_options[] = array(
				'group_name'    => $group_name,
				'group_options' => $group_options,
			);
		}

		return $acf_options;
	}

}
