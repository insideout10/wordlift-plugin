<?php
/**
 * Services: Relation Service.
 *
 * The Relation Service provides helpful function to get related posts/entities.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Define the {@link Wordlift_Relation_Service} class.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
class Wordlift_Relation_Service {

	private static $instance;

	private $relation_table;

	/**
	 * Wordlift_Relation_Service constructor.
	 */
	public function __construct() {
		global $wpdb;

		// The relations table.
		$this->relation_table = "{$wpdb->prefix}wl_relation_instances";

		self::$instance = $this;

	}

	public static function get_instance() {

		return self::$instance;
	}


	public function get_article_subjects( $object_id, $fields = '*', $predicate = null, $status = null, $excludes = array(), $limit = null, $include = null ) {
		global $wpdb;

		// The output fields.
		$actual_fields = self::fields( $fields );

		$objects = $this->article_id_to_entity_id( $object_id );

		$sql = "
			SELECT p.$actual_fields
			FROM {$this->relation_table} r
			INNER JOIN $wpdb->posts p
				ON p.id = r.subject_id
			"
		       // Add the status clause.
		       . self::and_status( $status )
		       . self::inner_join_is_article()
		       . self::where_object_id( $objects )
		       // Since `object_id` can be an article ID we need to exclude it from
		       // the results.
		       . self::and_article_not_in( array_merge( $excludes, (array) $object_id ) )
		       . self::and_article_in( $include )
		       . " AND p.post_type IN ( 'post', 'page', 'entity' ) "
		       . self::and_predicate( $predicate )
		       . self::limit( $limit );

		return '*' === $actual_fields ? $wpdb->get_results( $sql ) : $wpdb->get_col( $sql );
	}

	private static function limit( $limit = null ) {

		if ( null === $limit ) {
			return '';
		}

		return "LIMIT $limit";
	}

	private function article_id_to_entity_id( $object_id ) {

		$entity_service = Wordlift_Entity_Service::get_instance();

		$relation_service = $this;

		return array_reduce( (array) $object_id, function ( $carry, $item ) use ( $entity_service, $relation_service ) {
			if ( $entity_service->is_entity( $item ) ) {
				return array_merge( $carry, (array) $item );
			}

			return array_merge( $carry, $relation_service->get_objects( $item, 'ids' ) );
		}, array() );

	}

	private static function where_object_id( $object_id ) {

		return ' WHERE r.object_id IN ( ' . implode( ',', wp_parse_id_list( (array) $object_id ) ) . ' )';
	}

	private static function and_article_not_in( $exclude ) {

		return ' AND NOT p.ID IN ( ' . implode( ',', wp_parse_id_list( (array) $exclude ) ) . ' )';
	}

	private static function and_article_in( $include = null ) {

		if ( null === $include ) {
			return '';
		}

		return ' AND p.ID IN ( ' . implode( ',', wp_parse_id_list( (array) $include ) ) . ' )';
	}

	public function get_non_article_subjects( $object_id, $fields = '*', $status = null ) {
		global $wpdb;

		// The output fields.
		$actual_fields = self::fields( $fields );

		$sql = $wpdb->prepare(
			"
			SELECT p.$actual_fields
			FROM {$this->relation_table} r
			INNER JOIN $wpdb->posts p
				ON p.id = r.subject_id
			"
			// Add the status clause.
			. self::and_status( $status )
			. self::inner_join_is_not_article() .
			"
			WHERE r.object_id = %d
				AND p.post_type IN ( 'post', 'page', 'entity' )
			"
			,
			$object_id
		);

		return '*' === $actual_fields ? $wpdb->get_results( $sql ) : $wpdb->get_col( $sql );
	}

	public function get_objects( $subject_id, $fields = '*', $predicate = null, $status = null ) {
		global $wpdb;

		// The output fields.
		$actual_fields = self::fields( $fields );

		$sql = $wpdb->prepare(
			"
			SELECT p.$actual_fields
			FROM {$this->relation_table} r
			INNER JOIN $wpdb->posts p
				ON p.id = r.object_id
			"
			// Add the status clause.
			. self::and_status( $status )
			. self::inner_join_is_not_article()
			. "
			WHERE r.subject_id = %d
				AND p.post_type IN ( 'post', 'page', 'entity' )
			"
			. self::and_predicate( $predicate )
			,
			$subject_id
		);

		return '*' === $actual_fields ? $wpdb->get_results( $sql ) : $wpdb->get_col( $sql );
	}

	private static function and_status( $status = null ) {

		if ( null === $status ) {
			return '';
		}

		return " AND p.post_status IN ('" . implode( "', '", array_map( 'esc_sql', (array) $status ) ) . "')";
	}

	private static function and_predicate( $predicate = null ) {

		if ( null === $predicate ) {
			return '';
		}

		return " AND r.predicate IN ('" . implode( "', '", array_map( 'esc_sql', (array) $predicate ) ) . "')";
	}

	private static function fields( $fields = '*' ) {

		// The output fields.
		return 'ids' === $fields ? 'id' : '*';
	}

	private static function inner_join_is_article() {
		global $wpdb;

		return $wpdb->prepare(
			"
			INNER JOIN $wpdb->term_relationships tr
			 ON p.id = tr.object_id
			INNER JOIN $wpdb->term_taxonomy tt
			 ON tt.term_taxonomy_id = tr.term_taxonomy_id
			  AND tt.taxonomy = %s
			INNER JOIN $wpdb->terms t
			 ON t.term_id = tt.term_id
			  AND t.slug = %s
			",
			'wl_entity_type',
			'article'
		);
	}

	private static function inner_join_is_not_article() {
		global $wpdb;

		return $wpdb->prepare(
			"
			INNER JOIN $wpdb->term_relationships tr
			 ON p.id = tr.object_id
			INNER JOIN $wpdb->term_taxonomy tt
			 ON tt.term_taxonomy_id = tr.term_taxonomy_id
			  AND tt.taxonomy = %s
			INNER JOIN $wpdb->terms t
			 ON t.term_id = tt.term_id
			  AND NOT t.slug = %s
			",
			'wl_entity_type',
			'article'
		);
	}

}
