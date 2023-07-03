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
	 * @param mixed  $value The value to test.
	 * @param string $type The expected type.
	 *
	 * @throws Exception when an error occurs.
	 */
	public static function assert_of_type( $value, $type ) {

		// Check for nulls.
		if ( null === $value ) {
			throw new Exception( 'Value can`t be null.' );
		}

		// Check for type.
		if ( get_class( $value ) !== $type ) {
			throw new Exception( "Value must be a $type." );
		}

	}

	/**
	 * @throws Exception when the argument is not a string.
	 */
	public static function is_string( $arg, $message = 'Value must be a string' ) {
		if ( ! is_string( $arg ) ) {
			throw new Exception( $message );
		}
	}

	/**
	 * @throws Exception when actual doesn't match expected.
	 */
	public static function equals( $actual, $expected, $message = 'Values do not match' ) {
		if ( $actual !== $expected ) {
			throw new Exception( $message );
		}
	}

	/**
	 * @throws Exception when actual doesn't match expected.
	 */
	public static function array_key_exists( $arr, $key ) {
		if ( ! array_key_exists( $key, $arr ) ) {
			throw new Exception( "The key {$key} doesn't exist in array" );
		}
	}

	/**
	 * @throws Exception when the value doesn't match the pattern.
	 */
	public static function matches( $value, $pattern, $message = "Value doesn't match" ) {
		if ( 1 !== preg_match( $pattern, $value ) ) {
			throw new Exception( $message );
		}

	}

	/**
	 * @throws Exception when the value doesn't start with the provided scope.
	 */
	public static function starts_with( $value, $scope, $message = "Value doesn't start with provided scope" ) {
		if ( 0 !== strpos( $value, $scope ) ) {
			throw new Exception( $message );
		}
	}

	/**
	 * @throws Exception when the value is not of the specified type.
	 */
	public static function is_a( $value, $type, $message = 'Value is not of the required type' ) {
		if ( ! is_a( $value, $type ) ) {
			throw new Exception( $message );
		}
	}

	public static function is_set( $value, $message = 'Value is not set' ) {
		if ( ! isset( $value ) ) {
			throw new Exception( $message );
		}
	}

	public static function not_empty( $value, $message = "Value can't be empty" ) {
		if ( empty( $value ) ) {
			throw new Exception( $message );
		}
	}

	public static function is_array( $value, $message = 'Value should be array' ) {
		if ( ! is_array( $value ) ) {
			throw new Exception( $message );
		}
	}

	public static function is_numeric( $value, $message = 'Value should be numeric' ) {
		if ( ! is_numeric( $value ) ) {
			throw new Exception( $message );
		}
	}

}
