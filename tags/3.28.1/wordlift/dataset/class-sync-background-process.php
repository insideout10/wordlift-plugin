<?php

namespace Wordlift\Dataset;

class Sync_Background_Process extends \Wordlift_Plugin_WP_Background_Process {

	protected $action = 'wl_dataset__sync';

	/**
	 * @var Sync_Service
	 */
	private $sync_service;

	/**
	 * @var \Wordlift_Log_Service
	 */
	private $log;

	/**
	 * Sync_Background_Process constructor.
	 *
	 * @param $sync_service Sync_Service A {@link Sync_Service} instance providing the supporting functions to this background process.
	 */
	public function __construct( $sync_service ) {
		parent::__construct();

		$this->log = \Wordlift_Log_Service::get_logger( get_class() );

		$this->sync_service = $sync_service;

	}

	/**
	 * This function is called:
	 *  - To start a new Synchronization, by passing a {@link Sync_Start_Message} instance.
	 *  - To synchronize a post, by passing a numeric ID.
	 *
	 * This function returns the parameter for the next call or NULL if there are no more posts to process.
	 *
	 * @param int[] $post_ids An array of post IDs.
	 *
	 * @return int[]|false The next post IDs or false if there are no more.
	 */
	protected function task( $post_ids ) {

		// Check if we must cancel.
		if ( $this->must_cancel() ) {
			$this->cancel();

			return false;
		}

		$this->log->debug( sprintf( "Synchronizing posts %s...", implode( ', ', $post_ids ) ) );

		// Sync the item.
		return $this->sync_items( $post_ids );
	}

	/**
	 * Start the background processing.
	 *
	 * @return bool True if the process has been started, otherwise false.
	 */
	public function start() {

		// Create a new Sync_Model state of `started`.
		if ( ! $this->is_started( self::get_state() ) ) {
			$this->log->debug( "Starting..." );

			$sync_state = new Sync_State( time(), 0, $this->sync_service->count(), time(), 'started' );
			update_option( '_wl_dataset_sync', $sync_state, false );

			$next = $this->sync_service->next();
			$this->push_to_queue( $next );
			$this->save()->dispatch();

			$this->log->debug( sprintf( 'Started with post IDs %s.', implode( ', ', $next ) ) );

			return true;
		}

		return false;
	}

	/**
	 * Set the transient to cancel the process. The next time the process runs, it'll check whether this transient is
	 * set and will stop processing.
	 */
	public function request_cancel() {

		set_transient( "{$this->action}__cancel", true );

	}

	/**
	 * Get the sync state.
	 *
	 * @return Sync_State The {@link Sync_State}.
	 */
	public static function get_state() {

		try {
			return get_option( '_wl_dataset_sync', Sync_State::unknown() );
		} catch ( \Exception $e ) {
			return Sync_State::unknown();
		}

	}

	/**
	 * Check whether the provided state is `started` or not.
	 *
	 * @param Sync_State $state The {@link Sync_State}.
	 *
	 * @return bool True if the state is started.
	 */
	private function is_started( $state ) {
		return $state instanceof Sync_State && 'started' === $state->state && 30 > ( time() - $state->last_update );
	}

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
	private function cancel() {

		$this->log->debug( "Cancelling synchronization..." );

		// Cleanup the process data.
		$this->cancel_process();

		// Set the state to cancelled.
		$state = self::get_state();
		$state->set_state( 'cancelled' );
		update_option( '_wl_dataset_sync', $state, false );

		// Finally delete the transient.
		delete_transient( "{$this->action}__cancel" );

	}

	/**
	 * Push the post with the provided ID to the remote platform.
	 *
	 * @param int[] $post_ids The post IDs.
	 *
	 * @return int[]|false The next post ID to process or false if processing is complete.
	 */
	private function sync_items( $post_ids ) {

		if ( ! is_array( $post_ids ) ) {
			$this->log->error( '$post_ids must be an array, received: ' . var_export( $post_ids, true ) );
			return false;
		}

		// Sync this item.
		if ( $this->sync_service->sync_items( $post_ids ) ) {

			$next       = $this->sync_service->next();
			$next_state = isset( $next ) ? 'started' : 'ended';

			/**
			 * Update the synchronization meta data, by increasing the current index.
			 *
			 * @var Sync_State $sync The {@link Sync_State}.
			 */
			$state = self::get_state()
			             ->increment_index( $this->sync_service->get_batch_size() )
			             ->set_state( $next_state );
			update_option( '_wl_dataset_sync', $state, false );

			$this->log->debug( "State updated to " . var_export( $state, true ) );

			// Return the next IDs or false if there aren't.
			return isset( $next ) ? $next : false;
		} else {
			// Retry.
			// @@todo: put a limit to the number of retries.

			$this->log->error( sprintf( "Sync failed for posts %s.", implode( ', ', $post_ids ) ) );

			return $post_ids;
		}

	}

}
