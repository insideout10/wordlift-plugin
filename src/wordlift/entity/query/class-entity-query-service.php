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
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT t.term_id as id, t.name as title FROM $wpdb->terms t WHERE t.name = %s",
				$query
			)
		);

	}

	public function query( $query, $schema_types = array() , $limit = 10 ) {

		$results = $this->query_posts( $query, $schema_types, $limit );

		if ( count( $results ) >= $limit ) {
			return $results;
		}

		$results = array_merge( $results, $this->query_terms($query, $schema_types, $limit ) );

		return $results;

	}


}