<?php

namespace Wordlift\Vocabulary;

class Sync_State {

	public $started;
	public $index;
	public $count;
	public $last_update;
	public $state;

	/**
	 * Sync_Model constructor.
	 *
	 * @param $started
	 * @param $index
	 * @param $count
	 * @param $last_update
	 * @param $state
	 */
	public function __construct( $started, $index, $count, $last_update, $state ) {
		$this->started     = $started;
		$this->index       = $index;
		$this->count       = (int) $count;
		$this->last_update = $last_update;
		$this->state       = $state;
	}

	public function increment_index( $count ) {
		$this->index      += $count;
		$this->last_update = time();

		return $this;
	}

	public function set_state( $value ) {
		$this->state       = $value;
		$this->last_update = time();

		return $this;
	}

	public function get_array() {
		return array(
			'started'     => $this->started,
			'index'       => $this->index,
			'count'       => $this->count,
			'last_update' => $this->last_update,
			'state'       => $this->state,
		);
	}

	public static function unknown() {

		return new self( time(), 0, 0, time(), 'unknown' );
	}

}
