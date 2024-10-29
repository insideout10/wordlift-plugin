<?php

namespace Wordlift\Modules\Dashboard\Match;

use JsonSerializable;
use Serializable;
use Wordlift\Assertions;
class Match_Entry implements Serializable, JsonSerializable {

	private $id;
	private $match_jsonld;

	private $name;
	private $match_id;

	/**
	 * @param $id
	 * @param $match_jsonld
	 * @param $name
	 * @param $match_id
	 */
	public function __construct( $id, $name, $match_jsonld, $match_id ) {
		$this->id           = $id;
		$this->match_jsonld = $match_jsonld;
		$this->name         = $name;
		$this->match_id     = $match_id;
	}

	/**
	 * @param $data
	 *
	 * @return void
	 * @throws \Exception Throw exception if the validation fails.
	 */
	public static function validate( $data ) {
		Assertions::array_key_exists( $data, 'match_id' );
		Assertions::array_key_exists( $data, 'match_jsonld' );
		Assertions::array_key_exists( $data, 'name' );
		Assertions::array_key_exists( $data, 'id' );
	}

	public function __serialize() {
		return array(
			'id'           => $this->id,

			'match_jsonld' => $this->match_jsonld,
			'name'         => $this->name,
			'match_id'     => $this->match_id,
			'match_name'   => $this->get_name(),
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

	public function unserialize( $data ) {
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
		$this->__unserialize( unserialize( $data ) );
	}

	public function __unserialize( array $data ) {
		self::validate( $data );
		$this->id           = $data['id'];
		$this->name         = $data['name'];
		$this->match_jsonld = $data['match_jsonld'];
		$this->match_id     = $data['match_id'];
	}

	public static function from( $data ) {

		self::validate( $data );

		return new Match_Entry(
			$data['id'],
			$data['name'],
			$data['match_jsonld'],
			$data['match_id']
		);

	}

	/**
	 * @return void|string
	 */
	private function get_name() {
		$data = json_decode( $this->match_jsonld, true );
		if ( ! $data || ! array_key_exists( 'name', $data ) ) {
			return null;
		}
		return $data['name'];
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		return array(
			'id'           => $this->id,
			'match_jsonld' => $this->match_jsonld,
			'name'         => $this->name,
			'match_id'     => $this->match_id,
			'match_name'   => $this->get_name(),
		);
	}

}
