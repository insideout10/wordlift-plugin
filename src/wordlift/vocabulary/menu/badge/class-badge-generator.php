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
	 *
	 * @return int
	 */
	public static function round_to_nearest_hundred( $number ) {

		$number = (int) $number;

		if ( $number < 100 ) {
			return $number;
		}

		return floor( $number / 100 ) * 100;
	}

	public static function generate_html( $number ) {
		$round = self::round_to_nearest_hundred( $number );
		return "<span class=\"count-$round\"><span class=\"pending-term-count\">$round+</span></span>";
	}


}