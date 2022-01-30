<?php

namespace Wordlift\Task\Background;

class Background_Task_Stopped_State extends Abstract_Background_Task_State {

	/**
	 * @var Background_Task
	 */
	private $context;

	function __construct( $context ) {
		parent::__construct( $context, Background_Task::STATE_STOPPED );

		$this->context = $context;
	}

	function enter() {
		$this->context->set_state( Background_Task::STATE_STOPPED );
	}

	function leave() {
		$this->context->set_state( null );
	}

	function task( $value ) {

		$this->context->cancel_process();

		return false;
	}

}
