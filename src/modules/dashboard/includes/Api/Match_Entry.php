<?php

namespace Wordlift\Modules\Dashboard\Api;

use Wordlift\Assertions;

class Match_Entry {

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


	public function serialize() {
		return array(
			'id'           => $this->id,
			'name'         => $this->name,
			'match_id'     => $this->match_id,
			'match_jsonld' => $this->match_jsonld,
			'match_name'   => $this->get_name(),
		);
	}

	/**
	 * @throws \Exception
	 */
	public static function from( $data ) {

		Assertions::array_key_exists( $data, 'match_id' );
		Assertions::array_key_exists( $data, 'match_jsonld' );
		Assertions::array_key_exists( $data, 'name' );
		Assertions::array_key_exists( $data, 'id' );
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


}
