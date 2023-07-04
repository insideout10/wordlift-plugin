<?php
/**
 * This file defines the Task_Single_Instance_Task_Runner class
 * and the Task_Another_Instance_Is_Running_Exception exception.
 *
 * The single runner executes a non-concurrent {@link Task}
 * ensuring that only one instance of this task is executed. It does so by using WordPress
 * transients.
 *
 * This file is part of the task sub-folder.
 *
 * @since 1.0.0
 * @package Wordlift_Framework\Tasks
 */

namespace Wordlift\Tasks;

use Wordlift_Log_Service;

/**
 * Define the Task_Single_Instance_Task_Runner class.
 *
 * @since 1.0.0
 */
class Task_Single_Instance_Task_Runner {

	/**
	 * Define the transient prefix.
	 *
	 * @since 1.0.0
	 */
	const IS_RUNNING_PREFIX = '_wf_task_runner__';

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since 1.0.0
	 * @var Wordlift_Log_Service A {@link Wordlift_Log_Service} instance.
	 * @access private
	 */
	private $log;

	/**
	 * The {@link Task} to execute.
	 *
	 * @since 1.0.0
	 * @var Task $task The {@link Task} to execute.
	 * @access private
	 */
	private $task;

	/**
	 * One or more callbacks to call to update about the task progress.
	 *
	 * @since 1.0.0
	 * @var Task_Progress[] $callbacks An array of {@link Wordlift_For_Bungalowparkoverzicht_Progress}.
	 * @access private
	 */
	private $callbacks;

	/**
	 * Whether to force starting a task even if another instance of the task is already running.
	 *
	 * @since 1.0.0
	 * @var bool $force Whether to force starting a task even if another instance of the task is already running.
	 * @access private
	 */
	private $force;

	/**
	 * Create a {@link Task_Single_Instance_Task_Runner} instance.
	 *
	 * @param Task  $task The {@link Task} instance.
	 * @param bool  $force Whether to force starting a task even if another instance of the task is already running, default `false`.
	 * @param array $callbacks An array of {@link Wordlift_For_Bungalowparkoverzicht_Progress}.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $task, $force = false, $callbacks = array() ) {

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		$this->task      = $task;
		$this->force     = $force;
		$this->callbacks = $callbacks;

	}

	/**
	 * Get the transient name for running flag.
	 *
	 * @return string The transient name.
	 * @since 1.0.0
	 */
	private function get_running_transient() {

		return self::IS_RUNNING_PREFIX . $this->task->get_id();
	}

	/**
	 * Check whether a task is running.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function is_running() {
		return 'yes' === get_transient( $this->get_running_transient() );
	}

	/**
	 * Set whether the task is running or not.
	 *
	 * @param bool $value Whether the task is running or not.
	 *
	 * @since 1.0.0
	 */
	public function set_running( $value ) {
		set_transient( $this->get_running_transient(), $value ? 'yes' : 'no' );
	}

	/**
	 * Start the task.
	 *
	 * @param int $limit The maximum number of items to process.
	 * @param int $offset The starting offset.
	 *
	 * @throws Task_Another_Instance_Is_Running_Exception if the task is already running.
	 * @since 1.0.0
	 */
	public function start( $limit = 0, $offset = 0 ) {

		// Bail out if the task is already running.
		if ( ! $this->force && $this->is_running() ) {
			throw new Task_Another_Instance_Is_Running_Exception();
		}

		// Set the task as running.
		$this->set_running( true );

		// List the chunk of elements to process.
		$items = $this->task->list_items( $limit, $offset );

		$count = count( $items );
		for ( $i = 0; $i < $count; $i ++ ) {
			// Process the item.
			$this->task->process_item( $items[ $i ] );

			// Update the progress.
			$this->set_progress( $offset + $i, $items[ $i ] );
		}

		// Set the total number of elements to process.
		$this->set_count( $this->task->count_items() );

		// Unset the running flag.
		$this->set_running( false );

		// Set the task to complete.
		$this->finish();

	}

	/**
	 * Set the total number of items to process.
	 *
	 * @param int $value The total number of items to process.
	 *
	 * @since 1.0.0
	 */
	private function set_count( $value ) {

		if ( empty( $this->callbacks ) ) {
			return;
		}

		foreach ( $this->callbacks as $callback ) {
			call_user_func( array( $callback, 'set_count' ), $value );
		}

	}

	/**
	 * Set the task progress.
	 *
	 * @param int   $index The current item index.
	 * @param mixed $item The current item.
	 *
	 * @since 1.0.0
	 */
	private function set_progress( $index, $item ) {

		if ( empty( $this->callbacks ) ) {
			return;
		}

		foreach ( $this->callbacks as $callback ) {
			call_user_func( array( $callback, 'set_progress' ), $index, $item );
		}

	}

	/**
	 * Inform the callbacks that the task completed.
	 *
	 * @since 1.0.0
	 */
	private function finish() {

		if ( empty( $this->callbacks ) ) {
			return;
		}

		foreach ( $this->callbacks as $callback ) {
			call_user_func( array( $callback, 'finish' ) );
		}

	}

}
