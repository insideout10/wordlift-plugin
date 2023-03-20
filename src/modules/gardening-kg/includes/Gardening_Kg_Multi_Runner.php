<?php

namespace Wordlift\Modules\Gardening_Kg;

class Gardening_Kg_Multi_Runner implements Gardening_Kg_Runner {

	private $runners;

	/**
	 * @paramm Gardening_Kg_Runner[] $runners
	 */
	public function __construct( array $runners ) {
		$this->runners = $runners;
	}

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function run( $count ) {

	}

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	private function process( $item ) {

	}

}
