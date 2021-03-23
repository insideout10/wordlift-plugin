<?php

namespace Wordlift\Vocabulary\Menu\Badge;


/**
 * Class Badge_Generator
 * @since 3.30.0
 * @package Wordlift\Vocabulary\Menu\Badge
 */
class Badge_Generator {

	/**
	 * @param $number
	 * @return int
	 */
	public static function round_to_nearest_hundred( $number ) {

		if ( $number < 100 ) {
			return $number;
		}

		if ( $number % 100 === 0) {
			return $number;
		}



	}


}