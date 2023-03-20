<?php

namespace Wordlift\Modules\Gardening_Kg;

class Gardening_Kg_Main_Entity_Runner implements Gardening_Kg_Runner {

	/**
	 * @var Gardening_Kg_Store $store
	 */
	private $store;

	/**
	 * @paramm Gardening_Kg_Store $store
	 */
	public function __construct( Gardening_Kg_Store $store ) {
		$this->store = $store;
	}

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function run( $count ) {
		// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
		$items = $this->store->list_items( $offset, $limit );
		foreach ( $items as $item ) {
			$this->process( $item );
		}
	}

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	private function process( $item ) {

	}

}
