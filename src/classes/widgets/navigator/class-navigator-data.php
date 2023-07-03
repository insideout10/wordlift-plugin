<?php

namespace Wordlift\Widgets\Navigator;

/**
 * @since 3.27.8
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Navigator_Data {

	public static function get_post_types_as_string( $post_types ) {
		if ( array() === $post_types ) {
			$post_types = get_post_types();
		}
		$post_types = array_map(
			function ( $post_type ) {
				return "'" . esc_sql( $post_type ) . "'";
			},
			$post_types
		);

		return implode( ',', $post_types );
	}

	public static function post_navigator_get_results(
		$post_id, $fields = array(
			'ID',
			'post_title',
		), $order_by = 'ID DESC', $limit = 10, $offset = 0, $post_types = array()
	) {

		$post_types = self::get_post_types_as_string( $post_types );
		global $wpdb;

		$select = implode(
			', ',
			array_map(
				function ( $item ) {
					return "p.$item AS $item";
				},
				(array) $fields
			)
		);

		$order_by = implode(
			', ',
			array_map(
				function ( $item ) {
					return "p.$item";
				},
				(array) $order_by
			)
		);
// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
		/** @noinspection SqlNoDataSourceInspection */
		return $wpdb->get_results(
			$wpdb->prepare( // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber
				"
SELECT %4\$s, p2.ID as entity_id
 FROM {$wpdb->prefix}wl_relation_instances r1
    INNER JOIN {$wpdb->prefix}wl_relation_instances r2
        ON r2.object_id = r1.object_id
            AND r2.subject_id != %1\$d
	-- get the ID of the post entity in common between the object and the subject 2. 
    INNER JOIN {$wpdb->posts} p2
        ON p2.ID = r2.object_id
            AND p2.post_status = 'publish'
            AND p2.post_type IN ($post_types)
    INNER JOIN {$wpdb->posts} p
        ON p.ID = r2.subject_id
            AND p.post_status = 'publish'
            AND p.post_type IN ($post_types)
    INNER JOIN {$wpdb->term_relationships} tr
     	ON tr.object_id = p.ID
    INNER JOIN {$wpdb->term_taxonomy} tt
     	ON tt.term_taxonomy_id = tr.term_taxonomy_id
      	    AND tt.taxonomy = 'wl_entity_type'
    INNER JOIN {$wpdb->terms} t
        ON t.term_id = tt.term_id
            AND t.slug = 'article'
    -- select only posts with featured images.
    INNER JOIN {$wpdb->postmeta} m
        ON m.post_id = p.ID
            AND m.meta_key = '_thumbnail_id'
 WHERE r1.subject_id = %1\$d
 -- avoid duplicates.
 GROUP BY p.ID
 ORDER BY %5\$s
 LIMIT %2\$d
 OFFSET %3\$d
",
				$post_id,
				$limit,
				$offset,
				$select,
				$order_by
			)
		);
// phpcs:enable
	}

	public static function entity_navigator_get_results(
		$post_id, $fields = array(
			'ID',
			'post_title',
		), $order_by = 'ID DESC', $limit = 10, $offset = 0, $post_types = array()
	) {
		global $wpdb;

		$select = implode(
			', ',
			array_map(
				function ( $item ) {
					return "p.$item AS $item";
				},
				(array) $fields
			)
		);

		$order_by = implode(
			', ',
			array_map(
				function ( $item ) {
					return "p.$item";
				},
				(array) $order_by
			)
		);

		$post_types = self::get_post_types_as_string( $post_types );
// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
		/** @noinspection SqlNoDataSourceInspection */
		return $wpdb->get_results(
			$wpdb->prepare(
				"
SELECT %4\$s, p2.ID as entity_id
 FROM {$wpdb->prefix}wl_relation_instances r1
	-- get the ID of the post entity in common between the object and the subject 2. 
    INNER JOIN {$wpdb->posts} p2
        ON p2.ID = r1.object_id
            AND p2.post_status = 'publish'
             AND p2.post_type IN ($post_types)
    INNER JOIN {$wpdb->posts} p
        ON p.ID = r1.subject_id
            AND p.post_status = 'publish'
             AND p.post_type IN ($post_types)
    INNER JOIN {$wpdb->term_relationships} tr
     	ON tr.object_id = p.ID
    INNER JOIN {$wpdb->term_taxonomy} tt
     	ON tt.term_taxonomy_id = tr.term_taxonomy_id
      	    AND tt.taxonomy = 'wl_entity_type'
    INNER JOIN {$wpdb->terms} t
        ON t.term_id = tt.term_id
            AND t.slug = 'article'
    -- select only posts with featured images.
    INNER JOIN {$wpdb->postmeta} m
        ON m.post_id = p.ID
            AND m.meta_key = '_thumbnail_id'
 WHERE r1.object_id = %1\$d
 -- avoid duplicates.
 GROUP BY p.ID
 ORDER BY %5\$s
 LIMIT %2\$d
 OFFSET %3\$d
",
				$post_id,
				$limit,
				$offset,
				$select,
				$order_by
			)
		);
	}
// phpcs:enable
}

