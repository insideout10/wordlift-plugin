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

class Assertions {

	/**
	 * Asserts that the provided value is of the specified type.
	 *
	 * @param mixed $value The value to test.
	 * @param string $type The expected type.
	 *
	 * @throws Exception
	 */
	public static function assert_of_type( $value, $type ) {

		// Check for nulls.
		if ( null === $value ) {
			throw new Exception( 'Value can`t be null.' );
		}

		// Check for type.
		if ( get_class( $value ) !== $type ) {
			echo "Value must be a $type.";
			throw new Exception( "Value must be a $type." );
		}

	}

}