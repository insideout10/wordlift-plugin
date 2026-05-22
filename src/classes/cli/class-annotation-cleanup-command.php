<?php
/**
 * WP-CLI command to clean broken WordLift annotations.
 *
 * @since 3.55.0
 */

namespace Wordlift\Cli;

use Wordlift\Cleanup\Annotation_Cleanup_Service;

class Annotation_Cleanup_Command {

	const DEFAULT_BATCH_SIZE = 100;

	/**
	 * @var Annotation_Cleanup_Service
	 */
	private $cleanup_service;

	public function __construct( $cleanup_service = null ) {
		$this->cleanup_service = isset( $cleanup_service ) ? $cleanup_service : new Annotation_Cleanup_Service();
	}

	/**
	 * Remove WordLift text annotations that split words.
	 *
	 * ## OPTIONS
	 *
	 * [--apply]
	 * : Apply changes. Defaults to dry-run.
	 *
	 * [--debug]
	 * : Print affected post IDs, one per line.
	 *
	 * [--post_type=<post_type>]
	 * : Comma-separated post types. Defaults to post,page.
	 *
	 * [--post_status=<post_status>]
	 * : Comma-separated post statuses. Defaults to publish.
	 *
	 * [--post_ids=<post_ids>]
	 * : Comma-separated post IDs. Defaults to all IDs in scope.
	 *
	 * [--include-revisions]
	 * : Allow revision post type cleanup when post_type includes revision.
	 *
	 * [--batch-size=<batch_size>]
	 * : Number of posts to scan or update per batch. Defaults to 100.
	 *
	 * @param array $args Positional arguments.
	 * @param array $assoc_args Associative arguments.
	 */
	public function __invoke( $args, $assoc_args ) {
		$scope = $this->parse_scope( $assoc_args );

		\WP_CLI::log(
			sprintf(
				'Scanning post_type=%s post_status=%s%s',
				implode( ',', $scope['post_types'] ),
				implode( ',', $scope['post_statuses'] ),
				isset( $scope['post_ids'] ) ? sprintf( ' post_ids=%d requested', count( $scope['post_ids'] ) ) : ''
			)
		);

		$affected_ids = $this->cleanup_service->find_affected_post_ids( $scope );
		$total        = count( $affected_ids );

		if ( isset( $scope['post_ids'] ) ) {
			\WP_CLI::log(
				sprintf(
					'Requested IDs: %d. Matching affected IDs: %d.',
					count( $scope['post_ids'] ),
					$total
				)
			);
		}

		if ( ! empty( $assoc_args['debug'] ) ) {
			foreach ( $affected_ids as $post_id ) {
				\WP_CLI::line( (string) $post_id );
			}
		}

		if ( 0 === $total ) {
			\WP_CLI::success( 'No posts with broken WordLift annotations found.' );
			return;
		}

		$apply    = ! empty( $assoc_args['apply'] );
		$progress = \WP_CLI\Utils\make_progress_bar( 'Cleaning broken annotations', $total );
		$stats    = $this->cleanup_service->process_affected_posts(
			$affected_ids,
			$scope['batch_size'],
			$apply,
			function () use ( $progress ) {
				$progress->tick();
			}
		);
		$progress->finish();

		\WP_CLI::log(
			sprintf(
				'%s %d post(s), would remove %d broken annotation(s), removed %d, failed %d post(s).',
				$apply ? 'Updated' : 'Dry-run checked',
				$stats['processed'],
				$stats['would_remove'],
				$stats['removed'],
				count( $stats['failed'] )
			)
		);

		if ( ! empty( $stats['failed'] ) ) {
			\WP_CLI::warning( 'Failed post IDs: ' . implode( ',', $stats['failed'] ) );
			if ( $apply ) {
				\WP_CLI::error( 'Cleanup completed with failed updates.' );
			}
		}

		if ( $apply ) {
			\WP_CLI::success( 'Cleanup complete.' );
		} else {
			\WP_CLI::success( 'Dry-run complete. Run with --apply to update posts.' );
		}
	}

	/**
	 * Parse and validate command scope.
	 *
	 * @param array $assoc_args Associative arguments.
	 *
	 * @return array{post_types:string[],post_statuses:string[],post_ids?:int[],batch_size:int}
	 */
	private function parse_scope( $assoc_args ) {
		$post_types = $this->parse_csv_keys( isset( $assoc_args['post_type'] ) ? $assoc_args['post_type'] : 'post,page' );
		if ( empty( $post_types ) ) {
			\WP_CLI::error( 'No valid post types provided.' );
		}

		if ( in_array( 'revision', $post_types, true ) && empty( $assoc_args['include-revisions'] ) ) {
			\WP_CLI::error( 'Cleaning revisions requires --include-revisions.' );
		}

		if ( in_array( 'revision', $post_types, true ) && ! empty( $assoc_args['apply'] ) ) {
			\WP_CLI::error( 'Applying cleanup to revisions is not supported because WordPress may resolve revision IDs to parent posts.' );
		}

		$post_statuses = $this->parse_csv_keys( isset( $assoc_args['post_status'] ) ? $assoc_args['post_status'] : 'publish' );
		if ( empty( $post_statuses ) ) {
			\WP_CLI::error( 'No valid post statuses provided.' );
		}

		$batch_size = isset( $assoc_args['batch-size'] ) ? absint( $assoc_args['batch-size'] ) : self::DEFAULT_BATCH_SIZE;
		if ( 1 > $batch_size ) {
			\WP_CLI::error( 'Batch size must be a positive integer.' );
		}

		$scope = array(
			'post_types'    => $post_types,
			'post_statuses' => $post_statuses,
			'batch_size'    => $batch_size,
		);

		if ( isset( $assoc_args['post_ids'] ) ) {
			$post_ids = $this->parse_post_ids( $assoc_args['post_ids'] );
			if ( empty( $post_ids ) ) {
				\WP_CLI::error( 'No valid positive post IDs provided.' );
			}
			$scope['post_ids'] = $post_ids;
		}

		return $scope;
	}

	/**
	 * Parse a comma-separated list of keys.
	 *
	 * @param string $value CSV value.
	 *
	 * @return string[]
	 */
	private function parse_csv_keys( $value ) {
		$values = array_filter(
			array_map(
				'sanitize_key',
				array_map( 'trim', explode( ',', (string) $value ) )
			)
		);

		return array_values( array_unique( $values ) );
	}

	/**
	 * Parse a comma-separated post ID list.
	 *
	 * @param string $value CSV value.
	 *
	 * @return int[]
	 */
	private function parse_post_ids( $value ) {
		$ids = array_map( 'absint', array_map( 'trim', explode( ',', (string) $value ) ) );
		$ids = array_filter(
			$ids,
			function ( $id ) {
				return 0 < $id;
			}
		);

		return array_values( array_unique( $ids ) );
	}
}
