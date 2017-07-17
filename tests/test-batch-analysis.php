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

	private $http_filter_stage;
	private $analyze_sim_response;
	private $query_sim_response;
	private $analyzed_request;

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

	/**
	 * Simulate response
	 *
	 */
	public function simulate_response( $preempt, $request, $url ) {

		if ( 'analyze' == $this->http_filter_stage ) {
			$this->http_filter_stage = 'query';
			$this->analyzed_request = $request;
			return $this->analyze_sim_response;
		} else {
			return $this->query_sim_response;
		}
	}

	/**
	 * Test sending analysis request and handling response.
	 *
	 * @since 3.14.0
	 */
	public function test_send_analyze_request() {
		$batch_service = new Wordlift_Batch_analysis_Service( new Wordlift() );

		add_filter( 'pre_http_request', array( $this, 'simulate_response' ), 10, 3 );

		/*
		 * Test behavior with non existing posts.
		 * No message should be sent, queues should be cleared.
		 */
		$this->http_filter_stage = 'analyze';
		$this->analyze_sim_response = array();
		$this->query_sim_response = array();
		$this->analyzed_request = null;
		wp_clear_scheduled_hook( 'wl_batch_analyze' );

		$batch_service->enqueue_for_analysis( 1, 'default' );

		// Test just queue entry/exit.
		$batch_service->batch_analyze();

		$queue = $batch_service->waiting_for_analysis();
		$this->assertEmpty( $queue );

		$queue = $batch_service->waiting_for_response();
		$this->assertEmpty( $queue );

		// No event should be scheduled
		$this->assertFalse( wp_next_scheduled( 'wl_batch_analyze' ) );

		// No message was sent
		$this->assertEquals( 'analyze', $this->http_filter_stage );

		/*
		 * Handling the waiting queue should send an analyze request to the server
		 * and move the item from the waiting to the processing queue.
		 */

		$post_id = $this->factory->post->create( array(
			 'post_type'    => 'post',
			 'post_content' => 'test content',
			 'post_title'   => 'test post',
			 'post_status'  => 'publish',
		) );

		$this->http_filter_stage = 'analyze';
		$this->analyze_sim_response = array();
		$this->query_sim_response = array(
			'response' => array( 'code' => 200 ),
			'body' => json_encode( array( 'content' => 'analyzed' ) ),
		);
		$this->analyzed_request = null;
		wp_clear_scheduled_hook( 'wl_batch_analyze' );

		$batch_service->enqueue_for_analysis( $post_id, 'default' );

		// Test just queue entry/exit.
		$batch_service->batch_analyze();

		$queue = $batch_service->waiting_for_analysis();
		$this->assertEmpty( $queue );

		$queue = $batch_service->waiting_for_response();
		$this->assertEmpty( $queue );

		// No event should be scheduled
		$this->assertFalse( wp_next_scheduled( 'wl_batch_analyze' ) );

		$analyze_request = json_decode( $this->analyzed_request['body'] );
		$this->assertEquals( 'test content', $analyze_request->content );
		$this->assertEquals( $post_id, $analyze_request->id );
		$this->assertEquals( 'default', $analyze_request->links );
		$this->assertEquals( 'en', $analyze_request->contentLanguage );
		$this->assertEquals( 'local', $analyze_request->scope );
		// $this->assertEquals( '', $analyze_request->version ); TBD

		$post = get_post( $post_id );
		$this->assertEquals( 'analyzed', $post->post_content );

		/*
		 * Handling the response not ready case for the query.
		 */

		$post_id = $this->factory->post->create( array(
			 'post_type'    => 'post',
			 'post_content' => 'test content',
			 'post_title'   => 'test post',
			 'post_status'  => 'publish',
		) );

		$this->http_filter_stage = 'analyze';
		$this->analyze_sim_response = array();
		$this->query_sim_response = array(
			'response' => array( 'code' => 500 ),
			'body' => json_encode( array( 'content' => 'analyzed' ) ),
		);
		$this->analyzed_request = null;
		wp_clear_scheduled_hook( 'wl_batch_analyze' );

		$batch_service->enqueue_for_analysis( $post_id, 'default' );

		// Test just queue entry/exit.
		$batch_service->batch_analyze();

		// The query failing, sends the request into the waiting queue.
		$queue = $batch_service->waiting_for_analysis();
		$this->assertEquals( 1, count( $queue ) );
		$this->assertEquals( $post_id, $queue[ $post_id ]['id'] );
		$this->assertEquals( 'default', $queue[ $post_id ]['link'] );

		$queue = $batch_service->waiting_for_response();
		$this->assertEmpty( $queue );

		// An event should be scheduled
		$this->assertNotFalse( wp_next_scheduled( 'wl_batch_analyze' ) );

		// No response, no change in content.
		$post = get_post( $post_id );
		$this->assertEquals( 'test content', $post->post_content );
	}

}
