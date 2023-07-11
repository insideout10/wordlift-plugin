<?php

namespace Wordlift\Task\Background;

use Wordlift\Task\Task;
use Wordlift_Plugin_WP_Background_Process;

class Background_Task extends Wordlift_Plugin_WP_Background_Process implements Background_Route_Task {

	const STATE_STARTED = 'started';
	const STATE_STOPPED = 'stopped';

	/**
	 * The current state of the task, started or stopped.
	 *
	 * @var Background_Task_State $state
	 */
	private $state;

	/**
	 * The actual task.
	 *
	 * @var Task $task
	 */
	private $task;

	/**
	 * The prefix to store the state and other information in WP's options table, determined at instantiation.
	 *
	 * @var string $option_prefix
	 */
	private $option_prefix;

	/**
	 * @param Task $task
	 */
	public function __construct( $task ) {
		$this->action        = $task->get_id();
		$this->option_prefix = "_{$this->action}_";

		parent::__construct();

		// Set the current state.
		if ( self::STATE_STARTED === $this->get_state() ) {
			$this->state = new Background_Task_Started_State( $this, $task );
		} else {
			$this->state = new Background_Task_Stopped_State( $this );
		}

		$this->task = $task;
	}

	public static function create( $task ) {
		return new self( $task );
	}

	public function get_option_prefix() {
		return $this->option_prefix;
	}

	/**
	 * This function is called:
	 *  - To start a new Synchronization, by passing a {@link Sync_Start_Message} instance.
	 *  - To synchronize a post, by passing a numeric ID.
	 *
	 * This function returns the parameter for the next call or NULL if there are no more posts to process.
	 *
	 * @param mixed $item Queue item to iterate over.
	 *
	 * @return int[]|false The next post IDs or false if there are no more.
	 */
	protected function task( $item ) {

		return $this->state->task( $item );
	}

	/**
	 * Transition to the started state.
	 */
	public function start() {
		$this->state->leave();
		$this->state = new Background_Task_Started_State( $this, $this->task );
		$this->state->enter();
	}

	/**
	 * Transition to the stopped state.
	 */
	public function stop() {
		$this->state->leave();
		$this->state = new Background_Task_Stopped_State( $this );
		$this->state->enter();
	}

	public function resume() {
		$this->state->resume();
	}

	/**
	 * Get the current state.
	 *
	 * @return string Either self::STARTED_STATE or self::STOPPED_STATE (default).
	 */
	public function get_state() {
		return get_option( $this->option_prefix . 'state', self::STATE_STOPPED );
	}

	/**
	 * Persist the current state.
	 *
	 * @param string $value
	 *
	 * @return bool
	 */
	public function set_state( $value ) {
		return null === $value
			? delete_option( $this->option_prefix . 'state' )
			: update_option( $this->option_prefix . 'state', $value, true );
	}

	public function get_info() {
		return $this->state->get_info();
	}

}
