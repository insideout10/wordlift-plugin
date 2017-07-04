<?php
/**
 * Tests: Batch Analysis Service.
 *
 * @since   3.14.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Batch_Analysis_Service_Test} class.
 *
 * @since   3.14.0
 * @package Wordlift
 */
class Wordlift_Batch_Analysis_Service_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test post id insertion.
	 *
	 * @since 3.14.0
	 */
	public function test_adding_and_query() {

		$batch_service = new Wordlift_Batch_analysis_Service( new Wordlift() );

		// Test single post addition.
		$batch_service->enqueue_for_analysis( 1, 'default' );

		// test that only one post is returned.
		$queue = $batch_service->waiting_for_analysis();
		$this->assertEquals( 1, count( $queue ) );
		$this->assertEquals( 1, $queue[1]['id'] );
		$this->assertEquals( 'default', $queue[1]['link'] );

		// Test multiple post addition.
		$batch_service->enqueue_for_analysis( array( 3, 4 ), 'no' );

		// test that only one post is returned.
		$queue = $batch_service->waiting_for_analysis();
		$this->assertEquals( 3, count( $queue ) );
		$this->assertEquals( 3, $queue[3]['id'] );
		$this->assertEquals( 'no', $queue[3]['link'] );

		$this->assertEquals( 4, $queue[4]['id'] );
		$this->assertEquals( 'no', $queue[4]['link'] );
	}
}
