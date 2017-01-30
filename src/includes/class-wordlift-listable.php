<?php

/**
 */
abstract class Wordlift_Listable {

	/**
	 * List the items starting at the specified offset and up to the specified limit.
	 *
	 * @param int $offset The start offset.
	 * @param int $limit The maximum number of items to return.
	 * @param array $args Additional arguments.
	 *
	 * @return array A array of items (or an empty array if no items are found).
	 */
	abstract function find( $offset = 0, $limit = 10, $args = array() );

	/**
	 * @param callable $callback
	 * @param array $args
	 * @param int $offset
	 * @param int $max
	 */
	public function process( $callback, $args = array(), $offset = 0, $max = PHP_INT_MAX ) {

		// We process chunks in order to avoid using too much memory,
		// starting at offset 0, 10 at a time.
//		$limit = 10;

		while ( 0 < sizeof( $items = $this->find( $offset, 1, $args ) ) && $offset < $max ) {

			// Cycle through items and call the callback function.
			foreach ( $items as $item ) {
				call_user_func_array( $callback, array( $item ) );
			}

			// Clean the cache to avoid memory errors.
			wp_cache_flush();

			// Move to the next offset.
			$offset += 1;

		}

	}

}
