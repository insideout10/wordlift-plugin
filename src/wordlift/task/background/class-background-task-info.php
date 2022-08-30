<?php

namespace Wordlift\Task\Background;

class Background_Task_Info {

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
	public function __construct( $state, $started = null, $index = null, $count = null, $last_update = null ) {
		$this->started     = $started;
		$this->index       = $index;
		$this->count       = (int) $count;
		$this->last_update = $last_update;
		$this->state       = $state;
	}

}
