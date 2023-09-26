<?php
/**
 * This file is part of the task sub-folder. It provides the interface for tasks.
 *
 * @since 1.0.0
 * @package Wordlift_Framework\Tasks
 */

namespace Wordlift\Tasks;

/**
 * Define the Task interface.
 *
 * @since 1.0.0
 */
interface Task {

	/**
	 * Define the task ID.
	 *
	 * @return string The task id.
	 * @since 1.0.0
	 */
	public function get_id();

	public function get_label();

	/**
	 * List the items to process.
	 *
	 * @param int $limit
	 * @param int $offset
	 *
	 * @return array An array of items.
	 * @since 1.0.0
	 */
	public function list_items( $limit = 10, $offset = 0 );

	/**
	 * Count the total number of items to process.
	 *
	 * @return int Total number of items to process.
	 * @since 1.0.0
	 */
	public function count_items();

	/**
	 * Process the provided item.
	 *
	 * @param mixed $item Process the provided item.
	 *
	 * @since 1.0.0
	 */
	public function process_item( $item );

}
