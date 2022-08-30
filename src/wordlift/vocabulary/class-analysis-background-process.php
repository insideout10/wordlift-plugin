<?php

namespace Wordlift\Vocabulary;

class Analysis_Background_Process extends \Wordlift_Plugin_WP_Background_Process {

	const WL_CMKG_ANALYSIS_BACKGROUND_PROCESS = '_wl_cmkg_analysis_background_process';

	protected $action = 'wl_cmkg_analysis_background__analysis';

	/**
	 * @var Analysis_Background_Service
	 */
	private $analysis_background_service;

	/**
	 * @var \Wordlift_Log_Service
	 */
	private $log;

	/**
	 * Analysis_Background_Process constructor.
	 *
	 * @param $analysis_background_service Analysis_Background_Service A {@link Analysis_Background_Service} instance providing the supporting functions to this background process.
	 */
	public function __construct( $analysis_background_service ) {
		parent::__construct();

		$this->log = \Wordlift_Log_Service::get_logger( get_class() );

		$this->analysis_background_service = $analysis_background_service;

	}

	/**
	 * This function is called:
	 *  - To start a new Synchronization, by passing a {@link Sync_Start_Message} instance.
	 *  - To synchronize a post, by passing a numeric ID.
	 *
	 * This function returns the parameter for the next call or NULL if there are no more posts to process.
	 *
	 * @param int[] $term_ids An array of term IDs.
	 *
	 * @return int[]|false The next term IDs or false if there are no more.
	 */
	protected function task( $term_ids ) {

		// Check if we must cancel.
		if ( $this->must_cancel() ) {
			$this->cancel();

			return false;
		}

		if ( ! $term_ids || ! is_array( $term_ids ) ) {
			$this->cancel();
			return false;
		}

		$this->log->debug( sprintf( 'Synchronizing terms %s...', implode( ', ', $term_ids ) ) );
		// Sync the item.
		return $this->sync_items( $term_ids );
	}

	/**
	 * Start the background processing.
	 *
	 * @return bool True if the process has been started, otherwise false.
	 */
	public function start() {
		$this->log->debug( 'Trying to start analysis bg service...' );
		// Create a new Sync_Model state of `started`.
		if ( ! $this->is_started( self::get_state() ) ) {
			$this->log->debug( 'Starting...' );

			$sync_state = new Sync_State( time(), 0, $this->analysis_background_service->count(), time(), 'started' );
			update_option( self::WL_CMKG_ANALYSIS_BACKGROUND_PROCESS, $sync_state, false );

			$next = $this->analysis_background_service->next();

			$this->push_to_queue( $next );
			$this->save()->dispatch();

			if ( $next && is_array( $next ) ) {
				$this->log->debug( sprintf( 'Started with term IDs %s.', implode( ', ', $next ) ) );
			}

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
			return get_option( self::WL_CMKG_ANALYSIS_BACKGROUND_PROCESS, Sync_State::unknown() );
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
	public function cancel() {

		$this->log->debug( 'Cancelling synchronization...' );

		// Cleanup the process data.
		$this->cancel_process();

		// Set the state to cancelled.
		$state = self::get_state();
		$state->set_state( 'cancelled' );
		update_option( self::WL_CMKG_ANALYSIS_BACKGROUND_PROCESS, $state, false );

		// Finally delete the transient.
		delete_transient( "{$this->action}__cancel" );

	}

	/**
	 * Push the post with the provided ID to the remote platform.
	 *
	 * @param int[] $term_ids The term IDs.
	 *
	 * @return int[]|false The next term ID to process or false if processing is complete.
	 */
	private function sync_items( $term_ids ) {

		// Sync this item.
		if ( $this->analysis_background_service->perform_analysis_for_terms( $term_ids ) ) {

			$next       = $this->analysis_background_service->next();
			$next_state = isset( $next ) ? 'started' : 'ended';

			/**
			 * Update the synchronization meta data, by increasing the current index.
			 *
			 * @var Sync_State $sync The {@link Sync_State}.
			 */
			$state = self::get_state()
						 ->increment_index( $this->analysis_background_service->get_batch_size() )
						 ->set_state( $next_state );
			update_option( self::WL_CMKG_ANALYSIS_BACKGROUND_PROCESS . '', $state, false );

			// Return the next IDs or false if there aren't.
			return isset( $next ) ? $next : false;
		} else {
			// Retry.
			// @@todo: put a limit to the number of retries.

			$this->log->error( sprintf( 'Sync failed for terms %s.', implode( ', ', $term_ids ) ) );

			return $term_ids;
		}

	}

}
