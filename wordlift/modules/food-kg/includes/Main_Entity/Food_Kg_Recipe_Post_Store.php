<?php

namespace Wordlift\Modules\Food_Kg\Main_Entity;

use Wordlift\Modules\Common\Synchronization\Store;

class Food_Kg_Recipe_Post_Store implements Store {

	public function list_items( $id_greater_than, $batch_size ) {
		global $wpdb;

		return array_map(
			function ( $value ) {
				return (int) $value;
			},
			$wpdb->get_col(
				$wpdb->prepare(
					"SELECT ID FROM $wpdb->posts WHERE ID > %d AND post_type = 'wprm_recipe' ORDER BY ID ASC LIMIT %d",
					$id_greater_than,
					$batch_size
				)
			)
		);
	}

}
