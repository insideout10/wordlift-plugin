<?php
/**
 * This file defines the Progress_Interface interface which is used
 * by implementing classes to receive updates about running tasks.
 *
 * @since 1.0.0
 * @package Wordlift_Framework\Tasks
 */

namespace Wordlift\Tasks;

/**
 * Define the Progress_Interface interface.
 *
 * @since 1.0.0
 */
interface Task_Progress {

	/**
	 * The total number of elements to process.
	 *
	 * @param int $value The total number of elements to process.
	 *
	 * @since 1.0.0
	 */
	public function set_count( $value );

	/**
	 * Set the current processed item.
	 *
	 * @param int   $counter The current item.
	 * @param mixed $item The current item.
	 *
	 * @since 1.0.0
	 */
	public function set_progress( $counter, $item );

	/**
	 * Set the operation as complete.
	 *
	 * @since 1.0.0
	 */
	public function finish();

}
