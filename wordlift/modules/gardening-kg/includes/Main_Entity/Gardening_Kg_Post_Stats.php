<?php

namespace Wordlift\Modules\Gardening_Kg\Main_Entity;

use Wordlift\Escape;
use Wordlift\Modules\Dashboard\Stats\Stats_Settings;
use Wordlift\Object_Type_Enum;

class Gardening_Kg_Post_Stats {

	public function register_hooks() {
		add_filter( 'wl_dashboard__stats__settings', array( $this, 'dashboard__stats__settings' ) );
	}

	public function dashboard__stats__settings( $arr ) {

		$data = $this->get_data();

		$arr[] = new Stats_Settings(
			__( 'Boosted Posts are the ones WordLift matched the main topic with the Knowledge Graph. This helps Search Engines understand your content better and boost your rankings.', 'wordlift' ),
			__( 'Lifted Posts', 'wordlift' ),
			__( 'Posts', 'wordlift' ),
			'#00c48c',
			'../posts',
			(int) $data->total,
			(int) $data->lifted
		);

		return $arr;
	}

	public function get_data() {
		global $wpdb;
		$post_types     = apply_filters(
			'wl_dashboard__post_entity_match__post_types',
			array(
				'post',
				'page',
			)
		);
		$post_types_sql = Escape::sql_array( $post_types );

		// $post_types_sql is already escaped using esc_sql.
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $wpdb->get_row(
			$wpdb->prepare(
				"
				SELECT COUNT(1) AS total, COUNT(e.about_jsonld) as lifted
				FROM {$wpdb->prefix}posts p
				LEFT JOIN {$wpdb->prefix}wl_entities e
					ON e.content_id = p.ID
						AND e.content_type = %d
				WHERE p.post_type IN ({$post_types_sql})
				",
				Object_Type_Enum::POST
			)
		);
		// phpcs:enable
	}

}
