<?php

namespace Wordlift\Modules\Dashboard\Synchronization;

use DateTimeImmutable;
use DateTimeZone;
use Wordlift\Modules\Common\Synchronization\Runner;
use Wordlift\Modules\Dashboard\Synchronization\Exception\SynchronizationAlreadyRunningException;
use Wordlift\Modules\Dashboard\Synchronization\Exception\SynchronizationNotRunningException;

class Synchronization_Service {

	const HOOK  = 'wl_dashboard__synchronization';
	const GROUP = 'wl_dashboard';

	public function register_hooks() {
		add_action( self::HOOK, array( $this, 'run' ) );
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

		// Start the synchronization.
		as_enqueue_async_action( self::HOOK, array(), self::GROUP );

		return $synchronization;
	}

	/**
	 * @throws SynchronizationNotRunningException when a synchronization is not running.
	 */
	public function run() {
		$last_synchronization = $this->load();
		if ( ! is_a( $last_synchronization, 'Wordlift\Modules\Dashboard\Synchronization\Synchronization' ) || ! $last_synchronization->is_running() ) {
			throw new SynchronizationNotRunningException();
		}

		$runners = $this->get_runners();
		foreach ( $runners as $runner ) {
			$runner->run();
		}

		// $offset  = $last_synchronization->get_offset();
		// $runners = $this->get_runners();
		// foreach ( $runners as $runner ) {
		// $offset += $runner->run();
		// }

		// as_enqueue_async_action( self::HOOK, array(), self::GROUP );
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

}
