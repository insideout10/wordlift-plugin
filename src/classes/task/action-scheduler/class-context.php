<?php

namespace Wordlift\Task\Action_Scheduler;

class Context {

	private $count;
	private $offset;
	private $started;
	private $updated;

	/**
	 * @param $count
	 * @param $offset
	 * @param $started
	 * @param $updated
	 */
	public function __construct( $count, $offset, $started, $updated ) {
		$this->count   = $count;
		$this->offset  = $offset;
		$this->started = $started;
		$this->updated = $updated;
	}

	/**
	 * @return int
	 */
	public function get_count() {
		return $this->count;
	}

	/**
	 * @param int $count
	 *
	 * @return Context
	 */
	public function set_count( $count ) {
		$this->count = $count;

		return $this;
	}

	/**
	 * @return int
	 */
	public function get_offset() {
		return $this->offset;
	}

	/**
	 * @param int $offset
	 *
	 * @return Context
	 */
	public function set_offset( $offset ) {
		$this->offset = $offset;

		return $this;
	}

	/**
	 * @return int
	 */
	public function get_started() {
		return $this->started;
	}

	/**
	 * @param int $started
	 *
	 * @return Context
	 */
	public function set_started( $started ) {
		$this->started = $started;

		return $this;
	}

	/**
	 * @return int
	 */
	public function get_updated() {
		return $this->updated;
	}

	/**
	 * @param int $updated
	 *
	 * @return Context
	 */
	public function set_updated( $updated ) {
		$this->updated = $updated;

		return $this;
	}

	public function get_data() {
		return array(
			'count'   => $this->count,
			'updated' => $this->updated,
			'started' => $this->started,
			'offset'  => $this->offset,
			'index'   => $this->offset,
		);
	}

	/**
	 * @param $count int
	 *
	 * @return Context
	 */
	public static function from( $count ) {
		return new self( $count, 0, time(), time() );
	}

	/**
	 * @param $data array
	 *
	 * @return Context
	 */
	public static function from_data( $data ) {
		return new self( $data['count'], $data['offset'], $data['started'], $data['updated'] );
	}

}
