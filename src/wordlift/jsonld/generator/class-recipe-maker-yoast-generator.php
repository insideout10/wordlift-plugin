<?php

namespace Wordlift\Jsonld\Generator;

/**
 * This generator is triggered when we have recipe maker and yoast plugins active and recipe maker yoast
 * integration is turned on and when the post atleast have a single recipe on the post.
 */
class Recipe_Maker_Yoast_Generator implements Generator {

	function generate() {

		// Dont generate jsonld here, we just need to add mentions to the yoast jsonld.

	}
}