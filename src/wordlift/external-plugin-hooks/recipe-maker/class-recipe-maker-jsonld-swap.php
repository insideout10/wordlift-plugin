<?php
/**
 * @since 3.35.3
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\External_Plugin_Hooks\Recipe_Maker;

use Wordlift\Jsonld\Jsonld_Context_Enum;

class Recipe_Maker_Jsonld_Swap {


	/**
	 * @var Recipe_Maker_Validation_Service
	 */
	private $validation_service;
	/**
	 * @var \Wordlift_Jsonld_Service
	 */
	private $jsonld_service;

	/**
	 * @param $validation_service Recipe_Maker_Validation_Service
	 */
	public function __construct( $validation_service, $jsonld_service ) {

		$this->validation_service = $validation_service;
		$this->jsonld_service     = $jsonld_service;

		add_filter( 'wprm_recipe_metadata', array( $this, 'alter_jsonld' ), 10, 2 );
	}

	/**
	 * Remove recipe maker jsonld or add mentions based on the integration.
	 *
	 * @param $metadata
	 * @param $recipe
	 *
	 * @return array
	 */
	public function alter_jsonld( $metadata, $recipe ) {

		$post_id = get_the_ID();

		if ( ! $post_id ) {
			return array();
		}

		// if yoast + recipe maker integration is on, then we should add mentions to jsonld.
		if ( ! $this->validation_service->can_integrate_with_yoast_jsonld( $post_id ) ) {
			return array();
		}

		$post_jsonld = $this->jsonld_service->get_jsonld( false, $post_id, Jsonld_Context_Enum::PAGE );

		if ( 0 === count( $post_jsonld ) || ! array_key_exists( 'mentions', $post_jsonld[0] )
		     || 0 === count( $post_jsonld[0]['mentions'] ) ) {
			return array();
		}

		$metadata['mentions'] = $post_jsonld[0]['mentions'];

		return $metadata;
	}


}