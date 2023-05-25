<?php

namespace Wordlift\Modules\Gardening_Kg\Term_Entity;

use Wordlift\Modules\Common\Synchronization\Store;

class Gardening_Kg_Term_Store implements Store {

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function list_items( $id_greater_than, $batch_size ) {
		global $wpdb;

		return array_map(
			function ( $value ) {
				return (int) $value;
			},
			$wpdb->get_col(
				$wpdb->prepare(
					"SELECT term_id FROM $wpdb->terms WHERE term_id > %d LIMIT %d",
					$id_greater_than,
					$batch_size
				)
			)
		);
	}
}
