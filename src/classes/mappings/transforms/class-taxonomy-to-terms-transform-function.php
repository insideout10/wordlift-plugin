<?php
/**
 * This transform function will take the provided taxonomy name and print its terms.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.25.0
 * @package Wordlift\Mappings\Transforms
 */

namespace Wordlift\Mappings\Transforms;

use Wordlift\Mappings\Mappings_Transform_Function;

/**
 * Class Taxonomy_To_Terms_Transform_Function.
 *
 * @package Wordlift\Mappings\Transforms
 */
class Taxonomy_To_Terms_Transform_Function implements Mappings_Transform_Function {

	/**
	 * Taxonomy_To_Terms_Transform_Function constructor.
	 */
	public function __construct() {

		add_filter( 'wl_mappings_transformation_functions', array( $this, 'wl_mappings_transformation_functions' ) );

	}

	/**
	 * Hook to add ourselves to the list of available transform functions.
	 *
	 * @param Mappings_Transform_Function[] $value An array of {@link Mappings_Transform_Function}s.
	 *
	 * @return Mappings_Transform_Function[] An updated array with ourselves too.
	 */
	public function wl_mappings_transformation_functions( $value ) {

		$value[] = $this;

		return $value;
	}

	/**
	 * @inheritDoc
	 */
	public function get_name() {

		return 'taxonomy_to_terms';
	}

	/**
	 * @inheritDoc
	 */
	public function get_label() {

		return __( 'Taxonomy to Terms', 'wordlift' );
	}

	/**
	 * @inheritDoc
	 */
	public function transform_data( $data, $jsonld, &$references, $post_id ) {

		$terms = wp_get_object_terms( $post_id, $data, array( 'fields' => 'names' ) );

		if ( ! is_array( $terms ) || empty( $terms ) ) {
			return null;
		}

		return $terms;
	}

}
