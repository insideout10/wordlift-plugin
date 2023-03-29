<?php

namespace Wordlift\Modules\Gardening_Kg\Term_Entity;

use Wordlift\Modules\Dashboard\Stats\Stats_Settings;
use Wordlift\Object_Type_Enum;

class Gardening_Kg_Term_Stats {

	public function register_hooks() {
		add_filter( 'wl_dashboard__stats__settings', array( $this, 'dashboard__stats__settings' ) );
	}

	public function dashboard__stats__settings( $arr ) {

		$data = $this->get_data();

		$arr[] = new Stats_Settings(
			__( 'Boosted Terms are the ones WordLift matched with KG. Some Explanation how it helps them.', 'wordlift' ),
			__( 'Lifted Terms', 'wordlift' ),
			__( 'Terms', 'wordlift' ),
			'#0076f6',
			'../terms',
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
				WHERE e.content_type = %d AND tt.taxonomy IN ( 'post_tag', 'category' )
				",
				Object_Type_Enum::TERM
			)
		);
	}

}
