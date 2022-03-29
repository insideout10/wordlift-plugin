<?php

namespace Wordlift\Jsonld\Generator;

use Wordlift\Jsonld\Jsonld_Context_Enum;

/**
 * This generator is triggered when we have recipe maker and yoast plugins active and recipe maker yoast
 * integration is turned on and when the post atleast have a single recipe on the post.
 */
class Recipe_Maker_Yoast_Generator implements Generator {

	/**
	 * @var \Wordlift_Jsonld_Service
	 */
	private $jsonld_service;

	public function __construct( $jsonld_service ) {
		$this->jsonld_service = $jsonld_service;
	}

	function generate() {

		// Dont generate jsonld here, we just need to add mentions to the yoast jsonld.
		add_filter( 'wprm_recipe_metadata', array( $this, 'add_mentions' ), 10, 2 );
	}

	public function add_mentions( $metadata, $recipe ) {

		if ( ! class_exists( '\WPRM_Recipe' ) ) {
			return $metadata;
		}

		/**
		 * we dont check for is_singular here, the validations are done inside the generator
		 * factory.
		 */

		$jsonld = $this->jsonld_service->get_jsonld(
			false,
			get_the_ID(),
			Jsonld_Context_Enum::PAGE
		);

		if ( 0 === count( $jsonld )
		     || ! array_key_exists( 'mentions', $jsonld[0] )
		     || 0 === count( $jsonld[0]['mentions'] ) ) {
			return $metadata;
		}


		$metadata['mentions'] = $jsonld[0]['mentions'];

		return $metadata;
	}

	public function get_mentions() {

	}
}