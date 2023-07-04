<?php

namespace Wordlift\Task\Background;

class Background_Task_Started_State extends Abstract_Background_Task_State {

	/**
	 * @var Background_Task
	 */
	private $context;

	private $batch_size = 5;

	/**
	 * @var Task
	 */
	private $task;

	/**
	 * Back_Started_State constructor.
	 *
	 * @param Background_Task $context
	 */
	public function __construct( $context, $task ) {
		parent::__construct( $context, Background_Task::STATE_STARTED );

		$this->context = $context;
		$this->task    = $task;
	}

	public function enter() {

		$count = $this->task->starting();

		update_option( $this->context->get_option_prefix() . 'count', $count, true );
		update_option( $this->context->get_option_prefix() . 'offset', 0, true );
		update_option( $this->context->get_option_prefix() . 'started', time(), true );
		update_option( $this->context->get_option_prefix() . 'updated', time(), true );

		$this->context->set_state( Background_Task::STATE_STARTED );

		$this->resume();
	}

	public function resume() {
		$this->context->push_to_queue( true );
		$this->context->save()->dispatch();
	}

	public function leave() {
		$this->context->set_state( null );
	}

	public function task( $value ) {
		$offset = get_option( $this->context->get_option_prefix() . 'offset' );
		$count  = get_option( $this->context->get_option_prefix() . 'count' );

		$this->task->tick(
			$value,
			array(
				'offset'     => $offset,
				'count'      => $count,
				'batch_size' => $this->batch_size,
			)
		);

		update_option( $this->context->get_option_prefix() . 'updated', time(), true );

		// Increase the offset.
		if ( ( $offset + $this->batch_size ) < $count ) {
			update_option( $this->context->get_option_prefix() . 'offset', $offset + $this->batch_size, true );

			return true;
		}

		// Stop processing.
		$this->context->stop();

		return false;
	}

}
