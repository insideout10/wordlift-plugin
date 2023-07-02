<?php

namespace Wordlift\Task\Background;

interface Background_Route_Task {
	/**
	 * Transition to the started state.
	 */
	public function start();

	/**
	 * Transition to the stopped state.
	 */
	public function stop();

	public function resume();

	public function get_info();

}
