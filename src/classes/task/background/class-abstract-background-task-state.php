<?php

namespace Wordlift\Task\Background;

abstract class Abstract_Background_Task_State implements Background_Task_State {

	/**
	 * @var Background_Task_State
	 */
	private $state;

	/**
	 * @var Background_Task
	 */
	private $context;

	public function __construct( $context, $state ) {
		$this->state   = $state;
		$this->context = $context;
	}

	public function get_info() {
		$started     = get_option( $this->context->get_option_prefix() . 'started' );
		$offset      = get_option( $this->context->get_option_prefix() . 'offset' );
		$count       = get_option( $this->context->get_option_prefix() . 'count', array( 0 ) );
		$last_update = get_option( $this->context->get_option_prefix() . 'updated' );

		return new Background_Task_Info( $this->state, $started, $offset, $count, $last_update );
	}

	public function resume() {
		// do nothing.
	}

}
