<?php

namespace Wordlift\Dataset\Background;

use Wordlift\Dataset\Sync_Object_Adapter_Factory;
use Wordlift\Dataset\Sync_Service;

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
class Sync_Background_Process extends AS_Background_Process {

	const STATE_STARTED = 'started';
	const STATE_STOPPED = 'stopped';

	/**
	 * @var Sync_Service
	 */
	private $sync_service;

	/**
	 * @var Sync_Object_Adapter_Factory
	 */
	private $sync_object_adapter_factory;

	/**
	 * @var \Wordlift_Log_Service
	 */
	private $log;

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
		parent::__construct( 'wl_dataset__sync' );

		$this->log = \Wordlift_Log_Service::get_logger( get_class() );

		$this->sync_service                = $sync_service;
		$this->sync_object_adapter_factory = $sync_object_adapter_factory;

		// Set the current state.
		if ( self::STATE_STARTED === $this->get_state() ) {
			$this->state = new Sync_Background_Process_Started_State( $this, $this->sync_service, $this->sync_object_adapter_factory );
		} else {
			$this->state = new Sync_Background_Process_Stopped_State( $this );
		}

		$this->schedule();
	}

	/**
	 * Transition to the started state.
	 */
	public function start() {
		$this->state->leave();
		$this->state = new Sync_Background_Process_Started_State( $this, $this->sync_service, $this->sync_object_adapter_factory );
		$this->state->enter();
	}

	/**
	 * Transition to the stopped state.
	 */
	public function stop() {
		$this->state->leave();
		$this->state = new Sync_Background_Process_Stopped_State( $this );
		$this->state->enter();
	}

	public function resume() {
		$this->state->resume();
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
