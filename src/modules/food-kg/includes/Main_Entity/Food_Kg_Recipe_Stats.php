<?php

namespace Wordlift\Modules\Food_Kg\Main_Entity;

use Wordlift\Modules\Dashboard\Post_Entity_Match\Recipe_Query;
use Wordlift\Modules\Dashboard\Stats\Stats_Settings;
use Wordlift\Object_Type_Enum;

class Food_Kg_Recipe_Stats {

	public function register_hooks() {
		add_filter( 'wl_dashboard__stats__settings', array( $this, 'dashboard__stats__settings' ) );
	}

	public function dashboard__stats__settings( $arr ) {

		$data = Recipe_Query::get_data(); // $this->get_data();

		$arr[] = new Stats_Settings(
			__( 'Boosted Recipes are the ones WordLift matched the main ingredient with the Knowledge Graph. This helps Search Engines understand your content better and boost your rankings.', 'wordlift' ),
			__( 'Lifted Recipes', 'wordlift' ),
			__( 'Recipes', 'wordlift' ),
			'#00c48c',
			'../recipes',
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
				SELECT COUNT(1) AS total, COUNT(e.about_jsonld) as lifted
				FROM {$wpdb->prefix}posts p
				LEFT JOIN {$wpdb->prefix}wl_entities e
					ON e.content_id = p.ID
						AND e.content_type = %d 
				WHERE p.post_type = %s
				",
				Object_Type_Enum::POST,
				'wprm_recipe'
			)
		);
	}

}
