<?php

namespace Wordlift\Modules\Dashboard\Stats;

use Wordlift\Modules\Common\Symfony\Component\Config\Definition\Exception\Exception;
use Wordlift\Object_Type_Enum;

class Stats {


	public function taxonomy( $taxonomy ) {
		return $this->get( Object_Type_Enum::TERM, $taxonomy );
	}


	public function post_type( $post_type ) {
		return $this->get( Object_Type_Enum::TERM, $post_type );
	}

	/**
	 * @param $type int
	 * @param $identifier string
	 *
	 * @return array()
	 */
	private function get( $type, $identifier ) {

		if ( $type === Object_Type_Enum::TERM ) {
			global $wpdb;
			$query = $wpdb->prepare(
				"SELECT count(1) as total, count(e.about_jsonld) as lifted FROM {$wpdb->prefix}wl_entities e
                  LEFT JOIN {$wpdb->prefix}terms t ON e.content_id = t.term_id
                  INNER JOIN {$wpdb->prefix}term_taxonomy tt ON t.term_id = tt.term_id
                  WHERE e.content_type = %d AND tt.taxonomy = %s",
				$type,
				$identifier
			);

			return $wpdb->get_row( $query, ARRAY_A );
		}

		if ( $type === Object_Type_Enum::POST ) {
			global $wpdb;
			$query = $wpdb->prepare(
				"SELECT count(1) as total, count(e.about_jsonld) as lifted FROM {$wpdb->prefix}wl_entities e
                  LEFT JOIN {$wpdb->prefix}posts p ON e.content_id = p.ID
                  WHERE e.content_type = %d AND p.post_type = %s",
				$type,
				$identifier
			);

			return $wpdb->get_row( $query, ARRAY_A );
		}

		throw new Exception( 'get_stats() not supported for other object types' );
	}


}
