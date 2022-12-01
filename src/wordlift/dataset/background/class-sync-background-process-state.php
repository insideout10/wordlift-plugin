<?php

namespace Wordlift\Dataset\Background;

interface Sync_Background_Process_State {

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
	 * @return mixed
	 */
	public function task();

	public function get_info();

	/**
	 * Try to resume an interrupted task.
	 */
	public function resume();

}
