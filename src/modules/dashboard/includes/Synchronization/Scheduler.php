<?php

namespace Wordlift\Modules\Dashboard\Synchronization;

class Scheduler {

	const HOOK  = 'wordlift/dashboard_sync';
	const GROUP = 'wordlift-dashboard';

	/**
	 * Set up the callback for the scheduled job.
	 */
	public function register_hooks() {
		add_action( self::HOOK, array( $this, 'run' ), 10, 0 );
	}

	/**
	 * The callback.
	 */
	public function run() {
		$batch_size = 20;
		$count      = $this->runner->run( $batch_size );

		if ( $count < $batch_size ) {
			$this->mark_complete();
		} else {
			$this->schedule( time() );
		}
	}

	/**
	 * Mark the complete.
	 */
	public function mark_complete() {
		$this->unschedule();

		// \ActionScheduler_DataController::mark_complete();
		// do_action( 'action_scheduler_complete' );
	}

	/**
	 * Get a flag indicating whether the is scheduled.
	 *
	 * @return bool Whether there is a pending action in the store to handle the
	 */
	public function is_scheduled() {
		$next = as_next_scheduled_action( self::HOOK );

		return ! empty( $next );
	}

	/**
	 * Schedule the.
	 *
	 * @param int $when Optional timestamp to run the next batch. Defaults to now.
	 *
	 * @return string The action ID
	 */
	public function schedule( $when = 0 ) {
		// error_log( "**** SCHEDULING 1 **** " . $when );
		// $next = as_next_scheduled_action( self::HOOK );
		//
		// if ( ! empty( $next ) ) {
		// error_log( "**** SCHEDULING NEXT **** " . $next );
		//
		// return $next;
		// }
		//
		// if ( empty( $when ) ) {
		// $when = time() + MINUTE_IN_SECONDS;
		// }
		//
		// error_log( "**** SCHEDULING 2 **** " . $when );

		return as_schedule_single_action( $when, self::HOOK, array(), self::GROUP );
	}

	/**
	 * Remove the scheduled action.
	 */
	public function unschedule() {
		as_unschedule_action( self::HOOK, null, self::GROUP );
	}

}
