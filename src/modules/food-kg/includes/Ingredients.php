<?php

namespace Wordlift\Modules\Food_Kg;

use Countable;
use Iterator;

class Ingredients implements Iterator, Countable {

	/**
	 * @var string[]
	 */
	private $keys;

	/**
	 * @var string[] $data
	 */
	private $data;

	/**
	 * @var int $i
	 */
	private $i;

	/**
	 * @param string $body
	 *
	 * @return Ingredients
	 */
	public static function create_from_string( $body ) {
		$lines = preg_split( '/\R/', $body );
		$data  = array();
		$keys  = array();
		foreach ( $lines as $line ) {
			if ( preg_match( '@(^.+)\t(\{.*})$@', $line, $matches ) ) {
				$keys[] = $matches[1];
				$data[] = $matches[2];
			}
		}

		return new self( $keys, $data );
	}

	/**
	 * @param string[] $keys
	 * @param string[] $data
	 */
	private function __construct( $keys, $data ) {
		$this->keys = $keys;
		$this->data = $data;
		$this->i    = 0;
	}

	/**
	 * @return string
	 */
	public function current() {
		return $this->data[ $this->i ];
	}

	public function next() {
		++ $this->i;
	}

	public function key() {
		return $this->keys[ $this->i ];
	}

	/**
	 * @return bool
	 */
	public function valid() {
		return isset( $this->data[ $this->i ] );
	}

	/**
	 * @return void
	 */
	public function rewind() {
		$this->i = 0;
	}

	/**
	 * @return int
	 */
	public function count() {
		return count( $this->data );
	}

}
