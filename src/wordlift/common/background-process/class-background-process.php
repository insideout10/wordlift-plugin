<?php

namespace Wordlift\Common\Background_Process;

abstract class Background_Process extends \Wordlift_Plugin_WP_Background_Process {
	/**
	 * @var \Wordlift_Log_Service
	 */
	private $log;
	/**
	 * @var Data_Source
	 */
	private $data_source;

	/**
	 * Background_Process constructor.
	 *
	 * @param $data_source Data_Source
	 */
	public function __construct( $data_source ) {
		parent::__construct();
		$this->data_source = $data_source;
		$this->log = \Wordlift_Log_Service::get_logger( get_class() );
		// Set value for action key.
		$this->action = $this->get_action_key();
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
	 * @param int[] $items An array of item IDs.
	 *
	 * @return int[]|false The next IDs or false if there are no more.
	 */
	protected function task( $items ) {

		// Check if we must cancel.
		if ( $this->must_cancel() ) {
			$this->cancel();
			return false;
		}

		if ( $items && is_array( $items ) ) {
			$this->log->debug( sprintf( "Synchronizing items %s...", implode( ', ', $items ) ) );
		}
		// Sync the item.
		if ( $this->process_items( $items ) ) {
			// Update the process state, set the index in state in order
			// to reflect the new items.
			$this->update_batch_index();
			// Get the next batch for processing.
			return $this->get_next_batch();
		}
		else {
			// Return the failed term ids again.
			return $items;
		}
	}

	/**
	 * Process all the items in the current batch.
	 * @param $items
	 *
	 * @return bool If all items are successfully processed.
	 */
	abstract protected function process_items( $items );

	/**
	 * Return next batch of items after processing.
	 * @return int[] or false
	 */
	 protected function get_next_batch() {
	 	return $this->data_source->next();
	 }



	private function update_batch_index() {
		$next       = $this->data_source->next();
		$next_state = isset( $next ) ? 'started' : 'ended';

		/**
		 * Update the synchronization meta data, by increasing the current index.
		 *
		 * @var Sync_State $sync The {@link Sync_State}.
		 */
		$state = self::get_state()
		             ->increment_index( $this->data_source->get_batch_size() )
		             ->set_state( $next_state );
		update_option( $this->get_state_storage_key(), $state, false );

	}


}