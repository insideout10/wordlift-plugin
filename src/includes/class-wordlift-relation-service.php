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

	/**
	 * The singleton instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Relation_Service $instance The singleton instance.
	 */
	private static $instance;

	/**
	 * The relation table name in MySQL, set during instantiation.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var string $relation_table The relation table name.
	 */
	private $relation_table;

	/**
	 * Create a {@link Wordlift_Relation_Service} instance.
	 *
	 * @since 3.15.0
	 */
	public function __construct() {
		global $wpdb;

		// The relations table.
		$this->relation_table = "{$wpdb->prefix}wl_relation_instances";

		self::$instance = $this;

	}

	/**
	 * Get the singleton instance.
	 *
	 * @since  3.15.0
	 * @access public
	 * @return \Wordlift_Relation_Service The {@link Wordlift_Relation_Service}
	 *                                    singleton instance.
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Get the articles referencing the specified entity {@link WP_Post}.
	 *
	 * @since 3.15.0
	 *
	 * @param int|array   $object_id The entity {@link WP_Post}'s id.
	 * @param string      $fields    The fields to return, 'ids' to only return ids or
	 *                               '*' to return all fields, by default '*'.
	 * @param null|string $predicate The predicate (who|what|...), by default all.
	 * @param null|string $status    The status, by default all.
	 * @param array       $excludes  An array of ids to exclude from the results.
	 * @param null|int    $limit     The maximum number of results, by default
	 *                               no limit.
	 * @param null|array  $include   The {@link WP_Post}s' ids to include.
	 *
	 * @return array|object|null Database query results
	 */
	public function get_article_subjects( $object_id, $fields = '*', $predicate = null, $status = null, $excludes = array(), $limit = null, $include = null ) {
		global $wpdb;

		// The output fields.
		$actual_fields = self::fields( $fields );

		$objects = $this->article_id_to_entity_id( $object_id );

		$sql =
			"
			SELECT DISTINCT p.$actual_fields
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
			. self::and_post_type_in()
			. self::and_predicate( $predicate )
			. self::limit( $limit );


		return '*' === $actual_fields ? $wpdb->get_results( $sql ) : $wpdb->get_col( $sql );
	}

	/**
	 * The `post_type IN` clause.
	 *
	 * @since 3.15.3
	 *
	 * @return string The `post_type IN` clause.
	 */
	private static function and_post_type_in() {

		return " AND p.post_type IN ( '"
			   . implode(
				   "','",
				   array_map( 'esc_sql', Wordlift_Entity_Service::valid_entity_post_types() )
			   )
			   . "' )";
	}

	/**
	 * Add the limit clause if specified.
	 *
	 * @since 3.15.0
	 *
	 * @param null|int $limit The maximum number of results.
	 *
	 * @return string The limit clause (empty if no limit has been specified).
	 */
	private static function limit( $limit = null ) {

		if ( null === $limit ) {
			return '';
		}

		return "LIMIT $limit";
	}

	/**
	 * Map the provided ids into entities (i.e. return the id if it's an entity
	 * or get the entities if it's a post).
	 *
	 * @since 3.15.0
	 *
	 * @param int|array $object_id An array of posts/entities' ids.
	 *
	 * @return array An array of entities' ids.
	 */
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

	/**
	 * Add the WHERE clause.
	 *
	 * @since 3.15.0
	 *
	 * @param int|array $object_id An array of {@link WP_Post}s' ids.
	 *
	 * @return string The WHERE clause.
	 */
	private static function where_object_id( $object_id ) {

		return ' WHERE r.object_id IN ( ' . implode( ',', wp_parse_id_list( (array) $object_id ) ) . ' )';
	}

	/**
	 * Add the exclude clause.
	 *
	 * @since 3.15.0
	 *
	 * @param int|array $exclude An array of {@link WP_Post}s' ids to exclude.
	 *
	 * @return string The exclude clause.
	 */
	private static function and_article_not_in( $exclude ) {

		return ' AND NOT p.ID IN ( ' . implode( ',', wp_parse_id_list( (array) $exclude ) ) . ' )';
	}

	/**
	 * Add the include clause.
	 *
	 * @since 3.15.0
	 *
	 * @param null|int|array $include An array of {@link WP_Post}s' ids.
	 *
	 * @return string An empty string if $include is null otherwise the include
	 *                clause.
	 */
	private static function and_article_in( $include = null ) {

		if ( null === $include ) {
			return '';
		}

		return ' AND p.ID IN ( ' . implode( ',', wp_parse_id_list( (array) $include ) ) . ' )';
	}

	/**
	 * Get the entities' {@link WP_Post}s' ids referencing the specified {@link WP_Post}.
	 *
	 * @since 3.15.0
	 *
	 * @param int         $object_id The object {@link WP_Post}'s id.
	 * @param string      $fields    The fields to return, 'ids' to only return ids or
	 *                               '*' to return all fields, by default '*'.
	 * @param null|string $status    The status, by default all.
	 *
	 * @return array|object|null Database query results
	 */
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
			. self::inner_join_is_not_article()
			. " WHERE r.object_id = %d "
			. self::and_post_type_in()
			,
			$object_id
		);

		return '*' === $actual_fields ? $wpdb->get_results( $sql ) : $wpdb->get_col( $sql );
	}

	/**
	 * Get the entities referenced by the specified {@link WP_Post}.
	 *
	 * @since 3.15.0
	 *
	 * @param int         $subject_id The {@link WP_Post}'s id.
	 * @param string      $fields     The fields to return, 'ids' to only return ids or
	 *                                '*' to return all fields, by default '*'.
	 * @param null|string $predicate  The predicate (who|what|...), by default all.
	 * @param null|string $status     The status, by default all.
	 *
	 * @return array|object|null Database query results
	 */
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
			. " WHERE r.subject_id = %d "
			. self::and_post_type_in()
			. self::and_predicate( $predicate )
			,
			$subject_id
		);

		return '*' === $actual_fields ? $wpdb->get_results( $sql ) : $wpdb->get_col( $sql );
	}

	/**
	 * Add the `post_status` clause.
	 *
	 * @since 3.15.0
	 *
	 * @param null|string|array $status The status values.
	 *
	 * @return string An empty string if $status is null, otherwise the status clause.
	 */
	private static function and_status( $status = null ) {

		if ( null === $status ) {
			return '';
		}

		return " AND p.post_status IN ('" . implode( "', '", array_map( 'esc_sql', (array) $status ) ) . "')";
	}

	/**
	 * Add the `predicate` clause.
	 *
	 * @since 3.15.0
	 *
	 * @param null|string|array $predicate An array of predicates.
	 *
	 * @return string An empty string if $predicate is null otherwise the predicate
	 *                clause.
	 */
	private static function and_predicate( $predicate = null ) {

		if ( null === $predicate ) {
			return '';
		}

		return " AND r.predicate IN ('" . implode( "', '", array_map( 'esc_sql', (array) $predicate ) ) . "')";
	}

	/**
	 * The select fields.
	 *
	 * @since 3.15.0
	 *
	 * @param string $fields Either 'ids' or '*', by default '*'.
	 *
	 * @return string The `id` field if `ids` otherwise `*`.
	 */
	private static function fields( $fields = '*' ) {

		// The output fields.
		return 'ids' === $fields ? 'id' : '*';
	}

	/**
	 * The inner join clause for articles.
	 *
	 * @since 3.15.0
	 *
	 * @return string The articles inner join clause.
	 */
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

	/**
	 * The inner join clause for non-articles.
	 *
	 * @since 3.15.0
	 *
	 * @return string The non-articles inner join clause.
	 */
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
