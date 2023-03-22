<?php

namespace Wordlift\Modules\Common\Synchronization;

interface Runner {

	/**
	 * @param int $last_id The last id.
	 *
	 * @return int The number of processed items.
	 */
	public function run( $last_id );

	public function get_total();

}
