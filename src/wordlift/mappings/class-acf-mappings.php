<?php
// @@todo add the class description and comments.

namespace Wordlift\Mappings;

class Acf_Mappings {

	public function __construct() {

		add_filter( 'wl_mappings_field_types', array( $this, 'field_types' ) );

	}

	public function field_types( $field_types ) {

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
	 * @return array Acf options Array.
	 */
	private static function get_acf_options() {

		// Bail out if ACF is not available.
		if ( ! function_exists( 'acf_get_field_groups' ) ) {
			return array();
		}

		$acf_options = array();

		$field_groups = acf_get_field_groups();

		foreach ( $field_groups as $field_group ) {
			$group_name    = $field_group['title'];
			$group_key     = $field_group['key'];
			$group_fields  = acf_get_fields( $group_key );
			$group_options = array();
			foreach ( $group_fields as $group_field ) {
				array_push(
					$group_options,
					array(
						'label' => $group_field['label'],
						'value' => $group_field['key'],
					)
				);
			}

			array_push(
				$acf_options,
				array(
					'group_name'    => $group_name,
					'group_options' => $group_options,
				)
			);
		}

		return $acf_options;
	}

}