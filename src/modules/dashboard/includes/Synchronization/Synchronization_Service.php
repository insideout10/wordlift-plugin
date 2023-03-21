<?php

namespace Wordlift\Modules\Dashboard\Synchronization;

use Wordlift\Modules\Common\Synchronization\Runner;
use Wordlift\Modules\Dashboard\Synchronization\Exception\SynchronizationAlreadyRunningException;

class Synchronization_Service {

	/**
	 * @throws SynchronizationAlreadyRunningException when a synchronization is already running
	 * Exception when cannot update the synchronization object.
	 */
	public function create() {
		// Get the last synchronization and check if it's running
		$last_synchronization = get_option( '_wl_dashboard__synchronization', null );
		if ( is_a( $last_synchronization, 'Wordlift\Modules\Dashboard\Synchronization\Synchronization' ) && $last_synchronization->is_running() ) {
			throw new SynchronizationAlreadyRunningException();
		}

		// Create a new synchronization and populate it with initial data.
		$new_synchronization = new Synchronization();
		$runners             = $this->get_runners();

		// Get the total number of items.
		$total = array_reduce(
			$runners,
			/**
			 * @param Runner $runner
			 */
			function ( $carry, $runner ) {
				return $carry + $runner->get_total();
			},
			0
		);

		$new_synchronization->set_total( $total );

		update_option( '_wl_dashboard__synchronization', $new_synchronization );

		return $new_synchronization;
	}

	/**
	 * @return Runner[]
	 */
	private function get_runners() {
		return apply_filters( 'wl_dashboard__synchronization__runners', array() );
	}

}
