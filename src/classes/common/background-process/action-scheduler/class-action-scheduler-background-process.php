<?php

namespace Wordlift\Common\Background_Process\Action_Scheduler;

abstract class Action_Scheduler_Background_Process {

	/**
	 * @var string Hook name used for invoking the task method.
	 */
	private $hook;
	/**
	 * @var string Group which would be used when
	 */
	private $group;

	public function __construct( $hook, $group ) {
		$this->hook  = $hook;
		$this->group = $group;
		add_action( $this->hook, array( $this, 'task' ) );
	}

	public function schedule( $args = array() ) {

		as_enqueue_async_action( $this->hook, $args, $this->group );

	}

	public function unschedule() {
		as_unschedule_all_actions( $this->hook );
	}

	public function task( $args = array() ) {
		$state = $this->do_task( $args );
		if ( $state->has_next() ) {
			$this->schedule( $state->get_args() );
		}
	}

	/**
	 * @param $args
	 *
	 * @return State The state object provides necessary information about
	 * whether to schedule next event or stop processing.
	 */
	abstract public function do_task( $args );

}
