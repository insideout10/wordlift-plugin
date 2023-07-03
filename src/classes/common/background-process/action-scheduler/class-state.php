<?php

namespace Wordlift\Common\Background_Process\Action_Scheduler;

class State {

	private $args;
	private $has_next;

	public function __construct( $has_next, $args ) {
		$this->has_next = $has_next;
		$this->args     = $args;
	}

	public function has_next() {
		return $this->has_next;
	}

	public function get_args() {
		return $this->args;
	}

	public static function complete() {
		return new self( false, array() );
	}

	public static function items_in_queue( $args = array() ) {
		return new self( true, $args );
	}

}
