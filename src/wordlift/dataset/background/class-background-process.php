<?php

namespace Wordlift\Dataset\Background;


/**
 * Class Sync_Background_Process
 *
 * The background process has the following states:
 *  - STOPPING
 *  - STOPPED
 *  - STARTING
 *  - STARTED
 *
 * @package Wordlift\Dataset\Background
 */
interface Background_Process {
	/**
	 * Transition to the started state.
	 */
	public function start();

	/**
	 * Transition to the stopped state.
	 */
	public function stop();

	public function resume();

	/**
	 * Get the current state.
	 *
	 * @return string Either self::STARTED_STATE or self::STOPPED_STATE (default).
	 */
	public function get_state();

	/**
	 * Persist the current state.
	 *
	 * @param string $value
	 *
	 * @return bool
	 */
	public function set_state( $value );

	public function get_info();
}