<?php
/**
 * This file defines the interface for task runners.
 *
 * @since 1.0.0
 * @package Wordlift_Framework\Tasks
 */

namespace Wordlift\Tasks;

/**
 * Define the Task_Runner interface.
 *
 * @since 1.0.0
 */
interface Task_Runner {

	/**
	 * Start the task.
	 *
	 * @param int $limit The maximum number of items to process.
	 * @param int $offset The starting offset (zero-based).
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function start( $limit = 0, $offset = 0 );

}
