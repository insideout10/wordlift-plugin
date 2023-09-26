<?php

namespace Wordlift\Modules\Dashboard\Match;

use Wordlift\Object_Type_Enum;

abstract class Match_Service {

	/**
	 * @param $content_id
	 * @param $content_type
	 * @param $match_id
	 * @param $jsonld
	 *
	 * @return Match_Entry
	 * @throws \Exception Throw Exception if the jsonld cant be set.
	 */
	public function set_jsonld( $content_id, $content_type, $match_id, $jsonld ) {

		global $wpdb;

		/**
		 * As of May 16 2023, $wpdb:prepare doesnt support null
		 * values in about_jsonld, this results in NULL values being populated
		 * as `null` if we directly pass it to the prepare function(). So its necessary
		 * to make the query conditional based on the $value
		 */
		if ( null === $jsonld ) {
			$wpdb->query(
				$wpdb->prepare(
					"UPDATE {$wpdb->prefix}wl_entities SET about_jsonld = NULL WHERE id = %d AND content_id = %d AND content_type = %d",
					$match_id,
					$content_id,
					$content_type
				)
			);
		} else {
			$wpdb->query(
				$wpdb->prepare(
					"UPDATE {$wpdb->prefix}wl_entities SET about_jsonld = %s WHERE id = %d AND content_id = %d AND content_type = %d",
					wp_json_encode( $jsonld ),
					$match_id,
					$content_id,
					$content_type
				)
			);
		}

		if ( Object_Type_Enum::TERM === $content_type ) {

			$query = "SELECT e.content_id as match_id, e.about_jsonld as match_jsonld,  t.name,  e.id FROM {$wpdb->prefix}wl_entities e
                  LEFT JOIN {$wpdb->prefix}terms t ON e.content_id = t.term_id
                  WHERE  e.id = %d AND e.content_id = %d AND e.content_type = %d";
			// `{$wpdb->prefix}` cant be escaped for preparing.
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$results = $wpdb->get_row( $wpdb->prepare( $query, $match_id, $content_id, $content_type ), ARRAY_A );

			return Match_Entry::from( $results );
		}

		if ( Object_Type_Enum::POST === $content_type ) {

			$query = "SELECT e.content_id as match_id, e.about_jsonld as match_jsonld,  p.post_title AS name,  e.id FROM {$wpdb->prefix}wl_entities e
                  LEFT JOIN {$wpdb->prefix}posts p ON e.content_id = p.ID
                  WHERE  e.id = %d AND e.content_id = %d AND e.content_type = %d";
			// `{$wpdb->prefix}` cant be escaped for preparing.
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$results = $wpdb->get_row( $wpdb->prepare( $query, $match_id, $content_id, $content_type ), ARRAY_A );

			return Match_Entry::from( $results );
		}

	}

}
