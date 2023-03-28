<?php

namespace Wordlift\Modules\Dashboard\Stats;

use Wordlift\Object_Type_Enum;

class Stats {

	public function taxonomy( $taxonomy ) {
		return $this->get( Object_Type_Enum::TERM, $taxonomy );
	}

	public function post_type( $post_type ) {
		return $this->get( Object_Type_Enum::POST, $post_type );
	}

	/**
	 * @param $type int
	 * @param $identifier string
	 *
	 * @return array()
	 */
	private function get( $type, $identifier ) {
		global $wpdb;

		if ( $type === Object_Type_Enum::TERM ) {
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
					$type,
					$identifier
				),
				ARRAY_A
			);
		}

		if ( $type === Object_Type_Enum::POST ) {
			return $wpdb->get_row(
				$wpdb->prepare(
					"
				SELECT COUNT(1) AS total, COUNT(e.about_jsonld) as lifted
				FROM {$wpdb->prefix}posts p
				LEFT JOIN {$wpdb->prefix}wl_entities e
					ON e.content_id = p.ID
				WHERE e.content_type = %d AND p.post_type = %s
				",
					$type,
					$identifier
				),
				ARRAY_A
			);
		}

		return null;
	}

}
