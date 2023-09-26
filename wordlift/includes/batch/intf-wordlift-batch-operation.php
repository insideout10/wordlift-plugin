<?php
/**
 * Interfaces: Batch Operation.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/includes/batch
 */

/**
 * A Batch Operation is an operation which can executed in smaller chunks to avoid
 * using many computing resources and causing errors.
 *
 * @since 3.20.0
 */
interface Wordlift_Batch_Operation_Interface {

	/**
	 * Process the batch operation starting from the specified offset.
	 *
	 * @since 3.20.0
	 *
	 * @param int $offset Start from the specified offset (or 0 if not specified).
	 * @param int $limit Process the specified amount of items per call (or 10 if not specified).
	 *
	 * @return array {
	 * The operation result.
	 *
	 * @type int  $next The next offset.
	 * @type int  $limit The amount of items to process per call.
	 * @type int  $remaining The remaining number of elements to process.
	 * @type bool $complete Whether the operation completed.
	 * }
	 */
	public function process( $offset = 0, $limit = 10 );

	/**
	 * Count the number of elements that would be affected by the operation.
	 *
	 * @since 3.20.0
	 *
	 * @return int The number of elements that would be affected.
	 */
	public function count();

}
