<?php

namespace Wordlift\Task\Background\Action_Scheduler;

use Wordlift\Task\Background\Background_Task_Info;
use Wordlift\Task\Background\Background_Task_State;

abstract class State implements Background_Task_State {

	/**
	 * @var string
	 */
	private $option_prefix;

	/**
	 * @var string
	 */
	private $state;

	public function __construct( $option_prefix, $state ) {
		$this->option_prefix = $option_prefix;
		$this->state         = $state;
	}

	public function get_info() {
		$offset      = get_option( $this->option_prefix . 'offset' );
		$count       = get_option( $this->option_prefix . 'count', array( 0 ) );
		$started     = get_option( $this->option_prefix . 'started' );
		$last_update = get_option( $this->option_prefix . 'updated' );

		return new Background_Task_Info( $this->state, $started, $offset, $count, $last_update );
	}

	public function resume() {
		// TODO: Implement resume() method.
	}
}
