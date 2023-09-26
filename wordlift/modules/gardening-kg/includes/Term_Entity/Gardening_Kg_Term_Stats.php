<?php

namespace Wordlift\Modules\Gardening_Kg\Term_Entity;

use Wordlift\Escape;
use Wordlift\Modules\Dashboard\Stats\Stats_Settings;
use Wordlift\Object_Type_Enum;

class Gardening_Kg_Term_Stats {

	public function register_hooks() {
		add_filter( 'wl_dashboard__stats__settings', array( $this, 'dashboard__stats__settings' ) );
	}

	public function dashboard__stats__settings( $arr ) {

		$data = $this->get_data();

		$arr[] = new Stats_Settings(
			__( 'Boosted Terms are the ones WordLift matched with the Knowledge Graph. This helps Search Engines understand your content better and boost your rankings.', 'wordlift' ),
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
		$taxonomies     = apply_filters(
			'wl_dashboard__post_entity_match__taxonomies',
			array(
				'post_tag',
				'category',
			)
		);
		$taxonomies_sql = Escape::sql_array( $taxonomies );
		return $wpdb->get_row(
		// $taxonomies_sql is already escaped using esc_sql.
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$wpdb->prepare(
				"
				SELECT COUNT(1) as total, COUNT(e.about_jsonld) AS lifted
			    FROM {$wpdb->prefix}terms t 
			    INNER JOIN {$wpdb->prefix}term_taxonomy tt 
			        ON t.term_id = tt.term_id
			    LEFT JOIN {$wpdb->prefix}wl_entities e
			        ON e.content_id = t.term_id
			    		AND e.content_type = %d 
			    WHERE tt.taxonomy IN ( {$taxonomies_sql} )
				",
				Object_Type_Enum::TERM
			)
		);
		// phpcs:enable
	}

}
