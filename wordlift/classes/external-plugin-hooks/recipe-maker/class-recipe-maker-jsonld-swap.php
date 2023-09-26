<?php
/**
 * @since 3.35.4
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

		add_filter( 'wprm_recipe_metadata', array( $this, 'alter_jsonld' ), 10 );
	}

	/**
	 * Remove recipe maker jsonld or add mentions based on the integration.
	 *
	 * @param $metadata
	 * @param $recipe
	 *
	 * @return array
	 */
	public function alter_jsonld( $metadata ) {

		// If this filter is invoked on any other page except post, then we should not modify the jsonld.
		if ( ! get_post() instanceof \WP_Post ) {
			return $metadata;
		}

		$post_id = get_the_ID();

		/**
		 * We don't print our jsonld when the page has recipe maker, we enhance the recipe maker jsonld
		 * by adding only the `mentions` property.
		 */
		add_filter( 'wl_jsonld_enabled', '__return_false' );

		$post_jsonld = $this->jsonld_service->get_jsonld( false, $post_id, Jsonld_Context_Enum::PAGE );

		if ( 0 === count( $post_jsonld ) || ! array_key_exists( 'mentions', $post_jsonld[0] )
			 || 0 === count( $post_jsonld[0]['mentions'] ) ) {
			// Even if we don't have mentions return the metadata.
			return $metadata;
		}

		$metadata['mentions'] = $post_jsonld[0]['mentions'];

		return $metadata;
	}

}
