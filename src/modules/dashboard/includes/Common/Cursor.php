<?php

namespace Wordlift\Modules\Dashboard\Common;

use Exception;

class Cursor implements \Serializable, \JsonSerializable {

	private $position;
	private $element;
	private $direction;

	/**
	 * @throws Exception when one of the parameters has an invalid value.
	 */
	public function __construct( $position, $element, $direction ) {
		$this->set_position( $position );
		$this->set_element( $element );
		$this->set_direction( $direction );
	}

	public function get_position() {
		return $this->position;
	}

	public function set_position( $value ) {
		$this->position = $value;
	}

	public function get_element() {
		return $this->element;
	}

	/**
	 * @throws Exception when one of the parameters has an invalid value.
	 */
	public function set_element( $value ) {
		if ( ! in_array( $value, array( 'INCLUDED', 'EXCLUDED' ), true ) ) {
			throw new Exception( "Invalid value, only 'INCLUDED' or 'EXCLUDED' accepted." );
		}

		$this->element = $value;
	}

	public function get_direction() {
		return $this->direction;
	}

	/**
	 * @throws Exception when one of the parameters has an invalid value.
	 */
	public function set_direction( $value ) {
		if ( ! in_array( $value, array( 'ASCENDING', 'DESCENDING' ), true ) ) {
			throw new Exception( "Invalid value, only 'ASCENDING' or 'DESCENDING' accepted." );
		}

		$this->direction = $value;
	}

	public function to_base64_string() {
		// The requirement for cursors is to be obfuscated.
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		return base64_encode( wp_json_encode( $this ) );
	}

	public static function from_base64_string( $value ) {
		if ( ! is_string( $value ) ) {
			return self::empty_cursor();
		}

		try {
			// The requirement for cursors is to be obfuscated.
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			$json_string = base64_decode( $value, true );
			$json_array  = json_decode( $json_string, true );

			return self::from_array( $json_array );
		} catch ( Exception $e ) {
			return self::empty_cursor();
		}
	}

	/**
	 * @throws Exception when one of the parameters is not accepted.
	 */
	public static function from_array( $data ) {
		return new Cursor( $data['position'], $data['element'], $data['direction'] );
	}

	public static function empty_cursor() {
		return new self( null, 'INCLUDED', 'ASCENDING' );
	}

	public function __serialize() {
		return array(
			'position'  => $this->position,
			'element'   => $this->element,
			'direction' => $this->direction,
		);
	}

	/**
	 * Controls how the object is represented during PHP serialization.
	 *
	 * @return string The PHP serialized representation of the object.
	 */
	public function serialize() {
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
		return serialize( $this->__serialize() );
	}

	/**
	 * @throws Exception when unserialization fails.
	 */
	public function unserialize( $data ) {
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
		$this->__unserialize( unserialize( $data ) );
	}

	/**
	 * @throws Exception when unserialization fails.
	 */
	public function __unserialize( array $data ) {
		$this->set_position( $data['position'] );
		$this->set_element( $data['element'] );
		$this->set_direction( $data['direction'] );
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		return array(
			'position'  => $this->position,
			'element'   => $this->element,
			'direction' => $this->direction,
		);
	}

}
