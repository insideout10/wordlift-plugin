<?php

namespace Wordlift\Modules\Common\Synchronization;

class Runner_State implements \Serializable {

	private $total;
	private $offset;
	private $last_id;
	private $started_at;
	private $stopped_at;
	private $updated_at;

	/**
	 * @return int
	 */
	public function get_total() {
		return $this->total;
	}

	/**
	 * @param int $total
	 */
	public function set_total( $total ) {
		$this->total = $total;
	}

	/**
	 * @return int
	 */
	public function get_offset() {
		return $this->offset;
	}

	/**
	 * @param int $offset
	 */
	public function set_offset( $offset ) {
		$this->offset = $offset;
	}

	/**
	 * @return mixed
	 */
	public function get_last_id() {
		return $this->last_id;
	}

	/**
	 * @param mixed $last_id
	 */
	public function set_last_id( $last_id ) {
		$this->last_id = $last_id;
	}

	/**
	 * @return mixed
	 */
	public function get_started_at() {
		return $this->started_at;
	}

	/**
	 * @param mixed $started_at
	 */
	public function set_started_at( $started_at ) {
		$this->started_at = $started_at;
	}

	/**
	 * @return mixed
	 */
	public function get_stopped_at() {
		return $this->stopped_at;
	}

	/**
	 * @param mixed $stopped_at
	 */
	public function set_stopped_at( $stopped_at ) {
		$this->stopped_at = $stopped_at;
	}

	/**
	 * @return mixed
	 */
	public function get_updated_at() {
		return $this->updated_at;
	}

	/**
	 * @param mixed $updated_at
	 */
	public function set_updated_at( $updated_at ) {
		$this->updated_at = $updated_at;
	}

	public function is_running() {
		return isset( $this->started_at ) && ! isset( $this->stopped_at );
	}

	public function __serialize() {
		return array(
			'total'      => $this->total,
			'offset'     => $this->offset,
			'last_id'    => $this->last_id,
			'started_at' => $this->started_at,
			'stopped_at' => $this->stopped_at,
			'updated_at' => $this->updated_at,
		);
	}

	public function __unserialize( array $data ) {

		if ( isset( $data['total'] ) ) {
			$this->set_total( $data['total'] );
		}
		if ( isset( $data['offset'] ) ) {
			$this->set_offset( $data['offset'] );
		}
		if ( isset( $data['last_id'] ) ) {
			$this->set_last_id( $data['last_id'] );
		}
		if ( isset( $data['started_at'] ) ) {
			$this->set_started_at( $data['started_at'] );
		}
		if ( isset( $data['stopped_at'] ) ) {
			$this->set_stopped_at( $data['stopped_at'] );
		}
		if ( isset( $data['updated_at'] ) ) {
			$this->set_updated_at( $data['updated_at'] );
		}

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
	 * Controls how the object is reconstructed from a PHP serialized representation.
	 *
	 * @param string $data The PHP serialized representation of the object.
	 *
	 * @return void
	 */
	public function unserialize( $data ) {
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
		$this->__unserialize( unserialize( $data ) );
	}

}
