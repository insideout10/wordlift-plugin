<?php

namespace Wordlift\Dataset\Background;

abstract class Abstract_Sync_Background_Process_State implements Sync_Background_Process_State {

	private $state;

	public function __construct( $state ) {
		$this->state = $state;
	}

	public function get_info() {
		$started     = get_option( '_wl_sync_background_process_started' );
		$offset      = get_option( '_wl_sync_background_process_offset' );
		$stage       = get_option( '_wl_sync_background_process_stage' );
		$counts      = get_option( '_wl_sync_background_process_count', array( 0 ) );
		$last_update = get_option( '_wl_sync_background_process_updated' );

		// Calculate the overall index by adding the count of completed stages.
		$index = $offset + 1;
		for ( $i = 0; $i < $stage; $i ++ ) {
			$index += $counts[ $i ];
		}

		// Get the total count.
		$total_count = array_sum( $counts );

		return new Sync_Background_Process_Info( $this->state, $started, $index, $total_count, $last_update );
	}

	public function resume() {
		// do nothing.
	}

}
