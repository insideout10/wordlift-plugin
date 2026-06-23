<?php
/**
 * Fake repository for annotation cleanup service tests.
 */

class Annotation_Cleanup_Service_Test_Repository extends \Wordlift\Cleanup\Annotation_Cleanup_Post_Repository {

	public $requested_id_chunks = array();

	public $updates = array();

	private $posts;

	private $failing_ids;

	public function __construct( $posts, $failing_ids = array() ) {
		$this->posts       = array();
		$this->failing_ids = $failing_ids;

		foreach ( $posts as $post ) {
			$this->posts[ $post->ID ] = $post;
		}
	}

	public function get_candidate_posts_after_id( $last_id, $scope ) {
		$posts = array();
		foreach ( $this->posts as $post ) {
			if ( $post->ID > $last_id ) {
				$posts[] = $post;
			}
		}

		return array_slice( $posts, 0, $scope['batch_size'] );
	}

	public function get_candidate_posts_by_ids( $post_ids, $scope ) {
		unset( $scope );

		$this->requested_id_chunks[] = $post_ids;

		return $this->get_posts_by_ids( $post_ids );
	}

	public function get_posts_by_ids( $post_ids ) {
		$posts = array();
		foreach ( $post_ids as $post_id ) {
			if ( isset( $this->posts[ $post_id ] ) ) {
				$posts[] = $this->posts[ $post_id ];
			}
		}

		return $posts;
	}

	public function update_post_content( $post_id, $content ) {
		if ( in_array( $post_id, $this->failing_ids, true ) ) {
			return new WP_Error( 'annotation_cleanup_failed', 'Update failed.' );
		}

		$this->updates[ $post_id ] = $content;

		return $post_id;
	}
}
