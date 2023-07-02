<?php
/**
 * This file is part of the `task` subfolder and provides an ajax adapter class to publish tasks
 * as ajax end-points.
 *
 * @since 1.0.0
 * @package Wordlift_Framework\Tasks
 */

namespace Wordlift\Tasks;

/**
 * Define the Task_Ajax_Adapter class.
 *
 * @since 1.0.0
 */
class Task_Ajax_Adapter {

	/**
	 * @var Task
	 */
	private $task;

	/**
	 * @var string $action_name The action name.
	 */
	private $action_name;

	/**
	 * Task_Ajax_Adapter constructor.
	 *
	 * @param Task $task
	 */
	public function __construct( $task ) {

		$this->task = $task;

		$this->action_name = $task->get_id();
		add_action( 'wp_ajax_' . $this->action_name, array( $this, 'start' ) );

	}

	public function start() {

		// First check if there is a valid nonce.
		check_ajax_referer( $this->action_name );

		// Get the offset.
		$offset = filter_input( INPUT_POST, 'offset', FILTER_SANITIZE_NUMBER_INT );
		$offset = $offset ? $offset : 0;

		// Compatibility fix for FacetWP, which somewhere in some filter checks for the $_POST array.
		unset( $_POST['offset'] );

		// Create an AJAX progress. The AJAX progress returns the progress data to the AJAX client, which
		// in turn calls the next batch.
		$ajax_progress = new Task_Ajax_Progress( $this->action_name );

		// Finally create the task runner and start it.
		$task_runner = new Task_Single_Instance_Task_Runner( $this->task, true, array( $ajax_progress ) );

		try {
			// Start the task runner, 1 item at a time.
			$task_runner->start( 1, $offset );
		} catch ( Task_Another_Instance_Is_Running_Exception $e ) {
			wp_send_json_error( 'A task is already running.' );
		}

	}

	/**
	 * @return Task
	 */
	public function get_task() {

		return $this->task;
	}

}
