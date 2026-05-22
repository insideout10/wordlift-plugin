<?php
/**
 * Test annotation cleanup workflow.
 *
 * @group backend
 */
require_once __DIR__ . '/class-annotation-cleanup-service-test-repository.php';

class Annotation_Cleanup_Service_Test extends Wordlift_Unit_Test_Case {

	public function test_finds_affected_ids_using_post_id_scope() {
		$repository = new Annotation_Cleanup_Service_Test_Repository(
			array(
				$this->post( 1, 'post', 'plug<span class="textannotation">in</span>' ),
				$this->post( 2, 'post', '<span class="textannotation">plugin</span>' ),
				$this->post( 3, 'post', 'AP<span class="textannotation">I</span>' ),
			)
		);
		$service    = new \Wordlift\Cleanup\Annotation_Cleanup_Service( null, $repository );

		$affected_ids = $service->find_affected_post_ids(
			array(
				'post_ids'      => array( 1, 2, 3 ),
				'post_types'    => array( 'post' ),
				'post_statuses' => array( 'publish' ),
				'batch_size'    => 2,
			)
		);

		$this->assertSame( array( 1, 3 ), $affected_ids );
		$this->assertSame( array( array( 1, 2 ), array( 3 ) ), $repository->requested_id_chunks );
	}

	public function test_dry_run_reports_would_remove_without_updating() {
		$repository = new Annotation_Cleanup_Service_Test_Repository(
			array(
				$this->post( 1, 'post', 'plug<span class="textannotation">in</span>' ),
			)
		);
		$service    = new \Wordlift\Cleanup\Annotation_Cleanup_Service( null, $repository );
		$ticks      = array();

		$stats = $service->process_affected_posts(
			array( 1 ),
			1,
			false,
			function ( $post_id ) use ( &$ticks ) {
				$ticks[] = $post_id;
			}
		);

		$this->assertSame( 1, $stats['processed'] );
		$this->assertSame( 1, $stats['would_remove'] );
		$this->assertSame( 0, $stats['removed'] );
		$this->assertSame( array(), $stats['failed'] );
		$this->assertSame( array(), $repository->updates );
		$this->assertSame( array( 1 ), $ticks );
	}

	public function test_apply_updates_content_and_counts_removed_after_success() {
		$repository = new Annotation_Cleanup_Service_Test_Repository(
			array(
				$this->post( 1, 'post', 'plug<span class="textannotation">in</span>' ),
			)
		);
		$service    = new \Wordlift\Cleanup\Annotation_Cleanup_Service( null, $repository );

		$stats = $service->process_affected_posts( array( 1 ), 1, true );

		$this->assertSame( 1, $stats['processed'] );
		$this->assertSame( 1, $stats['would_remove'] );
		$this->assertSame( 1, $stats['removed'] );
		$this->assertSame( array(), $stats['failed'] );
		$this->assertSame( 'plugin', $repository->updates[1] );
	}

	public function test_apply_reports_failed_updates_without_counting_removed() {
		$repository = new Annotation_Cleanup_Service_Test_Repository(
			array(
				$this->post( 1, 'post', 'plug<span class="textannotation">in</span>' ),
			),
			array( 1 )
		);
		$service    = new \Wordlift\Cleanup\Annotation_Cleanup_Service( null, $repository );

		$stats = $service->process_affected_posts( array( 1 ), 1, true );

		$this->assertSame( 1, $stats['processed'] );
		$this->assertSame( 1, $stats['would_remove'] );
		$this->assertSame( 0, $stats['removed'] );
		$this->assertSame( array( 1 ), $stats['failed'] );
	}

	public function test_repository_preserves_backslashes_when_updating_content() {
		$post_id = wp_insert_post(
			array(
				'post_title'   => 'Backslash test',
				'post_content' => wp_slash( 'C:\Temp plugin' ),
				'post_status'  => 'publish',
			)
		);

		$repository = new \Wordlift\Cleanup\Annotation_Cleanup_Post_Repository();
		$repository->update_post_content( $post_id, 'C:\Temp plugin' );

		$this->assertSame( 'C:\Temp plugin', get_post_field( 'post_content', $post_id, 'raw' ) );
	}

	private function post( $id, $post_type, $post_content ) {
		return (object) array(
			'ID'           => $id,
			'post_type'    => $post_type,
			'post_content' => $post_content,
		);
	}
}
