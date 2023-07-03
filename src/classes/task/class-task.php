<?php

namespace Wordlift\Task;

interface Task {

	/**
	 * @return int The total number of items to process.
	 */
	public function starting();

	/**
	 * @param $value mixed The incoming value.
	 * @param $args {
	 *
	 * @type int $offset The index of the current item.
	 * @type int $count The total number of items as provided by the `starting` function.
	 * @type int $batch_size The number of items to process within this call.
	 * }
	 */
	public function tick( $value, $args );

}
