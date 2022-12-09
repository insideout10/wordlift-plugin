<?php

namespace Wordlift\Dataset\Background;

use Wordlift\Dataset\Sync_Object_Adapter_Factory;
use Wordlift\Dataset\Sync_Service;

class Cron_Background_Process implements Background_Process {

	private $queue      = array();
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
	 * @var \Wordlift_Log_Service
	 */
	private $log;

	/**
	 * @var Sync_Background_Process_State
	 */
	private $state;

	public function push_to_queue( $data ) {

		$this->queue[] = $data;

		return $this;
	}

	public function save() {
		return $this;
	}

	public function dispatch() {
		if ( count( $this->queue ) > 0 ) {
			$this->queue = array();
			$this->schedule();
		}
	}

	/**
	 * Sync_Background_Process constructor.
	 *
	 * @param Sync_Service $sync_service A {@link Sync_Service} instance providing the supporting functions to this background process.
	 * @param Sync_Object_Adapter_Factory $sync_object_adapter_factory
	 */
	public function __construct( $sync_service, $sync_object_adapter_factory ) {

		$this->log = \Wordlift_Log_Service::get_logger( get_class() );

		$this->sync_service                = $sync_service;
		$this->sync_object_adapter_factory = $sync_object_adapter_factory;

		// Set the current state.
		if ( self::STATE_STARTED === $this->get_state() ) {
			$this->state = new Sync_Background_Process_Started_State( $this, $this->sync_service, $this->sync_object_adapter_factory );
		} else {
			$this->state = new Sync_Background_Process_Stopped_State( $this );
		}

		add_action( self::HOOK_NAME, array( $this, 'task' ) );

	}

	/**
	 * This function is called:
	 *  - To start a new Synchronization, by passing a {@link Sync_Start_Message} instance.
	 *  - To synchronize a post, by passing a numeric ID.
	 *
	 * This function returns the parameter for the next call or NULL if there are no more posts to process.
	 *
	 * @param mixed $item Queue item to iterate over.
	 *
	 * @return int[]|false The next post IDs or false if there are no more.
	 */
	public function task(  ) {
		error_log("task() called from cron");
		try {
			$result = $this->state->task( array() );

			if ( $result ) {
				$this->schedule();
				var_dump( "running method" );
			}

		} catch ( \Exception $e ) {

		}
	}

	public function cancel_process() {
		$this->stop();
	}

	/**
	 * Transition to the started state.
	 */
	public function start() {
		$this->state->leave();
		$this->state = new Sync_Background_Process_Started_State( $this, $this->sync_service, $this->sync_object_adapter_factory );
		$this->state->enter();
		$this->schedule();
	}

	/**
	 * Transition to the stopped state.
	 */
	public function stop() {
		$this->state->leave();
		$this->state = new Sync_Background_Process_Stopped_State( $this );
		$this->state->enter();
		as_unschedule_all_actions( self::HOOK_NAME );
	}

	public function resume() {
		$this->schedule();
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

	/**
	 * @return void
	 */
	private function schedule() {
		error_log("scheduling task");
		as_enqueue_async_action(self::HOOK_NAME );
	}
}
