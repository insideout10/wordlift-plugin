<?php

namespace Wordlift\Modules\Gardening_Kg\Term_Entity;

use Wordlift\Modules\Gardening_Kg\Gardening_Kg_Store;

class Gardening_Kg_Term_Store implements Gardening_Kg_Store {

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function list_items( $id_greater_than, $batch_size ) {
		global $wpdb;

		return array_map(
			function ( $value ) {
				return (int) $value;
			},
			$wpdb->get_col(
				$wpdb->prepare(
					"SELECT ID FROM $wpdb->terms WHERE ID > %d LIMIT %d",
					$id_greater_than,
					$batch_size
				)
			)
		);
	}
}
