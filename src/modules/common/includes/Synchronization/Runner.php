<?php

namespace Wordlift\Modules\Common\Synchronization;

interface Runner {

	/**
	 * @return int The total number of items.
	 */
	public function get_total();

}
