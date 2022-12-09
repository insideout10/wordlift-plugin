<?php

namespace Wordlift\Dataset\Background;

use Wordlift\Common\Background_Process\Action_Scheduler\Action_Scheduler_Background_Process;
use Wordlift\Common\Background_Process\Action_Scheduler\State;
use Wordlift\Dataset\Sync_Object_Adapter_Factory;
use Wordlift\Dataset\Sync_Service;

class Sync_Background_Process extends Action_Scheduler_Background_Process {

	const STATE_STARTED = 'started';
	const STATE_STOPPED = 'stopped';

	const HOOK_NAME = 'wl_dataset__sync';

	/**
	 * @var Sync_Service
	 */
	private $sync_service;

	/**
	 * @var Sync_Object_Adapter_Factory
	 */
	private $sync_object_adapter_factory;

	/**
	 * @var Sync_Background_Process_State
	 */
	private $state;

	/**
	 * Sync_Background_Process constructor.
	 *
	 * @param Sync_Service                $sync_service A {@link Sync_Service} instance providing the supporting functions to this background process.
	 * @param Sync_Object_Adapter_Factory $sync_object_adapter_factory
	 */
	public function __construct( $sync_service, $sync_object_adapter_factory ) {
		parent::__construct( self::HOOK_NAME, 'wordlift' );
		$this->sync_service                = $sync_service;
		$this->sync_object_adapter_factory = $sync_object_adapter_factory;

		// Set the current state.
		if ( self::STATE_STARTED === $this->get_state() ) {
			$this->state = new Sync_Background_Process_Started_State( $this, $this->sync_service, $this->sync_object_adapter_factory );
		} else {
			$this->state = new Sync_Background_Process_Stopped_State( $this );
		}
	}

	public function start() {
		$this->state->leave();
		$this->state = new Sync_Background_Process_Started_State( $this, $this->sync_service, $this->sync_object_adapter_factory );
		$this->state->enter();
		$this->schedule();
	}

	public function stop() {
		$this->state->leave();
		$this->state = new Sync_Background_Process_Stopped_State( $this );
		$this->state->enter();
	}

	public function resume() {
		$this->schedule();
		$this->state->resume();
	}

	public function do_task( $args ) {

		// Action scheduler might have pending tasks which can call this method.
		// So we need to check if the task should run or not
		if ( self::STATE_STOPPED === $this->get_state() ) {
			return State::complete();
		}

		$result = $this->state->task( $args );
		if ( ! $result->has_next() ) {
			$this->stop();
		}
		return $result;
	}

	/**
	 * Get the current state.
	 *
	 * @return string Either self::STARTED_STATE or self::STOPPED_STATE (default).
	 */
	public function get_state() {
		return get_option( '_wl_sync_background_process_state', self::STATE_STOPPED );
	}

	/**
	 * Persist the current state.
	 *
	 * @param string $value
	 *
	 * @return bool
	 */
	public function set_state( $value ) {
		return null === $value
			? delete_option( '_wl_sync_background_process_state' )
			: update_option( '_wl_sync_background_process_state', $value, true );
	}

	public function get_info() {
		return $this->state->get_info();
	}

}
