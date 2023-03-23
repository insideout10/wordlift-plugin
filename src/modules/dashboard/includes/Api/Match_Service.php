<?php
namespace Wordlift\Modules\Dashboard\Api;

use PHPUnit\Util\Exception;
use Wordlift\Object_Type_Enum;

class Match_Service {


	/**
	 * @param $content_id
	 * @param $match_id
	 * @param $jsonld
	 *
	 * @return Match
	 * @throws \Exception
	 */
	public function set_jsonld( $content_id, $match_id, $jsonld ) {

		global $wpdb;
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->prefix}wl_entities SET about_jsonld = %s WHERE id = %d AND content_id = %d",
				wp_json_encode( $jsonld ),
				$match_id,
				$content_id
			)
		);

		$query = "SELECT e.content_id as match_id, e.about_jsonld as match_jsonld,  t.name,  e.id FROM {$wpdb->prefix}wl_entities e
                  LEFT JOIN {$wpdb->prefix}terms t ON e.content_id = t.term_id
                  WHERE  e.id = %d AND content_id = %d";

		return Match::from( $wpdb->get_row( $wpdb->prepare( $query, $match_id, $content_id ), ARRAY_A ) );

	}

	/**
	 * @param $content_id int
	 * @param $content_type int
	 *
	 * @return int
	 */
	public function get_id( $content_id, $content_type ) {
		global $wpdb;
		$result = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM {$wpdb->prefix}wl_entities WHERE content_id = %d AND content_type = %d",
				$content_id,
				$content_type
			)
		);
		if ( null === $result ) {
			throw new Exception( "Unable to find match id for {$content_id} and {$content_type}" );
		}
		return $result;
	}


}
