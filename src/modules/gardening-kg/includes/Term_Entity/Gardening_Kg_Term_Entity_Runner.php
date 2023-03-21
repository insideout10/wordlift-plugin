<?php

namespace Wordlift\Modules\Gardening_Kg\Term_Entity;

use Wordlift\Modules\Common\Synchronization\Runner;
use Wordlift\Modules\Gardening_Kg\Gardening_Kg_Store;

class Gardening_Kg_Term_Entity_Runner implements Runner {

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
		// $items = $this->store->list_items( $offset, $limit );
		// foreach ( $items as $item ) {
		// $this->process( $item );
		// }
	}

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	private function process( $item ) {

	}

	/**
	 * Get the total number of posts to process.
	 *
	 * We only count published posts.
	 *
	 * @return int
	 */
	public function get_total() {
		global $wpdb;

		return intval( $wpdb->get_var( "SELECT COUNT(1) FROM $wpdb->posts WHERE post_status = 'publish'" ) );
	}

}
