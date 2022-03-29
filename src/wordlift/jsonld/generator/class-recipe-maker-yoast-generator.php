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
		// we should not print our jsonld when yoast + recipe maker is active
		// and recipe maker integration is turned on.
	}


}