<?php

namespace Wordlift\Task\Background;

interface Background_Task_State {

	public function enter();

	public function leave();

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over.
	 *
	 * @return mixed
	 */
	public function task( $item );

	public function get_info();

	/**
	 * Try to resume an interrupted task.
	 */
	public function resume();

}
