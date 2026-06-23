<?php
/**
 * Repository for posts that may contain broken annotations.
 *
 * @since 3.55.0
 */

namespace Wordlift\Cleanup;

class Annotation_Cleanup_Post_Repository {

	/**
	 * Get candidate posts after an ID.
	 *
	 * @param int   $last_id Last seen ID.
	 * @param array $scope Command scope.
	 *
	 * @return array
	 */
	public function get_candidate_posts_after_id( $last_id, $scope ) {
		global $wpdb;

		$params = array_merge(
			array( $last_id ),
			$scope['post_types'],
			$scope['post_statuses'],
			array( '%' . $wpdb->esc_like( 'textannotation' ) . '%', $scope['batch_size'] )
		);

		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber
		$sql      = "SELECT ID, post_content, post_type FROM $wpdb->posts
			WHERE ID > %d
			AND post_type IN (" . $this->placeholders( $scope['post_types'] ) . ')
			AND post_status IN (' . $this->placeholders( $scope['post_statuses'] ) . ')
			AND post_content LIKE %s
			ORDER BY ID
			LIMIT %d';
		$prepared = $wpdb->prepare( $sql, $params );
		// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber

		return $wpdb->get_results(
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$prepared
		);
	}

	/**
	 * Get candidate posts for specific IDs.
	 *
	 * @param int[] $post_ids Post IDs.
	 * @param array $scope Command scope.
	 *
	 * @return array
	 */
	public function get_candidate_posts_by_ids( $post_ids, $scope ) {
		global $wpdb;

		$params = array_merge(
			$post_ids,
			$scope['post_types'],
			$scope['post_statuses'],
			array( '%' . $wpdb->esc_like( 'textannotation' ) . '%' )
		);

		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber
		$sql      = "SELECT ID, post_content, post_type FROM $wpdb->posts
			WHERE ID IN (" . $this->placeholders( $post_ids, '%d' ) . ')
			AND post_type IN (' . $this->placeholders( $scope['post_types'] ) . ')
			AND post_status IN (' . $this->placeholders( $scope['post_statuses'] ) . ')
			AND post_content LIKE %s
			ORDER BY ID';
		$prepared = $wpdb->prepare( $sql, $params );
		// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber

		return $wpdb->get_results(
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$prepared
		);
	}

	/**
	 * Get posts for IDs.
	 *
	 * @param int[] $post_ids Post IDs.
	 *
	 * @return array
	 */
	public function get_posts_by_ids( $post_ids ) {
		global $wpdb;

		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber
		$sql      = "SELECT ID, post_content, post_type FROM $wpdb->posts
			WHERE ID IN (" . $this->placeholders( $post_ids, '%d' ) . ')
			ORDER BY ID';
		$prepared = $wpdb->prepare( $sql, $post_ids );
		// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber

		return $wpdb->get_results(
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$prepared
		);
	}

	/**
	 * Update post content.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $content New post content.
	 *
	 * @return int|\WP_Error
	 */
	public function update_post_content( $post_id, $content ) {
		return wp_update_post(
			array(
				'ID'           => $post_id,
				'post_content' => wp_slash( $content ),
			),
			true
		);
	}

	/**
	 * Build a placeholder list.
	 *
	 * @param array  $values Values.
	 * @param string $placeholder Placeholder.
	 *
	 * @return string
	 */
	private function placeholders( $values, $placeholder = '%s' ) {
		return implode( ',', array_fill( 0, count( $values ), $placeholder ) );
	}
}
