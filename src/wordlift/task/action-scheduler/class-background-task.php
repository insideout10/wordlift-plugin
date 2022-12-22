<?php

namespace Wordlift\Task\Action_Scheduler;

use Wordlift\Common\Background_Process\Action_Scheduler\Action_Scheduler_Background_Process;
use Wordlift\Common\Background_Process\Action_Scheduler\State;
use Wordlift\Task\Background\Background_Route_Task;
use Wordlift\Task\Task;

class Background_Task extends Action_Scheduler_Background_Process implements Background_Route_Task {
	/**
	 * The option prefix to store state.
	 *
	 * @var string $option_prefix
	 */
	private $option_prefix;
	/**
	 * @var Task
	 */
	private $task;

	const STATE_STARTED = 'started';
	const STATE_STOPPED = 'stopped';
	/**
	 * @var int
	 */
	private $batch_size;

	public function __construct( $hook, $group, $task, $option_prefix, $batch_size = 5 ) {
		parent::__construct( $hook, $group );
		$this->task          = $task;
		$this->option_prefix = $option_prefix;
		$this->batch_size    = $batch_size;
	}

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function do_task( $args ) {
		if ( self::STATE_STOPPED === $this->get_process_state() ) {
			return State::complete();
		}
		$context = $this->get_context();
		$this->task->tick( null, $context->get_data() + array( 'batch_size' => $this->batch_size ) );

		if ( ( $context->get_count() - $context->get_offset() ) >= 0 ) {
			$context->set_offset( $context->get_offset() + $this->batch_size )->set_updated( time() );
			$this->set_info( $context );
			return State::items_in_queue();
		} else {
			$this->set_process_state( self::STATE_STOPPED );
			return State::complete();
		}

	}

	public function start() {
		$this->delete_info();
		$this->set_process_state( self::STATE_STARTED );
		$this->schedule();
	}

	public function stop() {
		$this->set_process_state( self::STATE_STOPPED );
		$this->unschedule();
	}

	public function resume() {
		$this->set_process_state( self::STATE_STARTED );
		$this->schedule();
	}

	public function get_info() {
		return $this->get_context()->get_data() + array( 'state' => $this->get_process_state() );
	}

	public function get_context() {
		$data = get_option(
			"{$this->option_prefix}_state",
			null
		);

		if ( null === $data ) {
			return Context::from( (int) $this->task->starting() );
		}

		return Context::from_data( $data );
	}

	/**
	 * @param $context Context
	 *
	 * @return void
	 */
	public function set_info( $context ) {
		update_option( "{$this->option_prefix}_state", $context->get_data(), false );
	}

	private function delete_info() {
		delete_option( "{$this->option_prefix}_state" );
	}

	private function get_process_state() {
		return get_option( "{$this->option_prefix}_action_scheduler_state", self::STATE_STOPPED );
	}

	private function set_process_state( $state ) {
		update_option( "{$this->option_prefix}_action_scheduler_state", $state );
	}
}
