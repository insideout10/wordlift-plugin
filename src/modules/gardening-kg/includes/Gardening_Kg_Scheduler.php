<?php

namespace Wordlift\Modules\Gardening_Kg;

class Gardening_Kg_Scheduler {

	/** Migration action hook. */
	const HOOK = 'wordlift/gardening_kg_sync';

	/** Migration action group. */
	const GROUP = 'wordlift-gardening-kg';

	/**
	 * @var Gardening_Kg_Runner $runner
	 */
	private $runner;

	public function __construct( Gardening_Kg_Runner $runner ) {
		$this->runner = $runner;
	}

	/**
	 * Set up the callback for the scheduled job.
	 */
	public function hook() {
		add_action( self::HOOK, array( $this, 'run' ), 10, 0 );
	}

	/**
	 * Remove the callback for the scheduled job.
	 */
	public function unhook() {
		remove_action( self::HOOK, array( $this, 'run' ), 10 );
	}

	/**
	 * The migration callback.
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
	 * Mark the migration complete.
	 */
	public function mark_complete() {
		$this->unschedule_migration();

		// \ActionScheduler_DataController::mark_migration_complete();
		// do_action( 'action_scheduler/migration_complete' );
	}

	/**
	 * Get a flag indicating whether the migration is scheduled.
	 *
	 * @return bool Whether there is a pending action in the store to handle the migration
	 */
	public function is_migration_scheduled() {
		$next = as_next_scheduled_action( self::HOOK );

		return ! empty( $next );
	}

	/**
	 * Schedule the migration.
	 *
	 * @param int $when Optional timestamp to run the next migration batch. Defaults to now.
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
	 * Remove the scheduled migration action.
	 */
	public function unschedule_migration() {
		as_unschedule_action( self::HOOK, null, self::GROUP );
	}

}
