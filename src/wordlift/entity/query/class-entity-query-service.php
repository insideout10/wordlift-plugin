<?php
/**
 * Query entities by title
 *
 * @see https://github.com/insideout10/wordlift-plugin/issues/1574
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.38.0
 */

namespace Wordlift\Entity\Query;


class Entity_Query_Service {


	private static $instance = null;

	/**
	 * The singleton instance.
	 *
	 * @return Entity_Query_Service
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	private function query_posts( $query, $schema_types, $limit ) {
		return wl_entity_get_by_title( $query, true, true, $limit, $schema_types );
	}

	private function query_terms( $query, $schema_types, $limit ) {
		global $wpdb;
		$schema_types = join(
			',',
			array_map(
				function ( $schema_type ) {
					return "'" . esc_sql( strtolower( $schema_type ) ) . "'";
				},
				$schema_types
			)
		);

		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT t.term_id as id, t.name as title FROM $wpdb->terms t INNER JOIN $wpdb->termmeta tm
    ON t.term_id=tm.term_id WHERE t.name LIKE %s AND (tm.meta_key = %s AND tm.meta_value IN ($schema_types))",
				'%' . $wpdb->esc_like( $query ) . '%',
				\Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME
			)
		);

	}

	public function query( $query, $schema_types = array(), $limit = 10 ) {

		$results = $this->query_posts( $query, $schema_types, $limit );

		if ( count( $results ) >= $limit ) {
			return $results;
		}

		$results = array_merge( $results, $this->query_terms( $query, $schema_types, $limit ) );

		return $results;

	}


}