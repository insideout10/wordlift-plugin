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
	 * The {@link Wordlift_Batch_Analysis_Service} to test.
	 *
	 * @since  3.14.2
	 * @access private
	 * @var \Wordlift_Batch_Analysis_Service $batch_analysis_service The {@link Wordlift_Batch_Analysis_Service} to test.
	 */
	private $batch_analysis_service;

	/**
	 * @inheritdoc
	 */
	function setUp() {
		parent::setUp();

		$this->batch_analysis_service = new Wordlift_Batch_Analysis_Service( new Wordlift(), Wordlift_Configuration_Service::get_instance() );

	}

	public function test_submit_default_values() {

		$post_ids = $this->factory->post->create_many( 2 );

		$count = $this->batch_analysis_service->submit( array() );

		$this->assertEquals( 2, $count );

		foreach ( $post_ids as $id ) {
			$options = get_post_meta( $id, Wordlift_Batch_Analysis_Service::BATCH_ANALYSIS_OPTIONS_META_KEY, true );

			$this->assertEquals( array(
				'link'            => 'default',
				'min_occurrences' => 1,
			), $options );
		}

	}

	public function test_submit_link_yes() {

		$post_ids = $this->factory->post->create_many( 2 );

		$count = $this->batch_analysis_service->submit( array( 'link' => 'yes' ) );

		$this->assertEquals( 2, $count );

		foreach ( $post_ids as $id ) {
			$options = get_post_meta( $id, Wordlift_Batch_Analysis_Service::BATCH_ANALYSIS_OPTIONS_META_KEY, true );

			$this->assertEquals( array(
				'link'            => 'yes',
				'min_occurrences' => 1,
			), $options );
		}

	}

	public function test_submit_link_no() {

		$post_ids = $this->factory->post->create_many( 2 );

		$count = $this->batch_analysis_service->submit( array( 'link' => 'no' ) );

		$this->assertEquals( 2, $count );

		foreach ( $post_ids as $id ) {
			$options = get_post_meta( $id, Wordlift_Batch_Analysis_Service::BATCH_ANALYSIS_OPTIONS_META_KEY, true );

			$this->assertEquals( array(
				'link'            => 'no',
				'min_occurrences' => 1,
			), $options );
		}

	}

	/**
	 * @expectedException WPDieException
	 */
	public function test_submit_link_unknown_value() {

		$this->factory->post->create_many( 2 );

		$this->batch_analysis_service->submit( array( 'link' => 'unknown' ) );

	}

	public function test_submit_min_occurrences_one() {

		$post_ids = $this->factory->post->create_many( 2 );

		$count = $this->batch_analysis_service->submit( array( 'min_occurrences' => 1 ) );

		$this->assertEquals( 2, $count );

		foreach ( $post_ids as $id ) {
			$options = get_post_meta( $id, Wordlift_Batch_Analysis_Service::BATCH_ANALYSIS_OPTIONS_META_KEY, true );

			$this->assertEquals( array(
				'link'            => 'default',
				'min_occurrences' => 1,
			), $options );
		}

	}

	public function test_submit_min_occurrences_greater_than_one() {

		$post_ids = $this->factory->post->create_many( 2 );

		$count = $this->batch_analysis_service->submit( array( 'min_occurrences' => 2 ) );

		$this->assertEquals( 2, $count );

		foreach ( $post_ids as $id ) {
			$options = get_post_meta( $id, Wordlift_Batch_Analysis_Service::BATCH_ANALYSIS_OPTIONS_META_KEY, true );

			$this->assertEquals( array(
				'link'            => 'default',
				'min_occurrences' => 2,
			), $options );
		}

	}

	/**
	 * @expectedException WPDieException
	 */
	public function test_submit_min_occurrences_not_a_number() {

		$this->factory->post->create_many( 2 );

		$this->batch_analysis_service->submit( array( 'min_occurrences' => 'a' ) );

	}

	/**
	 * @expectedException WPDieException
	 */
	public function test_submit_min_occurrences_less_than_one() {

		$this->factory->post->create_many( 2 );

		$this->batch_analysis_service->submit( array( 'min_occurrences' => 0 ) );

	}

	public function test_submit_posts_default_options() {

		$posts = $this->create_test_posts();

		### TEST DEFAULTS.

		$this->batch_analysis_service->submit( array() );

		$results = get_posts( array(
			'fields'         => 'ids',
			'meta_key'       => Wordlift_Batch_Analysis_Service::STATE_META_KEY,
			'meta_value'     => Wordlift_Batch_Analysis_Service::STATE_SUBMIT,
			'posts_per_page' => - 1,
			'post_type'      => 'any',
		) );

		// `post` w/ annotations not included.
		$this->assertFalse( in_array( $posts[0], $results ) );
		// `post` w/o annotations included.
		$this->assertTrue( in_array( $posts[1], $results ) );
		// `page` not included.
		$this->assertFalse( in_array( $posts[2], $results ) );
		// `post` w/o annotations modified 2017-01-01 included.
		$this->assertTrue( in_array( $posts[3], $results ) );
		// `post` w/o annotations modified 2018-01-01 included.
		$this->assertTrue( in_array( $posts[4], $results ) );

	}

	public function test_submit_posts_post_type_page() {

		$posts = $this->create_test_posts();

		### TEST DEFAULTS.

		$this->batch_analysis_service->submit( array( 'post_type' => 'page' ) );

		$results = get_posts( array(
			'fields'         => 'ids',
			'meta_key'       => Wordlift_Batch_Analysis_Service::STATE_META_KEY,
			'meta_value'     => Wordlift_Batch_Analysis_Service::STATE_SUBMIT,
			'posts_per_page' => - 1,
			'post_type'      => 'any',
		) );

		// `post` w/ annotations not included.
		$this->assertFalse( in_array( $posts[0], $results ) );
		// `post` w/o annotations included.
		$this->assertFalse( in_array( $posts[1], $results ) );
		// `page` included.
		$this->assertTrue( in_array( $posts[2], $results ) );
		// `post` w/o annotations modified 2017-01-01 included.
		$this->assertFalse( in_array( $posts[3], $results ) );
		// `post` w/o annotations modified 2018-01-01 included.
		$this->assertFalse( in_array( $posts[4], $results ) );

	}

	public function test_submit_posts_include_annotated() {

		$posts = $this->create_test_posts();

		### INCLUDE ANNOTATED.

		$this->batch_analysis_service->submit( array( 'include_annotated' => true ) );

		$results = get_posts( array(
			'fields'         => 'ids',
			'meta_key'       => Wordlift_Batch_Analysis_Service::STATE_META_KEY,
			'meta_value'     => Wordlift_Batch_Analysis_Service::STATE_SUBMIT,
			'posts_per_page' => - 1,
			'post_type'      => 'any',
		) );

		// `post` w/ annotations included.
		$this->assertTrue( in_array( $posts[0], $results ) );
		// `post` w/o annotations included.
		$this->assertTrue( in_array( $posts[1], $results ) );
		// `page` not included.
		$this->assertFalse( in_array( $posts[2], $results ) );
		// `post` w/o annotations modified 2017-01-01 included.
		$this->assertTrue( in_array( $posts[3], $results ) );
		// `post` w/o annotations modified 2018-01-01 included.
		$this->assertTrue( in_array( $posts[4], $results ) );

	}

	public function test_submit_posts_include_annotated_and_post_type_page() {

		$posts = $this->create_test_posts();

		### INCLUDE ANNOTATED.

		$this->batch_analysis_service->submit( array(
			'include_annotated' => true,
			'post_type'         => array(
				'post',
				'page',
			),
		) );

		$results = get_posts( array(
			'fields'         => 'ids',
			'meta_key'       => Wordlift_Batch_Analysis_Service::STATE_META_KEY,
			'meta_value'     => Wordlift_Batch_Analysis_Service::STATE_SUBMIT,
			'posts_per_page' => - 1,
			'post_type'      => 'any',
		) );

		// `post` w/ annotations included.
		$this->assertTrue( in_array( $posts[0], $results ) );
		// `post` w/o annotations included.
		$this->assertTrue( in_array( $posts[1], $results ) );
		// `page` not included.
		$this->assertTrue( in_array( $posts[2], $results ) );
		// `post` w/o annotations modified 2017-01-01 included.
		$this->assertTrue( in_array( $posts[3], $results ) );
		// `post` w/o annotations modified 2018-01-01 included.
		$this->assertTrue( in_array( $posts[4], $results ) );

	}

	public function test_submit_posts_include_a_post() {

		$posts = $this->create_test_posts();

		### INCLUDE A POST.

		$this->batch_analysis_service->submit_posts( array( 'ids' => $posts[0] ) );

		$results = get_posts( array(
			'fields'         => 'ids',
			'meta_key'       => Wordlift_Batch_Analysis_Service::STATE_META_KEY,
			'meta_value'     => Wordlift_Batch_Analysis_Service::STATE_SUBMIT,
			'posts_per_page' => - 1,
			'post_type'      => 'any',
		) );

		// `post` w/ annotations included.
		$this->assertTrue( in_array( $posts[0], $results ) );
		// `post` w/o annotations included.
		$this->assertFalse( in_array( $posts[1], $results ) );
		// `page` not included.
		$this->assertFalse( in_array( $posts[2], $results ) );
		// `post` w/o annotations modified 2017-01-01 included.
		$this->assertFalse( in_array( $posts[3], $results ) );
		// `post` w/o annotations modified 2018-01-01 included.
		$this->assertFalse( in_array( $posts[4], $results ) );

	}

	public function test_submit_posts_include_a_page() {

		$posts = $this->create_test_posts();

		### INCLUDE A POST.

		$this->batch_analysis_service->submit_posts( array( 'ids' => $posts[2] ) );

		$results = get_posts( array(
			'fields'         => 'ids',
			'meta_key'       => Wordlift_Batch_Analysis_Service::STATE_META_KEY,
			'meta_value'     => Wordlift_Batch_Analysis_Service::STATE_SUBMIT,
			'posts_per_page' => - 1,
			'post_type'      => 'any',
		) );

		// `post` w/ annotations included.
		$this->assertFalse( in_array( $posts[0], $results ) );
		// `post` w/o annotations included.
		$this->assertFalse( in_array( $posts[1], $results ) );
		// `page` not included.
		$this->assertTrue( in_array( $posts[2], $results ) );
		// `post` w/o annotations modified 2017-01-01 included.
		$this->assertFalse( in_array( $posts[3], $results ) );
		// `post` w/o annotations modified 2018-01-01 included.
		$this->assertFalse( in_array( $posts[4], $results ) );

	}

	public function test_submit_posts_exclude_a_post() {

		$posts = $this->create_test_posts();

		### EXCLUDE A POST.

		$this->batch_analysis_service->submit( array(
			'include_annotated' => true,
			'exclude'           => $posts[1],
		) );

		$results = get_posts( array(
			'fields'         => 'ids',
			'meta_key'       => Wordlift_Batch_Analysis_Service::STATE_META_KEY,
			'meta_value'     => Wordlift_Batch_Analysis_Service::STATE_SUBMIT,
			'posts_per_page' => - 1,
			'post_type'      => 'any',
		) );

		// `post` w/ annotations included.
		$this->assertTrue( in_array( $posts[0], $results ) );
		// `post` w/o annotations not included.
		$this->assertFalse( in_array( $posts[1], $results ) );
		// `page` not included.
		$this->assertFalse( in_array( $posts[2], $results ) );
		// `post` w/o annotations modified 2017-01-01 included.
		$this->assertTrue( in_array( $posts[3], $results ) );
		// `post` w/o annotations modified 2018-01-01 included.
		$this->assertTrue( in_array( $posts[4], $results ) );

	}


	/**
	 * @todo
	 */
	public function test_submit_from_date() {

		$posts = $this->create_test_posts();

		###

		$this->batch_analysis_service->submit( array(
			'from' => '2017-01-01T00:00:01+00:00',
		) );

		$results = get_posts( array(
			'fields'         => 'ids',
			'meta_key'       => Wordlift_Batch_Analysis_Service::STATE_META_KEY,
			'meta_value'     => Wordlift_Batch_Analysis_Service::STATE_SUBMIT,
			'posts_per_page' => - 1,
			'post_type'      => 'any',
		) );

		// `post` w/ annotations included.
		$this->assertFalse( in_array( $posts[0], $results ) );
		// `post` w/o annotations not included.
		$this->assertTrue( in_array( $posts[1], $results ) );
		// `page` not included.
		$this->assertFalse( in_array( $posts[2], $results ) );
		// `post` w/o annotations modified 2017-01-01 included.
		$this->assertFalse( in_array( $posts[3], $results ) );
		// `post` w/o annotations modified 2018-01-01 included.
		$this->assertTrue( in_array( $posts[4], $results ) );

	}

	/**
	 * @todo
	 */
	public function test_submit_to_date() {

		$posts = $this->create_test_posts();

		###

		$this->batch_analysis_service->submit( array(
			'to' => '2017-12-31T23:59:59+00:00',
		) );

		$results = get_posts( array(
			'fields'         => 'ids',
			'meta_key'       => Wordlift_Batch_Analysis_Service::STATE_META_KEY,
			'meta_value'     => Wordlift_Batch_Analysis_Service::STATE_SUBMIT,
			'posts_per_page' => - 1,
			'post_type'      => 'any',
		) );

		// `post` w/ annotations included.
		$this->assertFalse( in_array( $posts[0], $results ) );
		// `post` w/o annotations not included.
		$this->assertTrue( in_array( $posts[1], $results ) );
		// `page` not included.
		$this->assertFalse( in_array( $posts[2], $results ) );
		// `post` w/o annotations modified 2017-01-01 included.
		$this->assertTrue( in_array( $posts[3], $results ) );
		// `post` w/o annotations modified 2018-01-01 included.
		$this->assertFalse( in_array( $posts[4], $results ) );

	}

	/**
	 */
	public function test_submit_from_to_date() {

		$posts = $this->create_test_posts();

		###

		$this->batch_analysis_service->submit( array(
			'from' => '2017-01-01T00:00:01+00:00',
			'to'   => '2017-12-31T23:59:59+00:00',
		) );

		$results = get_posts( array(
			'fields'         => 'ids',
			'meta_key'       => Wordlift_Batch_Analysis_Service::STATE_META_KEY,
			'meta_value'     => Wordlift_Batch_Analysis_Service::STATE_SUBMIT,
			'posts_per_page' => - 1,
			'post_type'      => 'any',
		) );

		// `post` w/ annotations included.
		$this->assertFalse( in_array( $posts[0], $results ) );
		// `post` w/o annotations not included.
		$this->assertTrue( in_array( $posts[1], $results ) );
		// `page` not included.
		$this->assertFalse( in_array( $posts[2], $results ) );
		// `post` w/o annotations modified 2017-01-01 included.
		$this->assertFalse( in_array( $posts[3], $results ) );
		// `post` w/o annotations modified 2018-01-01 included.
		$this->assertFalse( in_array( $posts[4], $results ) );

	}

	/**
	 * @expectedException WPDieException
	 */
	public function test_submit_invalid_from_date() {

		$this->create_test_posts();

		###

		$this->batch_analysis_service->submit( array(
			'from' => 'random-string',
		) );

	}

	/**
	 * @expectedException WPDieException
	 */
	public function test_submit_invalid_to_date() {

		$this->create_test_posts();

		$this->batch_analysis_service->submit( array(
			'to' => 'random-string',
		) );

	}

	/**
	 */
	public function test_submit_to_before_from_date() {

		$this->create_test_posts();

		###

		$results = $this->batch_analysis_service->submit( array(
			'from' => '2017-12-31T23:59:59+00:00',
			'to'   => '2017-01-01T00:00:01+00:00',
		) );

		$this->assertEquals( 0, $results );

	}

	public function test_cancel() {

		$posts = $this->create_test_posts();

		###

		$this->batch_analysis_service->submit( array(
			'include_annotated' => true,
			'post_type'         => array(
				'post',
				'page',
			),
		) );

		$results_1 = get_posts( array(
			'fields'         => 'ids',
			'meta_key'       => Wordlift_Batch_Analysis_Service::STATE_META_KEY,
			'meta_value'     => Wordlift_Batch_Analysis_Service::STATE_SUBMIT,
			'posts_per_page' => - 1,
			'post_type'      => 'any',
		) );

		// `post` w/ annotations included.
		$this->assertTrue( in_array( $posts[0], $results_1 ) );
		// `post` w/o annotations not included.
		$this->assertTrue( in_array( $posts[1], $results_1 ) );
		// `page` not included.
		$this->assertTrue( in_array( $posts[2], $results_1 ) );
		// `post` w/o annotations modified 2017-01-01 included.
		$this->assertTrue( in_array( $posts[3], $results_1 ) );
		// `post` w/o annotations modified 2018-01-01 included.
		$this->assertTrue( in_array( $posts[4], $results_1 ) );

		### CANCEL THE BATCH ANALYSIS FOR 3 POSTS.
		$this->batch_analysis_service->cancel( array(
			$posts[0],
			$posts[2],
			$posts[4],
		) );

		$results_2 = get_posts( array(
			'fields'         => 'ids',
			'meta_key'       => Wordlift_Batch_Analysis_Service::STATE_META_KEY,
			'meta_value'     => Wordlift_Batch_Analysis_Service::STATE_SUBMIT,
			'posts_per_page' => - 1,
			'post_type'      => 'any',
		) );

		// `post` w/ annotations included.
		$this->assertFalse( in_array( $posts[0], $results_2 ) );
		// `post` w/o annotations not included.
		$this->assertTrue( in_array( $posts[1], $results_2 ) );
		// `page` not included.
		$this->assertFalse( in_array( $posts[2], $results_2 ) );
		// `post` w/o annotations modified 2017-01-01 included.
		$this->assertTrue( in_array( $posts[3], $results_2 ) );
		// `post` w/o annotations modified 2018-01-01 included.
		$this->assertFalse( in_array( $posts[4], $results_2 ) );

	}

	public function test_cancel_post_state_request() {

		$posts = $this->create_test_posts();

		###

		$this->batch_analysis_service->submit( array(
			'include_annotated' => true,
			'post_type'         => array(
				'post',
				'page',
			),
		) );

		$results_1 = get_posts( array(
			'fields'         => 'ids',
			'meta_key'       => Wordlift_Batch_Analysis_Service::STATE_META_KEY,
			'meta_value'     => Wordlift_Batch_Analysis_Service::STATE_SUBMIT,
			'posts_per_page' => - 1,
			'post_type'      => 'any',
		) );

		// `post` w/ annotations included.
		$this->assertTrue( in_array( $posts[0], $results_1 ) );
		// `post` w/o annotations not included.
		$this->assertTrue( in_array( $posts[1], $results_1 ) );
		// `page` not included.
		$this->assertTrue( in_array( $posts[2], $results_1 ) );
		// `post` w/o annotations modified 2017-01-01 included.
		$this->assertTrue( in_array( $posts[3], $results_1 ) );
		// `post` w/o annotations modified 2018-01-01 included.
		$this->assertTrue( in_array( $posts[4], $results_1 ) );

		// Simulate the request state for post[2].
		$update_post_meta_result = update_post_meta( $posts[2], Wordlift_Batch_Analysis_Service::STATE_META_KEY, Wordlift_Batch_Analysis_Service::STATE_REQUEST, Wordlift_Batch_Analysis_Service::STATE_SUBMIT );
		$this->assertTrue( $update_post_meta_result );

		### CANCEL THE BATCH ANALYSIS FOR 3 POSTS.
		$this->batch_analysis_service->cancel( array(
			$posts[0],
			$posts[2],
			$posts[4],
		) );

		$results_2 = get_posts( array(
			'fields'         => 'ids',
			'meta_key'       => Wordlift_Batch_Analysis_Service::STATE_META_KEY,
			'meta_value'     => Wordlift_Batch_Analysis_Service::STATE_SUBMIT,
			'posts_per_page' => - 1,
			'post_type'      => 'any',
		) );

		// `post` w/ annotations included.
		$this->assertFalse( in_array( $posts[0], $results_2 ) );
		// `post` w/o annotations not included.
		$this->assertTrue( in_array( $posts[1], $results_2 ) );
		// `page` not included.
		$this->assertFalse( in_array( $posts[2], $results_2 ) );
		// `post` w/o annotations modified 2017-01-01 included.
		$this->assertTrue( in_array( $posts[3], $results_2 ) );
		// `post` w/o annotations modified 2018-01-01 included.
		$this->assertFalse( in_array( $posts[4], $results_2 ) );

	}

	/**
	 * Test the `request` function using the default `submit` options.
	 *
	 * @since 3.17.0
	 */
	public function test_request_default_options() {

		$this->_test_request( array() );

	}

	/**
	 * Test the `request` function using custom `submit` options.
	 *
	 * @since 3.17.0
	 */
	public function test_request_link_yes_minimum_occurrences_two() {

		$this->_test_request( array(
			'link'            => 'yes',
			'min_occurrences' => 2,
		) );

	}

	public function test_complete() {

		$posts = $this->create_test_posts();
		$this->batch_analysis_service->submit_posts( array( 'ids' => $posts ) );

		// Send the request, preventing any actual http request from being sent.
		add_filter( 'pre_http_request', '__return_true' );
		$this->batch_analysis_service->request();
		remove_filter( 'pre_http_request', '__return_true' );

		add_filter( 'pre_http_request', array(
			$this,
			'__test_complete__pre_http_request',
		), 10, 3 );

		$this->batch_analysis_service->complete();

		remove_filter( 'pre_http_request', array(
			$this,
			'__test_complete__pre_http_request',
		) );

		// Check that the default taxonomy term is assigned.
		foreach ( $posts as $post_id ) {
			$terms = wp_get_post_terms( $post_id, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );
			$this->assertCount( 1, $terms );
			$this->assertEquals( 'article', $terms[0]->slug );
		}

		// Check that the posts have been assigned with the success state.
		$results = get_posts( array(
			'meta_key'       => Wordlift_Batch_Analysis_Service::STATE_META_KEY,
			'meta_value'     => Wordlift_Batch_Analysis_Service::STATE_SUCCESS,
			'posts_per_page' => - 1,
			'post_type'      => 'any',
		) );

		// Check that we have 5 results.
		$this->assertCount( 5, $results );

		// Check that the post content has been updated.
		foreach ( $results as $result ) {
			$this->assertEquals( 'Not chaos or over there, love the mineral.', $result->post_content );
		}

	}

	/**
	 * Internal function to test the `request` function.
	 *
	 * @since 3.17.0
	 *
	 * @param array $args The `submit` request options.
	 */
	private function _test_request( $args ) {

		$posts = $this->create_test_posts();

		### SUBMIT ONLY OUR TEST POSTS.

		$params = $args + array( 'ids' => $posts );
		$this->batch_analysis_service->submit_posts( $params );

		add_filter( 'pre_http_request', array(
			$this,
			'__test_request__pre_http_request',
		), 10, 3 );

		$this->batch_analysis_service->request();

		remove_filter( 'pre_http_request', array(
			$this,
			'__test_request__pre_http_request',
		) );


		// Check that the URL is correct.
		$expected_url = $this->configuration_service->get_batch_analysis_url();
		foreach ( $this->http_requests as $http_request ) {
			$this->assertEquals( $expected_url, $http_request['url'] );

			$request = $http_request['r'];
			$this->assertEquals( 'application/json', $request['headers']['Accept'] );
			$this->assertEquals( 'application/json; charset=UTF-8', $request['headers']['Content-type'] );

			$body = json_decode( $request['body'] );
			$this->assertEquals( isset( $args['link'] ) ? $args['link'] : 'default', $body->link );
			$this->assertEquals( isset( $args['min_occurrences'] ) ? $args['min_occurrences'] : 1, $body->minOccurrences );
			$this->assertTrue( in_array( $body->id, $posts ) );

		}

		$results = get_posts( array(
			'fields'         => 'ids',
			'meta_key'       => Wordlift_Batch_Analysis_Service::STATE_META_KEY,
			'meta_value'     => Wordlift_Batch_Analysis_Service::STATE_REQUEST,
			'posts_per_page' => - 1,
			'post_type'      => 'any',
		) );

		// `post` w/ annotations included.
		$this->assertTrue( in_array( $posts[0], $results ) );
		// `post` w/o annotations included.
		$this->assertTrue( in_array( $posts[1], $results ) );
		// `page` included.
		$this->assertTrue( in_array( $posts[2], $results ) );
		// `post` w/o annotations modified 2017-01-01 included.
		$this->assertTrue( in_array( $posts[3], $results ) );
		// `post` w/o annotations modified 2018-01-01 included.
		$this->assertTrue( in_array( $posts[4], $results ) );
	}

	private $http_requests = array();

	/**
	 * Intercept http requests called from the `request` function.
	 *
	 * @since 3.17.0
	 *
	 * @param bool   $preempt The previous `$preempt` value.
	 * @param array  $r       A request array.
	 * @param string $url     A request URL.
	 *
	 * @return bool The `$preempt` value.
	 */
	public function __test_request__pre_http_request( $preempt, $r, $url ) {

		$this->http_requests[] = array( 'r' => $r, 'url' => $url );

		return true;
	}

	public function __test_complete__pre_http_request( $preempt, $r, $url ) {

		return array( 'body' => '{ "content": "Not chaos or over there, love the mineral." }' );
	}

	/**
	 * Create many test posts, including a post with annotations and a post
	 * without, whose ids are returned by this function.
	 *
	 * @since 3.17.0
	 *
	 * @return array An array of {@link WP_Post}s' ids .
	 */
	private function create_test_posts() {

		$this->factory->post->create_many( 10 );

		$post_1_id = $this->factory->post->create( array(
			'post_type'     => 'post',
			'post_content'  => '<span id="urn:ex:test" class="annotation" itemid="http://example.org/ex">',
			'post_date'     => '2017-06-01 00:00:00',
			'post_date_gmt' => '2017-06-01 00:00:00',

		) );

		$post_2_id = $this->factory->post->create( array(
			'post_type'     => 'post',
			'post_content'  => '',
			'post_date'     => '2017-06-01 00:00:00',
			'post_date_gmt' => '2017-06-01 00:00:00',
		) );

		$post_3_id = $this->factory->post->create( array(
			'post_type'     => 'page',
			'post_content'  => '',
			'post_date'     => '2017-06-01 00:00:00',
			'post_date_gmt' => '2017-06-01 00:00:00',
		) );

		$post_4_id = $this->factory->post->create( array(
			'post_type'     => 'post',
			'post_content'  => '',
			'post_date'     => '2017-01-01 00:00:00',
			'post_date_gmt' => '2017-01-01 00:00:00',
		) );

		$post_5_id = $this->factory->post->create( array(
			'post_type'     => 'post',
			'post_content'  => '',
			'post_date'     => '2018-01-01 00:00:00',
			'post_date_gmt' => '2018-01-01 00:00:00',
		) );

		return array(
			$post_1_id,
			$post_2_id,
			$post_3_id,
			$post_4_id,
			$post_5_id,
		);
	}

//	public function test_waiting_for_response() {
//		$post1 = $this->factory->post->create( array(
//			'post_status'  => 'publish',
//		) );
//
//		$post2 = $this->factory->post->create( array(
//			'post_status'  => 'publish',
//		) );
//
//		$this->batch_service->set_state( $post1, Wordlift_Batch_Analysis_Service::STATE_REQUEST );
//		$this->batch_service->set_state( $post2, Wordlift_Batch_Analysis_Service::STATE_REQUEST );
//
//		$response = $this->batch_service->waiting_for_response();
//
//		$this->assertCount( 2, $response );
//	}
//
//	public function test_fix_interpolation_errors() {
//		$post1 = $this->factory->post->create( array(
//			'post_status' => 'publish',
//		) );
//
//		$test_string = 'test < span id = "urn:enhancement-99be0126-4d2c-caa3-7a59-7b5a9061426c" class="textannotation" itemid = "http://dbpedia.org/resource/Command_(computing)" > command</span > <span id = "urn:enhancement-99be0126-4d2c-caa3-7a59-7b5a9061426c" class="textannotation" itemid = "http://dbpedia.org/resource/Command_(computing)" > command</span > t <span id = "urn:enhancement-99be0126-4d2c-caa3-7a59-7b5a9061426c" class="textannotation" itemid = "http://dbpedia.org/resource/Command_(computing)" > command</span > ';
//
//		$fixed_string = 'testcommand commandt   command';
//
//		$response = $this->batch_service->fix_interpolation_errors( $test_string, $post1 );
//
//		$this->assertEquals( $fixed_string, $response );
//	}
//
//	public function test_set_get_state() {
//		$post1 = $this->factory->post->create( array(
//			'post_status' => 'publish',
//		) );
//
//		$post2 = $this->factory->post->create( array(
//			'post_status' => 'publish',
//		) );
//
//		$post3 = $this->factory->post->create( array(
//			'post_status' => 'publish',
//		) );
//
//
//		$this->batch_service->set_state( $post1, Wordlift_Batch_Analysis_Service::STATE_SUBMIT );
//		$this->batch_service->set_state( $post2, Wordlift_Batch_Analysis_Service::STATE_REQUEST );
//		$this->batch_service->set_state( $post3, Wordlift_Batch_Analysis_Service::STATE_ERROR );
//
//		// Get states.
//		$state1 = $this->batch_service->get_state( $post1 );
//		$state2 = $this->batch_service->get_state( $post2 );
//		$state3 = $this->batch_service->get_state( $post3 );
//
//		// Test that there was a post with warning.
//		$this->assertEquals( 0, $state1 );
//		$this->assertEquals( 1, $state2 );
//		$this->assertEquals( 2, $state3 );
//	}
//
//	public function test_cancel_post() {
//		$post1 = $this->factory->post->create( array(
//			'post_status' => 'publish',
//		) );
//
//		$params = array(
//			'link' => 'yes',
//		);
//
//		// Set the params.
//		$this->batch_service->set_params( $params );
//		// Submit posts for request.
//		$response1 = $this->batch_service->submit();
//
//		$this->batch_service->request();
//
//		$this->batch_service->cancel( array( $post1 ) );
//
//		$response2 = $this->batch_service->waiting_for_analysis();
//
//		// Test that there was a post with warning.
//		$this->assertEquals( 1, $response1 );
//
//		// Test that warnings have been removed.
//		$this->assertCount( 0, $response2 );
//	}
//
//	public function test_warnings() {
//		$post1 = $this->factory->post->create( array(
//			'post_status' => 'publish',
//		) );
//
//		// Set warning.
//		update_post_meta( $post1, Wordlift_Batch_Analysis_Service::WARNING_META_KEY, 'yes' );
//
//		// Get warnings.
//		$warnings = $this->batch_service->get_warnings();
//
//		// Clean post warning.
//		$this->batch_service->clear_warning( $post1 );
//
//		// Try to get warnings after they have been removed.
//		$warnings2 = $this->batch_service->get_warnings();
//
//		// Test that there was a post with warning.
//		$this->assertCount( 1, $warnings );
//
//		// Test that warnings have been removed.
//		$this->assertCount( 0, $warnings2 );
//	}
//
//	public function test_set_params() {
//		$params = array(
//			'link'              => 'no',
//			'include '           => 123,
//			'exclude'           => 231,
//			'from'              => '2017 - 12 - 12 09:41:49',
//			'to'                => '2017 - 12 - 12 09:41:50',
//			'min_occurrences'    => 5,
//			'post_type'         => 'page',
//		);
//
//		// Set the params.
//		$this->batch_service->set_params( $params );
//
//		// Get the service params.
//		$service_params = $this->batch_service->get_params();
//
//		// Test link.
//		$this->assertEquals( $params['link'], $service_params['link'] );
//
//		// Test include posts.
//		$this->assertEquals( (array) $params['include '], $service_params['include '] );
//
//		// Test exclude posts.
//		$this->assertEquals( (array) $params['exclude'], $service_params['exclude'] );
//
//		// Test start date.
//		$this->assertEquals( $params['from'], $service_params['from'] );
//
//		// Test end date.
//		$this->assertEquals( $params['to'], $service_params['to'] );
//
//		// Test min occurrences.
//		$this->assertEquals( $params['min_occurrences'], $service_params['minOccurrences'] );
//
//		// Test post type
//		$this->assertEquals( $params['post_type'], $service_params['post_type'] );
//	}
//
//	public function test_link_options() {
//		$post1 = $this->factory->post->create( array(
//			'post_status' => 'publish',
//		) );
//
//		$post2 = $this->factory->post->create( array(
//			'post_status' => 'publish',
//		) );
//
//		// Get custom link options
//		$link_options = unserialize( $this->batch_service->get_link_options( 'no', 5 ) );
//
//		// Update post 1 with the custom options.
//		update_post_meta( $post1, Wordlift_Batch_Analysis_Service::LINK_META_KEY, $link_options );
//
//		// Get link options for both custom and default cases.
//		$post1_link_options = $this->batch_service->get_post_link_options( $post1 );
//		$post2_link_options = $this->batch_service->get_post_link_options( $post2 );
//
//		// Test link options.
//		$this->assertEquals( $link_options['links'], 'no' );
//		$this->assertEquals( $link_options['minOccurrences'], 5 );
//
//		// Test custom link options.
//		$this->assertEquals( $link_options['links'], $post1_link_options['links'] );
//		$this->assertEquals( $link_options['minOccurrences'], $post1_link_options['minOccurrences'] );
//
//		// Test default link options.
//		$this->assertEquals( $post2_link_options['links'], 'default' );
//		$this->assertEquals( $post2_link_options['minOccurrences'], '1' );
//	}
//
//	public function test_maybe_set_default_term() {
//		$post1 = $this->factory->post->create( array(
//			'post_status' => 'publish',
//		) );
//
//		$post2 = $this->factory->post->create( array(
//			'post_status' => 'publish',
//		) );
//
//		// Set custom term to post2.
//		wp_set_object_terms( $post2, 'topic', Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );
//
//		// Remove default `article` term from post1.
//		wp_set_object_terms( $post1, '', Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );
//
//		// Set default 'article' term.
//		$response1 = $this->batch_service->maybe_set_default_term( $post1 );
//
//		// try to set 'article' term to post that already have term from that taxonomy.
//		$response2 = $this->batch_service->maybe_set_default_term( $post2 );
//
//		$terms = wp_get_post_terms( $post1, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME, array( 'fields' => 'slugs') );
//
//		// Test that 'article' term has been assigned to post1.
//		$this->assertInternalType( 'array', $response1 );
//
//		// Test that exactly `article` term has been assigned.
//		$this->assertContains( 'article', $terms );
//
//		// Test that post2 was skipped, because already has entity term.
//		$this->assertNull( $response2 );
//	}
//
//	public function test_submit() {
//		$post1 = $this->factory->post->create( array(
//			'post_status' => 'publish',
//		) );
//
//		$post2 = $this->factory->post->create( array(
//			'post_status' => 'publish',
//		) );
//
//		$page = $this->factory->post->create( array(
//			'post_status' => 'publish',
//			'post_type'   => 'page',
//		) );
//
//		$post_params = array(
//			'link'      => 'yes',
//		);
//
//		$page_params = array(
//			'link'      => 'yes',
//			'post_type' => 'page',
//		);
//
//		// Set the params.
//		$this->batch_service->set_params( $post_params );
//
//		$response1 = $this->batch_service->submit();
//
//		// Set the params.
//		$this->batch_service->set_params( $page_params );
//
//		$response2 = $this->batch_service->submit();
//
//		// Test posts.
//		$this->assertEquals( 2, $response1 );
//
//		// Test pages.
//		$this->assertEquals( 1, $response2 );
//
//	}
//
//	public function test_batch_analysis_regex_1() {
//
//		$content = 'a < span id = "urn:enhancement-xyz" class="class" itemid = "http://example.org" > Lorem</span > ';
//
//		$matches = array();
//		$warning = 0 < preg_match_all( ' / \w<[ a - z ] + id = "urn:enhancement-[^"]+ " class="[^"]+" itemid = "[^"]+ ">/', $content, $matches )
//				   || 0 < preg_match_all( '/<[a-z]+ id="urn:enhancement - [^"]+" class="[^"]+ " itemid="[^"]+" > \s / ', $content, $matches );
//
//		$this->assertTrue( $warning );
//
//	}
//
//	public function test_batch_analysis_regex_2() {
//
//		$content = '<span id = "urn:enhancement-xyz" class="class" itemid = "http://example.org" > Lorem</span > ';
//
//		$matches = array();
//		$warning = 0 < preg_match_all( ' / \w<[ a - z ] + id = "urn:enhancement-[^"]+ " class="[^"]+" itemid = "[^"]+ ">/', $content, $matches )
//				   || 0 < preg_match_all( '/<[a-z]+ id="urn:enhancement - [^"]+" class="[^"]+ " itemid="[^"]+" > \s / ', $content, $matches );
//
//		$this->assertTrue( $warning );
//
//	}
//
//	public function test_batch_analysis_regex_3() {
//
//		$content = ' <span id = "urn:enhancement-xyz" class="class" itemid = "http://example.org" > Lorem</span >.';
//
//		$matches = array();
//		$warning = 0 < preg_match_all( ' / \w < [ a - z ] + id = "urn:enhancement-[^"]+ " class="[^"]+" itemid = "[^"]+ ">/', $content, $matches )
//				   || 0 < preg_match_all( '/<[a-z]+ id="urn:enhancement - [^"]+" class="[^"]+ " itemid="[^"]+" > \s / ', $content, $matches );
//
//		$this->assertFalse( $warning );
//
//	}

}
