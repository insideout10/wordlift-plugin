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
 * @package Wordlift\Mappings
 *
 */
class Taxonomy_Option {

	public function add_taxonomy_option() {
		$this->add_rule_field_one_option();
	}

	private function add_rule_field_one_option() {
		add_filter( 'wl_mappings_rule_field_one_options', function ( $rule_field_one_options ) {

			$rule_field_one_options[] = array(
				'label' => __( 'Taxonomy', 'wordlift' ),
				'value' => 'taxonomy'
			);

			return $rule_field_one_options;

		} );
	}

}
