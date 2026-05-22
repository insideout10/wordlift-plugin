<?php
/**
 * Coordinates broken annotation cleanup across posts.
 *
 * @since 3.55.0
 */

namespace Wordlift\Cleanup;

class Annotation_Cleanup_Service {

	/**
	 * @var Broken_Word_Boundary_Annotation_Remover
	 */
	private $remover;

	/**
	 * @var Annotation_Cleanup_Post_Repository
	 */
	private $repository;

	public function __construct( $remover = null, $repository = null ) {
		$this->remover    = isset( $remover ) ? $remover : new Broken_Word_Boundary_Annotation_Remover();
		$this->repository = isset( $repository ) ? $repository : new Annotation_Cleanup_Post_Repository();
	}

	/**
	 * Find affected post IDs using exact PHP scanning.
	 *
	 * @param array $scope Command scope.
	 *
	 * @return int[]
	 */
	public function find_affected_post_ids( $scope ) {
		$affected_ids = array();

		if ( isset( $scope['post_ids'] ) ) {
			foreach ( array_chunk( $scope['post_ids'], $scope['batch_size'] ) as $post_ids ) {
				$this->collect_affected_ids_from_posts(
					$this->repository->get_candidate_posts_by_ids( $post_ids, $scope ),
					$affected_ids
				);
			}

			return $affected_ids;
		}

		$last_id = 0;
		do {
			$posts      = $this->repository->get_candidate_posts_after_id( $last_id, $scope );
			$post_count = count( $posts );
			foreach ( $posts as $post ) {
				$last_id = max( $last_id, (int) $post->ID );
			}
			$this->collect_affected_ids_from_posts( $posts, $affected_ids );
		} while ( $post_count === $scope['batch_size'] );

		return $affected_ids;
	}

	/**
	 * Process affected posts.
	 *
	 * @param int[]         $affected_ids Affected post IDs.
	 * @param int           $batch_size Batch size.
	 * @param bool          $apply Whether changes should be applied.
	 * @param callable|null $progress_callback Optional callback after each processed post.
	 *
	 * @return array{processed:int,would_remove:int,removed:int,failed:int[]}
	 */
	public function process_affected_posts( $affected_ids, $batch_size, $apply, $progress_callback = null ) {
		$stats = array(
			'processed'    => 0,
			'would_remove' => 0,
			'removed'      => 0,
			'failed'       => array(),
		);

		foreach ( array_chunk( $affected_ids, $batch_size ) as $post_ids ) {
			$posts = $this->repository->get_posts_by_ids( $post_ids );
			foreach ( $posts as $post ) {
				$result = $this->remover->remove( $post->post_content );

				if ( $result['changed'] ) {
					$stats['would_remove'] += $result['removed_count'];

					if ( $apply ) {
						$updated = $this->repository->update_post_content( (int) $post->ID, $result['content'] );

						if ( is_wp_error( $updated ) ) {
							$stats['failed'][] = (int) $post->ID;
						} else {
							$stats['removed'] += $result['removed_count'];
						}
					}
				}

				++$stats['processed'];

				if ( is_callable( $progress_callback ) ) {
					call_user_func( $progress_callback, (int) $post->ID );
				}
			}
		}

		return $stats;
	}

	/**
	 * Add affected IDs from candidate posts.
	 *
	 * @param array $posts Candidate posts.
	 * @param array $affected_ids Affected IDs.
	 */
	private function collect_affected_ids_from_posts( $posts, &$affected_ids ) {
		foreach ( $posts as $post ) {
			if ( $this->remover->has_broken_annotations( $post->post_content ) ) {
				$affected_ids[] = (int) $post->ID;
			}
		}
	}
}
