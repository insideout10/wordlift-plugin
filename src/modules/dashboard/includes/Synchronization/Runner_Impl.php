<?php

namespace Wordlift\Modules\Dashboard\Synchronization;

class Runner_Impl {

	private $runners;

	/**
	 * @paramm Runner[] $runners
	 */
	public function __construct( array $runners ) {
		$this->runners = $runners;
	}

	/**
	 * @param int $count
	 *
	 * @return int The number of items processed.
	 */
	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function run( $count ) {
		return 0;
	}

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	private function process( $item ) {

	}

}
