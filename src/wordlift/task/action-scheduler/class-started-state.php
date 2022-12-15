<?php

namespace Wordlift\Task\Background\Action_Scheduler;

use Wordlift\Task\Background\Background_Task;

class Started_State extends State {
	public function __construct( $option_prefix ) {
		parent::__construct( $option_prefix, Background_Task::STATE_STARTED );
	}

	public function enter() {
		// TODO: Implement enter() method.
	}

	public function leave() {
		// TODO: Implement leave() method.
	}

	public function task( $item ) {
		// TODO: Implement task() method.
	}
}
