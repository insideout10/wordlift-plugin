<?php

namespace Wordlift\Modules\Common\Synchronization;

interface Store {

	/**
	 * @param int $id_greater_than The starting offset (excluded).
	 * @param int $batch_size The batch size.
	 *
	 * @return array
	 */
	public function list_items( $id_greater_than, $batch_size );

}
