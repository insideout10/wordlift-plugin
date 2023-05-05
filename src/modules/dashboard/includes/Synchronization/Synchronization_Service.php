<?php

namespace Wordlift\Modules\Dashboard\Synchronization;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Wordlift\Modules\Common\Synchronization\Runner;
use Wordlift\Modules\Dashboard\Synchronization\Exception\SynchronizationAlreadyRunningException;
use Wordlift\Modules\Dashboard\Synchronization\Exception\SynchronizationNotRunningException;

class Synchronization_Service {

	const HOOK  = 'wl_dashboard__synchronization';
	const GROUP = 'wl_dashboard';

	public function register_hooks() {
		add_action( self::HOOK, array( $this, 'run' ) );
		add_action( 'wl_dashboard__synchronization__create', array( $this, 'scheduled_create' ) );
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {
		if ( function_exists( 'as_next_scheduled_action' ) && ! as_next_scheduled_action( 'wl_dashboard__synchronization__create' ) ) {
			as_schedule_recurring_action( strtotime( 'yesterday' ), DAY_IN_SECONDS, 'wl_dashboard__synchronization__create', array(), self::GROUP, true );
		}
	}

	/**
	 * @throws SynchronizationAlreadyRunningException when a synchronization is already running
	 *         Exception when cannot update the synchronization object.
	 */
	public function create() {
		// Get the last synchronization and check if it's running
		$last_synchronization = $this->load();
		if ( is_a( $last_synchronization, 'Wordlift\Modules\Dashboard\Synchronization\Synchronization' ) && $last_synchronization->is_running() ) {
			throw new SynchronizationAlreadyRunningException();
		}

		// Create a new synchronization and populate it with initial data.
		$new_synchronization = new Synchronization();

		// Get the total number of items.
		$total = $this->get_total();
		$new_synchronization->set_total( $total );
		$new_synchronization->set_started_at( new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) ) );
		$synchronization = $this->save( $new_synchronization );

		as_enqueue_async_action( self::HOOK, array(), self::GROUP );

		return $synchronization;
	}

	public function scheduled_create() {
		try {
			return $this->create();
		} catch ( Exception $e ) {
			return $e->getMessage();
		}
	}

	/**
	 * @throws SynchronizationNotRunningException when a synchronization is not running.
	 */
	public function run() {
		$last_synchronization = $this->load();

		if ( ! is_a( $last_synchronization, 'Wordlift\Modules\Dashboard\Synchronization\Synchronization' ) || ! $last_synchronization->is_running() ) {
			throw new SynchronizationNotRunningException();
		}

		$last_runner_idx = $last_synchronization->get_last_runner_idx();
		$last_id         = $last_synchronization->get_last_id();
		$runners         = $this->get_runners();

		// Completed?
		if ( ! is_numeric( $last_runner_idx ) || $last_runner_idx >= count( $runners ) ) {
			do_action( 'wl_ttl_cache_cleaner__flush' );

			$last_synchronization->set_stopped_at( new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) ) );
			$this->save( $last_synchronization );
		}

		// Not completed?
		if ( $last_runner_idx < count( $runners ) ) {
			// Run the runner from the last known id.
			list( $count, $new_last_id ) = $runners[ $last_runner_idx ]->run( $last_id );
			// Update the offset.
			$last_synchronization->set_offset( $last_synchronization->get_offset() + $count );

			// Set the next runner in case the last ID is `null` (i.e. there isn't).
			$new_last_runner_idx = ( is_numeric( $new_last_id ) ? $last_runner_idx : $last_runner_idx + 1 );
			// Set the next last ID in case there is one or `0` to restart.
			$new_last_id = ( is_numeric( $new_last_id ) ? $new_last_id : 0 );
			$last_synchronization->set_last_runner_idx( $new_last_runner_idx );
			$last_synchronization->set_last_id( $new_last_id );
			$this->save( $last_synchronization );

			as_enqueue_async_action( self::HOOK, array(), self::GROUP );
		}

	}

	/**
	 * @return Synchronization|null
	 */
	public function load() {
		return get_option( '_wl_dashboard__synchronization', null );
	}

	private function save( $synchronization ) {
		update_option( '_wl_dashboard__synchronization', $synchronization, false );

		return $synchronization;
	}

	private $runners;

	/**
	 * Get an array of Runners. The array is loaded the first time, then cached.
	 *
	 * @return Runner[]
	 */
	private function get_runners() {
		if ( ! isset( $this->runners ) ) {
			$this->runners = apply_filters( 'wl_dashboard__synchronization__runners', array() );
		}

		return $this->runners;
	}

	private $total;

	/**
	 * Get the total of items.
	 *
	 * @return int
	 */
	private function get_total() {
		if ( ! isset( $this->total ) ) {
			$this->total = array_reduce(
				$this->get_runners(),
				/**
				 * @param Runner $runner
				 */
				function ( $carry, $runner ) {
					return $carry + $runner->get_total();
				},
				0
			);
		}

		return $this->total;
	}

	public function get_last_sync() {
		$last_synchronization = $this->load();
		if ( ! is_a( $last_synchronization, 'Wordlift\Modules\Dashboard\Synchronization\Synchronization' )
			 || $last_synchronization->is_running() ) {
			return null;
		}

		return $last_synchronization->get_stopped_at();
	}

	public function get_next_sync() {
		$timestamp = as_next_scheduled_action( 'wl_dashboard__synchronization__create' );
		if ( ! is_numeric( $timestamp ) ) {
			return null;
		}

		return new DateTimeImmutable( "@$timestamp" );
	}

	public function delete_syncs() {
		delete_option( '_wl_dashboard__synchronization' );
	}

}

