<?php
/**
 * This file provides a set of methods to check values.
 *
 * The main aim is to provide helpful methods to constructors and methods to validate the incoming arguments.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @package Wordlift
 * @since 3.26.0
 */

namespace Wordlift;

use Exception;

class Escape {

	/**
	 * Escapes the php array in to a string so that it can be used in the IN CLAUSE.
	 * For example, WHERE NOT IN ({Escape::sql_array($arr)})
	 *
	 * @throws Exception When supplied argument is not an array, throw exception.
	 */
	public static function sql_array( $arr ) {
		Assertions::is_array( $arr );
		$arr = array_map(
			function ( $item ) {
				Assertions::is_string( $item );
				return "'" . esc_sql( $item ) . "'";
			},
			$arr
		);

		return implode( ',', $arr );
	}

}
