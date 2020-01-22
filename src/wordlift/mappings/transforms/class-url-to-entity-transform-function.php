<?php
/**
 * This transform function will take the provided URL and expand it to an entity if it exists.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.25.0
 * @package Wordlift\Mappings\Transforms
 */

namespace Wordlift\Mappings\Transforms;

use Wordlift\Mappings\Mappings_Transform_Function;

class Url_To_Entity_Transform_Function implements Mappings_Transform_Function {

	/**
	 * Url_To_Entity_Transform_Function constructor.
	 */
	public function __construct() {

		add_filter( 'wl_mappings_transformation_functions', array( $this, 'wl_mappings_transformation_functions' ) );

	}

	public function wl_mappings_transformation_functions( $value ) {

		$value[] = $this;

		return $value;
	}

	/**
	 * @inheritDoc
	 */
	public function get_name() {

		return 'url_to_entity';
	}

	/**
	 * @inheritDoc
	 */
	public function get_label() {

		return __( 'URL to Entity', 'wordlift' );
	}

	/**
	 * @inheritDoc
	 */
	public function transform_data( $data, $jsonld, &$references ) {
		// TODO: Implement transform_data() method.

		$references[] = 4;

		return $data;
	}

}
