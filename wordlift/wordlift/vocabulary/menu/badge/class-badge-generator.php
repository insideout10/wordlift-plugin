<?php

namespace Wordlift\Vocabulary\Menu\Badge;

/**
 * Class Badge_Generator
 *
 * @since 3.30.0
 * @package Wordlift\Vocabulary\Menu\Badge
 */
class Badge_Generator {

	/**
	 * Returns the term count which needs to be shown on ui.
	 *
	 * @param $number
	 *
	 * @return int
	 */
	public static function get_ui_count( $number ) {

		$number = (int) $number;

		if ( $number < 100 ) {
			return $number;
		}

		return 100;
	}

	public static function generate_html( $number ) {
		$count_string = self::get_formatted_count_string( $number );
		return "<span class=\"wl-admin-menu-badge\">$count_string</span>";
	}

	/**
	 * @param $number
	 *
	 * @return string
	 */
	public static function get_formatted_count_string( $number ) {
		$round = self::get_ui_count( $number );

		return $round < 100 ? "$round" : "$round+";
	}

}
