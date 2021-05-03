<?php

namespace Wordlift\Common\Background_Process;

abstract class Background_Process extends \Wordlift_Plugin_WP_Background_Process {


	/**
	 * @var \Wordlift_Log_Service
	 */
	private $log;


	public function __construct( $analysis_background_service ) {
		parent::__construct();
		$this->log = \Wordlift_Log_Service::get_logger( get_class() );
	}

	/**
	 * The key which is used to store the Sync_State class for the current process
	 * @return string
	 */
	protected abstract function get_state_storage_key();

	/**
	 * The key which is used as prefix to store the options.
	 * @return string
	 */
	protected abstract function get_action_key();


	/**
	 * Check whether the process must cancel or not.
	 *
	 * @return bool Whether to cancel or not the process.
	 */
	private function must_cancel() {

		return get_transient( "{$this->action}__cancel" );
	}

	/**
	 * Cancels the current process.
	 */
	public function cancel() {

		$action = $this->action;
		$this->log->debug( "Cancelling ${action}..." );

		// Cleanup the process data.
		$this->cancel_process();

		// Set the state to cancelled.
		$state = $this->get_state();
		$state->set_state( 'cancelled' );
		update_option( $this->get_state_storage_key(), $state, false );

		// Finally delete the transient.
		delete_transient( "{$this->action}__cancel" );

	}


	/**
	 * Get the sync state.
	 *
	 * @return Sync_State The {@link Sync_State}.
	 */
	public function get_state() {

		try {
			return get_option( $this->get_state_storage_key(), Sync_State::unknown() );
		} catch ( \Exception $e ) {
			return Sync_State::unknown();
		}

	}


	/**
	 * This function is called:
	 *  - To start a new Synchronization, by passing a {@link Sync_Start_Message} instance.
	 *  - To process a item, by passing a numeric ID.
	 *
	 * This function returns the parameter for the next call or NULL if there are no more items to process.
	 *
	 * @param int[] $items An array of term IDs.
	 *
	 * @return int[]|false The next IDs or false if there are no more.
	 */
	protected function task( $term_ids ) {

		// Check if we must cancel.
		if ( $this->must_cancel() ) {
			$this->cancel();

			return false;
		}

		if ( $term_ids && is_array( $term_ids ) ) {
			$this->log->debug( sprintf( "Synchronizing terms %s...", implode( ', ', $term_ids ) ) );
		}
		// Sync the item.
		$this->process_items( $term_ids );

		// Update the process state, set the index in state in order
		// to reflect the new items.
		$this->update_batch_index();

		// Get the next batch for processing.
		return $this->get_next_batch();
	}

	/**
	 * @param $items
	 *
	 * @return void
	 */
	abstract protected function process_items( $items );

	/**
	 * Return next batch of items after processing.
	 * @return int[] or false
	 */
	abstract protected function get_next_batch();

	private function update_batch_index() {
	}


}