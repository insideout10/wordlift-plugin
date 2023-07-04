<?php
/**
 * This file defines a class which implements a progress tracker for ajax calls, i.e. the ajax client
 * receives the updates and sends new calls to proceed forward.
 *
 * @since 1.0.0
 * @package Wordlift_Framework\Tasks
 */

namespace Wordlift\Tasks;

use Wordlift_Log_Service;

/**
 * Define the Task_Ajax_Progress class.
 *
 * @since 1.0.0
 */
class Task_Ajax_Progress implements Task_Progress {

	/**
	 * The AJAX action, used to generate new nonces.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string $action The AJAX action.
	 */
	private $action;

	/**
	 * The total number of items to process.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var int The total number of items to process.
	 */
	private $count;

	/**
	 * The current item index.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var int $index The current item index.
	 */
	private $index;

	/**
	 * @var Wordlift_Log_Service
	 */
	private $log;

	/**
	 * Create a Task_Ajax_Progress instance with the specified
	 * AJAX action.
	 *
	 * @param string $action The AJAX action.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $action ) {

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		$this->action = $action;

	}

	/**
	 * {@inheritDoc}
	 */
	public function set_count( $value ) {

		$this->log->debug( "New count $value for action $this->action..." );

		$this->count = $value;

	}

	/**
	 * {@inheritDoc}
	 */
	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function set_progress( $index, $item ) {
		$this->index = $index;
	}

	/**
	 * {@inheritDoc}
	 */
	public function finish() {
		wp_send_json_success(
			array(
				'count'    => $this->count,
				'index'    => $this->index,
				// $this->index is zero based.
				'complete' => $this->index >= $this->count - 1,
				'nonce'    => wp_create_nonce( $this->action ),
			)
		);
	}

}
