<?php
/**
 * Query entities by title
 *
 * @see https://github.com/insideout10/wordlift-plugin/issues/1574
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.38.0
 */

namespace Wordlift\Entity\Query;

use Wordlift\Content\Wordpress\Wordpress_Content;
use Wordlift\Object_Type_Enum;
use Wordlift\Term\Type_Service;

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
				"SELECT DISTINCT t.term_id as id, t.name as title, tm.meta_value as schema_type_name FROM $wpdb->terms t INNER JOIN $wpdb->termmeta tm
    ON t.term_id=tm.term_id WHERE t.name LIKE %s AND (tm.meta_key = %s AND tm.meta_value IN ($schema_types)) LIMIT %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				'%' . $wpdb->esc_like( $query ) . '%',
				\Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
				$limit
			)
		);

	}

	/**
	 * @param $results
	 * @param $object_type
	 *
	 * @return  Entity[]
	 */
	private function transform_posts( $results ) {
		return array_map(
			function ( $item ) {
				return new Entity( $item->schema_type_name, new Wordpress_Content( get_post( $item->id ) ) );
			},
			$results
		);
	}

	private function transform_terms( $results ) {
		return array_map(
			function ( $item ) {
				return new Entity( $item->schema_type_name, new Wordpress_Content( get_term( $item->id ) ) );
			},
			$results
		);
	}

	/**
	 * @param $query
	 * @param $schema_types
	 * @param $limit
	 *
	 * @return Entity[]
	 */
	public function query( $query, $schema_types = array(), $limit = 10 ) {

		$results = $this->transform_posts( $this->query_posts( $query, $schema_types, $limit ) );

		if ( count( $results ) >= $limit ) {
			return $results;
		}

		$results = array_merge( $results, $this->transform_terms( $this->query_terms( $query, $schema_types, $limit ) ) );

		return $results;

	}

	public function get( $linked_entities ) {

		/**
		 * @var $term_type_service Type_Service
		 */
		$term_type_service        = Type_Service::get_instance();
		$post_entity_type_service = \Wordlift_Entity_Type_Service::get_instance();

		return array_filter(
			array_map(
				function ( $item ) use ( $term_type_service, $post_entity_type_service ) {
					$parts      = explode( '_', $item );
					$type       = Object_Type_Enum::from_string( $parts[0] );
					$identifier = $parts[1];

					if ( Object_Type_Enum::POST === $type ) {
						return new Entity( join( ',', $post_entity_type_service->get_names( $identifier ) ), new Wordpress_Content( get_post( $identifier ) ) );
					} elseif ( Object_Type_Enum::TERM === $type ) {

						return new Entity( join( ',', $term_type_service->get_entity_types_labels( $identifier ) ), new Wordpress_Content( get_term( $identifier ) ) );
					}

					// return new Entity( $item->schema_type_name, new Wordpress_Content( get_term( $item->id ) ) );
				},
				$linked_entities
			)
		);

	}

}
