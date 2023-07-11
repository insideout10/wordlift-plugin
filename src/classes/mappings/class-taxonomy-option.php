<?php
/**
 * @since      3.27.0
 * @package    Wordlift
 * @subpackage Wordlift/Mappings
 */

namespace Wordlift\Mappings;

/**
 * This class adds the taxonomy option to the edit mappings screen.
 * Class Taxonomy_Option
 *
 * @package Wordlift\Mappings
 */
class Taxonomy_Option {

	/**
	 * This value is used in rule field one and rule field two
	 * options are linked to one by this value
	 */
	const PARENT_VALUE = 'taxonomy';

	public function add_taxonomy_option() {
		$this->add_rule_field_one_options();
		$this->add_rule_field_two_options();
	}

	private function add_rule_field_one_options() {

		add_filter(
			'wl_mappings_rule_field_one_options',
			function ( $rule_field_one_options ) {

				$rule_field_one_options[] = array(
					'label'      => __( 'Taxonomy', 'wordlift' ),
					'value'      => Taxonomy_Option::PARENT_VALUE,
					// Left empty since these values are provided locally, not needed to be fetched from
					// api.
					'api_source' => '',
				);

				return $rule_field_one_options;

			}
		);
	}

	private function add_rule_field_two_options() {

		add_filter(
			'wl_mappings_rule_field_two_options',
			function ( $rule_field_two_options ) {

				$taxonomies = get_object_taxonomies( 'post', 'objects' );

				$taxonomy_options = array();

				foreach ( $taxonomies as $item ) {
					/**
					 * $item Taxonomy
					 */
					$taxonomy_options[] = array(
						'label'        => $item->label,
						'value'        => $item->name,
						// The value of parent option on rule field one.
						'parent_value' => Taxonomy_Option::PARENT_VALUE,
					);
				}

				return array_merge( $rule_field_two_options, $taxonomy_options );

			}
		);

	}

}
