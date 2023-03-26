<?php
namespace Wordlift\Modules\Dashboard\Api;

use PHPUnit\Util\Exception;
use Wordlift\Object_Type_Enum;

class Match_Service {

	/**
	 * @param $taxonomy string The taxonomy name
	 * @param $position int The position of the item, usually id used for pagination.
	 * @param $limit int The number of results to return
	 * @param $direction string @enum { 'FORWARD', 'BACKWARD'}
	 * @param $sort string @enum { 'ASC', 'DESC'}
	 *
	 * @return array|array[]
	 * @throws \Exception
	 */

	public function get_term_matches( $taxonomy, $position, $limit, $direction, $sort ) {

		$operator = Page::FORWARD === $direction ? '>=' : '<=';

		if ( ! in_array( $sort, array( Page::SORT_ASC, Page::SORT_DESC ) ) ) {
			throw new \Exception( 'Invalid sort order specified' );
		}

		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT t.term_id as id, e.about_jsonld as match_jsonld,  t.name,  e.id AS match_id FROM {$wpdb->prefix}terms t
     INNER JOIN {$wpdb->prefix}term_taxonomy tt ON t.term_id = tt.term_id
     LEFT JOIN {$wpdb->prefix}wl_entities e ON t.term_id = e.content_id
     WHERE e.content_type = %d AND tt.taxonomy = %s AND t.term_id {$operator} %d ORDER BY t.term_id {$sort} LIMIT %d",
			Object_Type_Enum::TERM,
			$taxonomy,
			$position,
			$limit
		);

		return array_map(
			function ( $e ) {
				return Match_Entry::from( $e )->serialize();
			},
			$wpdb->get_results(
				$query,
				ARRAY_A
			)
		);
	}

	/**
	 * @param $post_type string The post type
	 * @param $position int The position of the item, usually id used for pagination.
	 * @param $limit int The number of results to return
	 * @param $direction string @enum { 'FORWARD', 'BACKWARD'}
	 * @param $sort string @enum { 'ASC', 'DESC'}
	 *
	 * @return array|array[]
	 * @throws \Exception
	 */
	public function get_post_matches( $post_type, $position, $limit, $direction, $sort ) {
		global $wpdb;

		$operator = $direction === Page::FORWARD ? '>=' : '<=';

		if ( ! in_array( $sort, array( Page::SORT_ASC, Page::SORT_DESC ) ) ) {
			throw new \Exception( 'Invalid sort order specified' );
		}

		$query = $wpdb->prepare(
			"SELECT e.content_id as id, e.about_jsonld as match_jsonld, parent.post_title as name, p.post_title as recipe_name, e.id AS match_id 
FROM {$wpdb->prefix}posts p 
INNER JOIN {$wpdb->prefix}postmeta pm ON p.ID = pm.post_id AND pm.meta_key = 'wprm_parent_post_id' 
INNER JOIN {$wpdb->prefix}posts parent ON pm.meta_value = parent.ID 
LEFT JOIN {$wpdb->prefix}wl_entities e ON p.ID = e.content_id 
WHERE e.content_type = %d AND p.post_type = %s AND p.ID {$operator} %d AND pm.meta_value IS NOT NULL 
ORDER BY p.ID {$sort} LIMIT %d;",
			Object_Type_Enum::POST,
			$post_type,
			$position,
			$limit
		);

		return array_map(
			function ( $e ) {
				return array_merge(
					Match_Entry::from( $e )->serialize(),
					array(
						'recipe_name'    => $e['recipe_name'],
						'post_permalink' => get_permalink( $e['id'] ),
					)
				);
			},
			$wpdb->get_results(
				$query,
				ARRAY_A
			)
		);
	}

	/**
	 * @param $content_id
	 * @param $content_type
	 * @param $match_id
	 * @param $jsonld
	 *
	 * @return Match_Entry
	 * @throws \Exception
	 */
	public function set_jsonld( $content_id, $content_type, $match_id, $jsonld ) {

		global $wpdb;
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->prefix}wl_entities SET about_jsonld = %s WHERE id = %d AND content_id = %d AND content_type = %d",
				wp_json_encode( $jsonld ),
				$match_id,
				$content_id,
				$content_type
			)
		);

		if ( Object_Type_Enum::TERM === $content_type ) {
			$query   = "SELECT e.content_id as match_id, e.about_jsonld as match_jsonld,  t.name,  e.id FROM {$wpdb->prefix}wl_entities e
                  LEFT JOIN {$wpdb->prefix}terms t ON e.content_id = t.term_id
                  WHERE  e.id = %d AND e.content_id = %d AND e.content_type = %d";
			$results = $wpdb->get_row( $wpdb->prepare( $query, $match_id, $content_id, $content_type ), ARRAY_A );

			return Match_Entry::from( $results );
		}

		if ( Object_Type_Enum::POST === $content_type ) {
			$query   = "SELECT e.content_id as match_id, e.about_jsonld as match_jsonld,  p.post_title AS name,  e.id FROM {$wpdb->prefix}wl_entities e
                  LEFT JOIN {$wpdb->prefix}posts p ON e.content_id = p.ID
                  WHERE  e.id = %d AND e.content_id = %d AND e.content_type = %d";
			$results = $wpdb->get_row( $wpdb->prepare( $query, $match_id, $content_id, $content_type ), ARRAY_A );

			return Match_Entry::from( $results );
		}

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
