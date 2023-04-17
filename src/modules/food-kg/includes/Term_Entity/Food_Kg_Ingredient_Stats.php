<?php

namespace Wordlift\Modules\Food_Kg\Term_Entity;

use Wordlift\Modules\Dashboard\Stats\Stats_Settings;
use Wordlift\Object_Type_Enum;

class Food_Kg_Ingredient_Stats {

	public function register_hooks() {
		add_filter( 'wl_dashboard__stats__settings', array( $this, 'dashboard__stats__settings' ) );
	}

	public function dashboard__stats__settings( $arr ) {

		$data = $this->get_data();

		$arr[] = new Stats_Settings(
			__( 'Boosted Ingredient are the ones WordLift matched with KG. Some Explanation how it helps them.', 'wordlift' ),
			__( 'Lifted Ingredients', 'wordlift' ),
			__( 'Ingredients', 'wordlift' ),
			'#0076f6',
			'../ingredients',
			(int) $data->total,
			(int) $data->lifted
		);

		return $arr;
	}

	public function get_data() {
		global $wpdb;

		return $wpdb->get_row(
			$wpdb->prepare(
				"
				SELECT COUNT(1) as total, COUNT(e.about_jsonld) AS lifted
			    FROM {$wpdb->prefix}terms t 
			    INNER JOIN {$wpdb->prefix}term_taxonomy tt 
			        ON t.term_id = tt.term_id
			    LEFT JOIN {$wpdb->prefix}wl_entities e
			        ON e.content_id = t.term_id
				WHERE e.content_type = %d AND tt.taxonomy = %s
				",
				Object_Type_Enum::TERM,
				'wprm_ingredient'
			)
		);
	}

}
