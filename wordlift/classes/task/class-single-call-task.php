<?php

namespace Wordlift\Task;

class Single_Call_Task implements Task {

	private $callable;

	/**
	 * @var string|null
	 */
	private $id;

	public function __construct( $callable, $id = null ) {
		$this->callable = $callable;
		$this->id       = $id;
	}

	public function get_id() {
		return isset( $this->id ) ? $this->id : hash( 'sha256', get_class( $this ) );
	}

	public function starting() {
		return 1;
	}

	/**
	 * @param $value mixed The incoming value.
	 * @param $args {
	 *
	 * @type int $offset The index of the current item.
	 * @type int $count The total number of items as provided by the `starting` function.
	 * @type int $batch_size The number of items to process within this call.
	 * }
	 *
	 * @return void
	 */
	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function tick( $value, $args ) {

		call_user_func( $this->callable, 1 );

	}

}
