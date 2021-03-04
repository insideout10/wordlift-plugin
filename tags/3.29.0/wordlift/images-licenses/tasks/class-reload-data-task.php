<?php

namespace Wordlift\Images_Licenses\Tasks;

use Wordlift\Images_Licenses\Image_License_Scheduler;
use Wordlift\Tasks\Task;

class Reload_Data_Task implements Task {

	/**
	 * @inheritDoc
	 */
	function get_id() {

		return 'wl_reload_data_task';
	}

	function get_label() {

		return __( 'Reload data', 'wordlift' );
	}

	/**
	 * @inheritDoc
	 */
	function list_items( $limit = 10, $offset = 0 ) {

		return array( 1 );
	}

	/**
	 * @inheritDoc
	 */
	function count_items() {

		return 1;
	}

	/**
	 * @inheritDoc
	 */
	function process_item( $item ) {

		do_action( Image_License_Scheduler::ACTION_NAME );

	}

}
