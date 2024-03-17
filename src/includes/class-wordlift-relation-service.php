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
	 * The relation table name in MySQL, set during instantiation.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var string $relation_table The relation table name.
	 */
	private $relation_table;

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since 3.15.3
	 *
	 * @var Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private static $log;

	/**
	 * Create a {@link Wordlift_Relation_Service} instance.
	 *
	 * @since 3.15.0
	 */
	protected function __construct() {
		global $wpdb;

		self::$log = Wordlift_Log_Service::get_logger( get_class() );

		// The relations table.
		$this->relation_table = "{$wpdb->prefix}wl_relation_instances";

	}

	/**
	 * The singleton instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var Wordlift_Relation_Service $instance The singleton instance.
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return Wordlift_Relation_Service The {@link Wordlift_Relation_Service} singleton instance.
	 * @since  3.15.0
	 * @access public
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get the articles referencing the specified entity {@link WP_Post}.
	 *
	 * @param int|array     $object_id The entity {@link WP_Post}'s id.
	 * @param string        $fields The fields to return, 'ids' to only return ids or
	 *                                             '*' to return all fields, by default '*'.
	 * @param null|string   $predicate The predicate (who|what|...), by default all.
	 * @param null|string   $status The status, by default all.
	 * @param array         $excludes An array of ids to exclude from the results.
	 * @param null|int      $limit The maximum number of results, by default
	 *                                         no limit.
	 * @param null|array    $include The {@link WP_Post}s' ids to include.
	 *
	 * @param null | string $order_by
	 *
	 * @param array         $post_types
	 *
	 * @return array|object|null Database query results
	 * @since 3.15.0
	 */
	public function get_article_subjects( $object_id, $fields = '*', $predicate = null, $status = null, $excludes = array(), $limit = null, $include = null, $order_by = null, $post_types = array(), $offset = null ) {
		global $wpdb;

		// The output fields.
		$actual_fields = self::fields( $fields );

		self::$log->trace( 'Getting article subjects for object ' . implode( ', ', (array) $object_id ) . '...' );

		$objects = $this->article_id_to_entity_id( $object_id );

		// If there are no related objects, return an empty array.
		if ( empty( $objects ) ) {
			self::$log->debug( 'No entities found for object ' . implode( ', ', (array) $object_id ) . '.' );

			return array();
		}

		self::$log->debug( count( $objects ) . ' entity id(s) found for object ' . implode( ', ', (array) $object_id ) . '.' );

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
			. self::and_post_type_in( $post_types )
			. self::and_predicate( $predicate )
			. self::order_by( $order_by )
			. self::limit( $limit )
			. self::offset( $offset );

		return '*' === $actual_fields ? $wpdb->get_results( $sql ) : $wpdb->get_col( $sql );  // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * The `post_type IN` clause.
	 *
	 * @param array $post_types If the post type is not provided then the valid
	 * entity post types are used.
	 *
	 * @return string The `post_type IN` clause.
	 * @since 3.15.3
	 */
	private static function and_post_type_in( $post_types = array() ) {

		if ( array() === $post_types ) {
			$post_types = Wordlift_Entity_Service::valid_entity_post_types();
		}

		return " AND p.post_type IN ( '"
			. implode(
				"','",
				array_map( 'esc_sql', $post_types )
			)
			   . "' )";
	}

	/**
	 * Add the limit clause if specified.
	 *
	 * @param null|int $limit The maximum number of results.
	 *
	 * @return string The limit clause (empty if no limit has been specified).
	 * @since 3.15.0
	 */
	private static function limit( $limit = null ) {

		if ( null === $limit ) {
			return '';
		}

		return "LIMIT $limit";
	}

	/**
	 * Add the OFFSET clause if specified.
	 *
	 * @param null|int $offset The number of results to skip.
	 *
	 * @return string The offset clause (empty if no offset has been specified).
	 * @since 3.35.11
	 */
	private static function offset( $offset = null ) {

		if ( null === $offset || ! is_numeric( $offset ) ) {
			return '';
		}

		return " OFFSET $offset";
	}

	/**
	 * @param $order_by string | null
	 *
	 * @return string
	 */
	private static function order_by( $order_by ) {
		if ( ! $order_by ) {
			return '';
		}
		$order_by         = (string) $order_by;
		$order_by_clauses = array( 'DESC', 'ASC' );

		if ( in_array( $order_by, $order_by_clauses, true ) ) {
			return " ORDER BY p.post_modified {$order_by} ";
		} else {
			return ' ORDER BY p.post_modified DESC ';
		}
	}

	/**
	 * Map the provided ids into entities (i.e. return the id if it's an entity
	 * or get the entities if it's a post).
	 *
	 * @param int|array $object_id An array of posts/entities' ids.
	 *
	 * @return array An array of entities' ids.
	 * @since 3.15.0
	 */
	private function article_id_to_entity_id( $object_id ) {

		$entity_service = Wordlift_Entity_Service::get_instance();

		$relation_service = $this;

		return array_reduce(
			(array) $object_id,
			function ( $carry, $item ) use ( $entity_service, $relation_service ) {
				if ( $entity_service->is_entity( $item ) ) {
					return array_merge( $carry, (array) $item );
				}

				return array_merge( $carry, $relation_service->get_objects( $item, 'ids' ) );
			},
			array()
		);

	}

	/**
	 * Add the WHERE clause.
	 *
	 * @param int|array $object_id An array of {@link WP_Post}s' ids.
	 *
	 * @return string The WHERE clause.
	 * @since 3.15.0
	 */
	private static function where_object_id( $object_id ) {

		if ( empty( $object_id ) ) {
			// self::$log->warn( sprintf( "%s `where_object_id` called with empty `object_id`.", var_export( debug_backtrace( false, 3 ), true ) ) );

			return ' WHERE 1 = 1';
		}

		return ' WHERE r.object_id IN ( ' . implode( ',', wp_parse_id_list( (array) $object_id ) ) . ' )';
	}

	/**
	 * Add the exclude clause.
	 *
	 * @param int|array $exclude An array of {@link WP_Post}s' ids to exclude.
	 *
	 * @return string The exclude clause.
	 * @since 3.15.0
	 */
	private static function and_article_not_in( $exclude ) {

		return ' AND NOT p.ID IN ( ' . implode( ',', wp_parse_id_list( (array) $exclude ) ) . ' )';
	}

	/**
	 * Add the include clause.
	 *
	 * @param null|int|array $include An array of {@link WP_Post}s' ids.
	 *
	 * @return string An empty string if $include is null otherwise the include
	 *                clause.
	 * @since 3.15.0
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
	 * @param int         $object_id The object {@link WP_Post}'s id.
	 * @param string      $fields The fields to return, 'ids' to only return ids or
	 *                                         '*' to return all fields, by default '*'.
	 * @param null|string $status The status, by default all.
	 *
	 * @return array|object|null Database query results
	 * @since 3.15.0
	 */
	public function get_non_article_subjects( $object_id, $fields = '*', $status = null ) {
		global $wpdb;

		// The output fields.
		$actual_fields = self::fields( $fields );

		$sql = $wpdb->prepare(
			"SELECT p.$actual_fields FROM {$wpdb->prefix}wl_relation_instances r INNER JOIN $wpdb->posts p ON p.id = r.subject_id" // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			// Add the status clause.
			. self::and_status( $status ) // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			. self::inner_join_is_not_article() // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			. ' WHERE r.object_id = %d '
			. self::and_post_type_in(), // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$object_id
		);

		return '*' === $actual_fields ? $wpdb->get_results( $sql ) : $wpdb->get_col( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * Get the entities referenced by the specified {@link WP_Post}.
	 *
	 * @param int         $subject_id The {@link WP_Post}'s id.
	 * @param string      $fields The fields to return, 'ids' to only return ids or
	 *                                          '*' to return all fields, by default '*'.
	 * @param null|string $predicate The predicate (who|what|...), by default all.
	 * @param null|string $status The status, by default all.
	 *
	 * @return array|object|null Database query results
	 *
	 * @deprecated since it doesn't handle the subject_type nor the object_type
	 * @since 3.15.0
	 */
	public function get_objects( $subject_id, $fields = '*', $predicate = null, $status = null ) {
		global $wpdb;

		// The output fields.
		$actual_fields = self::fields( $fields );

		$sql = $wpdb->prepare(
			"SELECT p.$actual_fields FROM {$this->relation_table} r INNER JOIN $wpdb->posts p ON p.id = r.object_id" // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			// Add the status clause.
			. self::and_status( $status ) // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			. self::inner_join_is_not_article() // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			. ' WHERE r.subject_id = %d '
			. self::and_post_type_in() // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			. self::and_predicate( $predicate ), // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$subject_id
		);

		return '*' === $actual_fields ? $wpdb->get_results( $sql ) : $wpdb->get_col( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * Add the `post_status` clause.
	 *
	 * @param null|string|array $status The status values.
	 *
	 * @return string An empty string if $status is null, otherwise the status clause.
	 * @since 3.15.0
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
	 * @param null|string|array $predicate An array of predicates.
	 *
	 * @return string An empty string if $predicate is null otherwise the predicate
	 *                clause.
	 * @since 3.15.0
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
	 * @param string $fields Either 'ids' or '*', by default '*'.
	 *
	 * @return string The `id` field if `ids` otherwise `*`.
	 * @since 3.15.0
	 */
	private static function fields( $fields = '*' ) {

		// The output fields.
		return 'ids' === $fields ? 'id' : '*';
	}

	/**
	 * The inner join clause for articles.
	 *
	 * @return string The articles inner join clause.
	 * @since 3.15.0
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
	 * @return string The non-articles inner join clause.
	 * @since 3.15.0
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

	/**
	 * Find all the subject IDs and their referenced/related object IDs. The
	 * object IDs are returned as comma separated IDs in the `object_ids` key.
	 *
	 * @return mixed Database query results
	 * @since 3.18.0
	 */
	public function find_all_grouped_by_subject_id() {
		global $wpdb;

		return $wpdb->get_results(
			"
			SELECT subject_id, GROUP_CONCAT( DISTINCT object_id ORDER BY object_id SEPARATOR ',' ) AS object_ids
			FROM {$wpdb->prefix}wl_relation_instances
			GROUP BY subject_id
			"
		);

	}

}
