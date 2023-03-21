<?php

namespace Wordlift\Modules\Dashboard\Synchronization;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use JsonSerializable;
use Serializable;

class Synchronization implements Serializable, JsonSerializable {

	private $created_at;
	private $modified_at;
	private $started_at;
	private $stopped_at;
	private $total;
	private $offset;

	/**
	 * @throws Exception when the created at date can't be set.
	 */
	public function __construct() {
		$this->set_created_at( new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) ) );
		$this->set_offset( 0 );
	}

	/**
	 * @return mixed
	 */
	public function get_created_at() {
		return $this->created_at;
	}

	/**
	 * @param mixed $created_at
	 */
	public function set_created_at( $created_at ) {
		$this->created_at = $created_at;
	}

	/**
	 * @return mixed
	 */
	public function get_modified_at() {
		return $this->modified_at;
	}

	/**
	 * @param mixed $modified_at
	 */
	public function set_modified_at( $modified_at ) {
		$this->modified_at = $modified_at;
	}

	/**
	 * @return mixed
	 */
	public function get_started_at() {
		return $this->started_at;
	}

	/**
	 * @param mixed $started_at
	 *
	 * @throws Exception when the modified at date cannot be set.
	 */
	public function set_started_at( $started_at ) {
		$this->started_at = $started_at;
		$this->set_modified_at( new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) ) );
	}

	/**
	 * @return mixed
	 */
	public function get_stopped_at() {
		return $this->stopped_at;
	}

	/**
	 * @param mixed $stopped_at
	 *
	 * @throws Exception when the modified at date cannot be set.
	 */
	public function set_stopped_at( $stopped_at ) {
		$this->stopped_at = $stopped_at;
		$this->set_modified_at( new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) ) );
	}

	/**
	 * @return int
	 */
	public function get_total() {
		return $this->total;
	}

	/**
	 * @param int $total
	 *
	 * @throws Exception when the modified at date cannot be set.
	 */
	public function set_total( $total ) {
		$this->total = $total;
		$this->set_modified_at( new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) ) );
	}

	/**
	 * @return mixed
	 */
	public function get_offset() {
		return $this->offset;
	}

	/**
	 * @param mixed $offset
	 *
	 * @throws Exception when the modified at date cannot be set.
	 */
	public function set_offset( $offset ) {
		$this->offset = $offset;
		$this->set_modified_at( new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) ) );
	}

	public function is_running() {
		return isset( $this->started_at ) && ! isset( $this->stopped_at );
	}

	public function __serialize() {
		return array(
			'created_at'  => $this->created_at,
			'modified_at' => $this->modified_at,
			'started_at'  => $this->started_at,
			'stopped_at'  => $this->stopped_at,
			'total'       => $this->total,
			'offset'      => $this->offset,
		);
	}

	/**
	 * @throws Exception when dates cannot be set.
	 */
	public function __unserialize( array $data ) {
		if ( isset( $data['created_at'] ) ) {
			$this->set_created_at( $data['created_at'] );
		}
		if ( isset( $data['modified_at'] ) ) {
			$this->set_modified_at( $data['modified_at'] );
		}
		if ( isset( $data['started_at'] ) ) {
			$this->set_started_at( $data['started_at'] );
		}
		if ( isset( $data['stopped_at'] ) ) {
			$this->set_stopped_at( $data['stopped_at'] );
		}
		if ( isset( $data['total'] ) ) {
			$this->set_total( $data['total'] );
		}
		if ( isset( $data['offset'] ) ) {
			$this->set_offset( $data['offset'] );
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

	public function jsonSerialize() {
		return array(
			'created_at'  => is_a( $this->created_at, 'DateTimeInterface' )
				? date_format( $this->created_at, 'Y-m-d\TH:i:s\Z' ) : null,
			'modified_at' => is_a( $this->modified_at, 'DateTimeInterface' )
				? date_format( $this->modified_at, 'Y-m-d\TH:i:s\Z' ) : null,
			'started_at'  => is_a( $this->started_at, 'DateTimeInterface' )
				? date_format( $this->started_at, 'Y-m-d\TH:i:s\Z' ) : null,
			'stopped_at'  => is_a( $this->stopped_at, 'DateTimeInterface' )
				? date_format( $this->stopped_at, 'Y-m-d\TH:i:s\Z' ) : null,
			'total'       => $this->total,
			'offset'      => $this->offset,
			'is_running'  => $this->is_running(),
		);
	}

}
