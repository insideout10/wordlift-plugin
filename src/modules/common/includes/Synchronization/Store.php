<?php

namespace Wordlift\Modules\Common\Synchronization;

interface Store {

	/**
	 * @param int $offset The starting offset (excluded).
	 * @param int $batch_size The batch size.
	 *
	 * @return array
	 */
	public function list_items( $offset, $batch_size );

}
