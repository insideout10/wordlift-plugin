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
	 * The {@link Wordlift_Cache_Service} implementation.
	 *
	 * @since  3.17.0
	 * @access private
	 * @var \Wordlift_Cache_Service $cache_service The {@link Wordlift_Cache_Service} implementation.
	 */
	private $cache_service;

	/**
	 * @inheritdoc
	 */
	function setUp() {
		parent::setUp();

		$this->batch_analysis_service = $this->get_wordlift_test()->get_batch_analysis_service();
		$this->cache_service          = $this->get_wordlift_test()->get_file_cache_service();

	}

	public function test_submit_default_values() {

		$post_ids = $this->factory()->post->create_many( 2 );

		$count = $this->batch_analysis_service->submit( array() );

		$this->assertEquals( 2, $count );

		foreach ( $post_ids as $id ) {
			$options = get_post_meta( $id, Wordlift_Batch_Analysis_Service::BATCH_ANALYSIS_OPTIONS_META_KEY, true );

			$this->assertEquals( array(
				'links'           => 'default',
				'min_occurrences' => 1,
			), $options );
		}

	}

	public function test_submit_link_yes() {

		$post_ids = $this->factory()->post->create_many( 2 );

		$count = $this->batch_analysis_service->submit( array( 'links' => 'yes' ) );

		$this->assertEquals( 2, $count );

		foreach ( $post_ids as $id ) {
			$options = get_post_meta( $id, Wordlift_Batch_Analysis_Service::BATCH_ANALYSIS_OPTIONS_META_KEY, true );

			$this->assertEquals( array(
				'links'           => 'yes',
				'min_occurrences' => 1,
			), $options );
		}

	}

	public function test_submit_link_no() {

		$post_ids = $this->factory()->post->create_many( 2 );

		$count = $this->batch_analysis_service->submit( array( 'links' => 'no' ) );

		$this->assertEquals( 2, $count );

		foreach ( $post_ids as $id ) {
			$options = get_post_meta( $id, Wordlift_Batch_Analysis_Service::BATCH_ANALYSIS_OPTIONS_META_KEY, true );

			$this->assertEquals( array(
				'links'           => 'no',
				'min_occurrences' => 1,
			), $options );
		}

	}

	/**
	 * @expectedException WPDieException
	 */
	public function test_submit_link_unknown_value() {

		$this->factory()->post->create_many( 2 );

		$this->batch_analysis_service->submit( array( 'links' => 'unknown' ) );

	}

	public function test_submit_min_occurrences_one() {

		$post_ids = $this->factory()->post->create_many( 2 );

		$count = $this->batch_analysis_service->submit( array( 'min_occurrences' => 1 ) );

		$this->assertEquals( 2, $count );

		foreach ( $post_ids as $id ) {
			$options = get_post_meta( $id, Wordlift_Batch_Analysis_Service::BATCH_ANALYSIS_OPTIONS_META_KEY, true );

			$this->assertEquals( array(
				'links'           => 'default',
				'min_occurrences' => 1,
			), $options );
		}

	}

	public function test_submit_min_occurrences_greater_than_one() {

		$post_ids = $this->factory()->post->create_many( 2 );

		$count = $this->batch_analysis_service->submit( array( 'min_occurrences' => 2 ) );

		$this->assertEquals( 2, $count );

		foreach ( $post_ids as $id ) {
			$options = get_post_meta( $id, Wordlift_Batch_Analysis_Service::BATCH_ANALYSIS_OPTIONS_META_KEY, true );

			$this->assertEquals( array(
				'links'           => 'default',
				'min_occurrences' => 2,
			), $options );
		}

	}

	/**
	 * @expectedException WPDieException
	 */
	public function test_submit_min_occurrences_not_a_number() {

		$this->factory()->post->create_many( 2 );

		$this->batch_analysis_service->submit( array( 'min_occurrences' => 'a' ) );

	}

	/**
	 * @expectedException WPDieException
	 */
	public function test_submit_min_occurrences_less_than_one() {

		$this->factory()->post->create_many( 2 );

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
			'links'           => 'yes',
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

		// Prepopulate the cache w/ bogus content just to see that it's cleaned
		// afterwards.
		foreach ( $posts as $post_id ) {
			$this->cache_service->set_cache( $post_id, 'Be celestine.' );
			$this->assertTrue( $this->cache_service->has_cache( $post_id ) );
		}

		add_filter( 'pre_http_request', array(
			$this,
			'__test_complete__pre_http_request',
		), 10, 3 );

		$this->batch_analysis_service->complete();

		remove_filter( 'pre_http_request', array(
			$this,
			'__test_complete__pre_http_request',
		) );

		foreach ( $posts as $post_id ) {
			$this->assertFalse( $this->cache_service->has_cache( $post_id ) );
		}

		// Check that the default taxonomy term is assigned.
		foreach ( $posts as $post_id ) {
			$terms = wp_get_post_terms( $post_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
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

	public function test_complete_fix_interpolation_errors() {

		$posts = $this->create_test_posts();
		$this->batch_analysis_service->submit_posts( array( 'ids' => $posts[0] ) );

		// Send the request, preventing any actual http request from being sent.
		add_filter( 'pre_http_request', '__return_true' );
		$this->batch_analysis_service->request();
		remove_filter( 'pre_http_request', '__return_true' );

		add_filter( 'pre_http_request', array(
			$this,
			'__test_complete__pre_http_request__with_interpolation_errors',
		), 10, 3 );

		$this->batch_analysis_service->complete();

		remove_filter( 'pre_http_request', array(
			$this,
			'__test_complete__pre_http_request__with_interpolation_errors',
		) );

		// Check that the posts have been assigned with the success state.
		$results = get_posts( array(
			'meta_key'       => Wordlift_Batch_Analysis_Service::STATE_META_KEY,
			'meta_value'     => Wordlift_Batch_Analysis_Service::STATE_SUCCESS,
			'posts_per_page' => 1,
			'post_type'      => 'any',
		) );

		// Check that we have 5 results.
		$this->assertCount( 1, $results );

		// Check that the post content has been updated.
		$this->assertEquals( <<<EOF
<em>“Disasters affect everyone, but impact the poor and vulnerable”</em>- A touching note by Arbind Kumar Mishra, Honorable Member, National Planning Commission, Nepal, as he addressed the participants at the UNESCAP Training Workshop on Disaster Risks specific to South and South-West Asia in Kathmandu, Nepal on October 30.

Asia-Pacific, as the world’s most disaster-prone region, has shouldered the burden of more than two million lives lost with economic damage of approximately $1.3 trillion between 1970 and 2016. Add to that the woeful living conditions that plague the region, and the loss becomes unquantifiable. The region accounts for over half of the world’s absolute poor living under the international poverty line of $1.90 per day.

[caption id="attachment_84518" align="aligncenter" width="762"]<a href="https://gsw-staging.localhost/wp-content/uploads/2017/10/Disaster-Impacts-in-South-and-South-West-Asia-2000-2016.png"><img class=" td-modal-image wp-image-84518 size-full" src="https://gsw-staging.localhost/wp-content/uploads/2017/10/Disaster-Impacts-in-South-and-South-West-Asia-2000-2016.png" alt="Disaster-Impacts-in-South-and-South-West-Asia-2000-2016" width="762" height="341" /></a> Disaster-Impacts-in-South-and-South-West-Asia-2000-2016 Source: ESCAP, APDR 2017[/caption]

In a region where development is already occurring at a snail’s pace, disasters reverse the development gains. It would not be incorrect to say that the disasters are actually outpacing the development efforts. In developing economies, the annual losses due to natural disasters account for 2.5% of the GDP. Disasters widen the socio-economic disparity, make the poor poorer, and lead to more conflicts. The loss a disaster brings in, impacts not only the physical and financial health of an economy, but the psychological well-being of the people. The effects last much longer than accounted for.

It’s futile to fight nature; it’s likely to overpower. So, why not focus towards building our own abilities to combat the wrath. This is what the UNESCAP Training Workshop focuses on. By bringing together experts on disaster risk reduction from nine countries; <span id="urn:local-text-annotation-9ideofrw04u8g4t0guwyzj3gwywqxfyp" class="textannotation disambiguated wl-place" itemid="http://data.wordlift.it/be2/entity/india">India</span>, Nepal, Bhutan, Maldives, Iran, Afghanistan, Pakistan, Bangladesh and Sri Lanka, it provides an excellent platform for exchanging ideas and best practices that can help the countries learn from each other and consequently achieve better preparation, prevention and response with respect to disasters.

Interestingly, while detailing their disaster management efforts in their respective countries, almost every speaker at the workshop reiterated the fact that ‘technology’ is the driving factor for a better, safer world. Be it modeling through early warning systems or using decision support systems to understand which disaster is going to affect or affecting which area the most, the preparation can become better, efforts can be more directed and response can be faster.

Satellite imagery is already enabling the world to combat disaster risks and carry out more effective response, and the <span id="urn:local-text-annotation-nlhoxfn3ucksb5j44sbcqb5bus4bd0eu" class="textannotation disambiguated wl-thing" itemid="http://data.wordlift.it/be2/entity/developing_country">developing economies</span> must follow suit. Few efforts have been made, but increased use of <span id="urn:local-text-annotation-80nvidmf0qh7x3pzf1ia1mjkajo161vq" class="textannotation disambiguated wl-thing" itemid="http://data.wordlift.it/be2/entity/technology">technologies</span> including geospatial is required to be prepared in a better way. We need more systems like the Tsunami Early Warning System developed by INCOIS. It is also very necessary to make such applications affordable. Only when the technology use becomes so widespread that it becomes a household name, we can think of making it affordable.

As Michael Williamson, Officer-in-Charge, Subregional Office for South and South-West Asia, ESCAP puts it, <em>“Space applications can really aid in disaster risk management. With more and more satellites being put in the orbit, the <span id="urn:local-text-annotation-j2yty1jhkvw1wzgedsc5n5s9b80qa2v2" class="textannotation disambiguated wl-thing" itemid="http://data.wordlift.it/be2/entity/technology">technology</span> is going to be cheaper in the coming days. This will help the community be more prepared for disasters.”</em>

Governments of developing economies must seriously consider investing in technologies for disaster risk reduction. Geospatial information management is necessary. The returns would go beyond monetary concerns. The number of lives early warning systems and mapping using remote sensing technologies can save will make any investment seem small.

Collaboration between countries is another crucial factor achieving for disaster risk reduction. Disasters do not know political boundaries. Earthquakes in Nepal or Bhutan affect <span id="urn:local-text-annotation-zr44v0jxv3sxllcsv5s32edr42gmgbfa" class="textannotation disambiguated wl-place" itemid="http://data.wordlift.it/be2/entity/india">India</span> and Bangladesh. Rivers flow beyond boundaries, so floods impact lives beyond political boundaries. In such a scenario, cooperation is vital.

Asia-Pacific countries agreed on a regional roadmap for implementing the 2030 Agenda for Sustainable Development at the 4th Asia-Pacific Forum on Sustainable Development in 2017. The roadmap identifies priority areas of regional cooperation for the means of implementation and partnerships, as well as six thematic areas including disaster risk reduction and resilience that correspond to major challenges still faced particularly in South Asia.

The Asia-Pacific Disaster Report 2017 published by UNESCAP highlights that developmental response to disaster risks must consider sub-regional specificities of shared vulnerabilities and disaster risk. A better understanding of the sub-regional specificity would facilitate cooperation among countries and enhance the capacity of member States, particularly least developed countries and land-locked <span id="urn:local-text-annotation-73ftedh2bij7skp2nm6w4v4falmuaaxs" class="textannotation disambiguated wl-thing" itemid="http://data.wordlift.it/be2/entity/developing_country">developing countries</span> to implement risk-sensitive sustainable development strategies, monitor the progress, and report their results towards pursuing the SDGs.

The UNESCAP Training Workshop is an effort to promote such cooperation. As experts from the nine participating countries share their experiences, learning and future plans, everyone in the room is motivated to do more once they reach back home. An area where a wide disparity is observed is the use of <span id="urn:local-text-annotation-o0ly5yjqgz8g7t7kuk43iw16v2ec2feq" class="textannotation disambiguated wl-thing" itemid="http://data.wordlift.it/be2/entity/technology">technology</span> to manage disasters. While a few <span id="urn:local-text-annotation-agrjae6fk87d45n7djmqblmvvt45j3w5" class="textannotation disambiguated wl-thing" itemid="http://data.wordlift.it/be2/entity/developing_country">developing economies</span> have started actually walking the talk, others seem to be still grappling with the intricacies of ideating and implementation. The gap between the industry and academia seems to a daunting issue here. However, the future seems bright. With everyone on board agreeing with the fact that <span id="urn:local-text-annotation-k7riikpnba2cx4zv8ev343a431ap9fxg" class="textannotation disambiguated wl-thing" itemid="http://data.wordlift.it/be2/entity/technology">technology</span> is the answer to most of the worries, we can expect to see happier faces in this part of the world sooner!
EOF
			, $results[0]->post_content );

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
			$this->assertEquals( isset( $args['links'] ) ? $args['links'] : 'default', $body->links );
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
	 * @param array  $r A request array.
	 * @param string $url A request URL.
	 *
	 * @return bool The `$preempt` value.
	 */
	public function __test_request__pre_http_request( $preempt, $r, $url ) {

		$this->http_requests[] = array( 'r' => $r, 'url' => $url );

		return true;
	}

	public function __test_complete__pre_http_request( $preempt, $r, $url ) {

		return array(
			'body'     => '{ "content": "Not chaos or over there, love the mineral." }',
			'response' => array( 'code' => 200 ),
		);
	}

	public function __test_complete__pre_http_request__with_interpolation_errors( $preempt, $r, $url ) {

		return array(
			'body'     => '{ "content": "<em>\u201cDisasters affect everyone, but impact the poor and vulnerable\u201d<\/em>- A touching note by Arbind Kumar Mishra, Honorable Member, National Planning Commission, Nepal, as he addressed the participants at the UNESCAP Training Workshop on Disaster Risks specific to South and <span id=\"urn:enhancement-3a96f895-278a-d3ed-b273-fa83ec1e6f67\" class=\"textannotation\">South-<\/span>West <span id=\"urn:enhancement-757f2a27-d03e-7e74-0b23-c6871d776b24\" class=\"textannotation\">Asia<\/span> in <span id=\"urn:enhancement-0292dabf-cb91-9f04-e3b3-0bba8a7f7c6f\" class=\"textannotation\">Kathmandu, Nepal<\/span> on October 30.\n\n<span id=\"urn:enhancement-e8468332-dee8-f91d-4fad-96cdfa497c96\" class=\"textannotation\">Asia<\/span>-<span id=\"urn:enhancement-8c0b6ba7-8c3c-35af-2075-8ed70ffb4a17\" class=\"textannotation\">Pacific<\/span>, as the world\u2019s most disaster-pro<span id=\"urn:local-text-annotation-oxbgy6139gnjgk1n0oxnq9zg62py29pf\" class=\"textannotation disambiguated wl-thing\" itemid=\"http:\/\/data.wordlift.it\/be2\/entity\/developing_country\">ne region, has shoul<\/span>dered the burden of more than two mill<span id=\"urn:local-text-annotation-nt8ysrxqd6eqp5mdoggayekpflvuhfno\" class=\"textannotation disambiguated wl-thing\" itemid=\"http:\/\/data.wordlift.it\/be2\/entity\/technology\">ion lives lo<\/span>st with economic damage of approximately $1.3 trillion between 1970 and 2016. Add to that the woeful living conditions that plague the <span id=\"urn:enhancement-9fd51857-a6d4-7acc-8f3e-89d70eb53b22\" class=\"textannotation\">region<\/span>, and the loss becomes unquantifiable. The <span id=\"urn:enhancement-2e66b92d-39d0-ce17-4039-39e4f494f9e2\" class=\"textannotation\">region<\/span> accounts for over half of the world\u2019s absolute poor living under the international <span id=\"urn:enhancement-0206610c-8ac0-441f-ab66-58d8732616b2\" class=\"textannotation\">poverty line<\/span> of $1.90 per day.\n\n[caption id=\"attachment_84518\" align=\"aligncenter\" width=\"762\"]<a href=\"https:\/\/gsw-staging.localhost\/wp-content\/uploads\/2017\/10\/Disaster-Impacts-in-South-and-South-West-Asia-2000-2016.png\"><img class=\" td-modal-image wp-image-84518 size-full\" src=\"https:\/\/gsw-staging.localhost\/wp-content\/uploads\/2017\/10\/Disaster-Impacts-in-South-and-South-West-Asia-2000-2016.png\" alt=\"Disaster-Impacts-in-South-and-South-West-Asia-2000-2016\" width=\"762\" height=\"341\" \/><\/a> Disaster-Impacts-in-<span id=\"urn:enhancement-aa3bb9d7-812f-815a-0496-a2c05aa85c41\" class=\"textannotation\">South-<\/span>and-<span id=\"urn:enhancement-79a0a1db-4a91-9109-0d49-204dd4453eef\" class=\"textannotation\">South-<\/span>West-<span id=\"urn:enhancement-8bc9696c-0083-987f-6b56-97e1b522f138\" class=\"textannotation\">Asia<\/span>-2000-2016 Source: ESCAP, APDR 2017[\/caption]\n\nIn a <span id=\"urn:enhancement-654aa46d-4d0e-13d5-57c3-088bd181c3b8\" class=\"textannotation\">region<\/span> where <span id=\"urn:enhancement-98e1c08b-6221-bb5c-7a5b-68233fa1d3fc\" class=\"textannotation\">development<\/span> is already occurring at a snail\u2019s pace, disasters reverse the <span id=\"urn:enhancement-5b74f072-c055-f76a-7690-5a216cba0c02\" class=\"textannotation\">development<\/span> gains. It would not be incorrect to say that the disasters are actually outpacing the <span id=\"urn:enhancement-718421ce-38e3-192f-92b4-e511c8f8bea8\" class=\"textannotation\">development<\/span> efforts. In <span id=\"urn:enhancement-c94cd3a3-7ce5-4d08-332a-bfc2499ae110\" class=\"textannotation\">developing economies<\/span>, the annual losses due to natural disasters account for 2.5% of the <span id=\"urn:enhancement-bab98ca3-1d61-6b8b-2323-9048eab58f36\" class=\"textannotation\">GDP<\/span>. Disasters widen the <span id=\"urn:enhancement-881be34b-6994-1679-06fa-459b05db4387\" class=\"textannotation\">socio-economic<\/span> disparity, make the poor poorer, and lead to more conflicts. The loss a disaster brings in, impacts not only the physical and financial health of an economy, but the psychological well-being of the people. The effects last much longer than accounted for.\n\nIt\u2019s futile to fight nature; it\u2019s likely to overpower. So, why not focus towards building our own abilities to combat the wrath. This is what the UNESCAP Training Workshop focuses on. By bringing together experts on <span id=\"urn:enhancement-42437d32-f58d-71cd-6fd7-97d7b7f99058\" class=\"textannotation\">disaster risk reduction<\/span> from nine countries; <span id=\"urn:local-text-annotation-9ideofrw04u8g4t0guwyzj3gwywqxfyp\" class=\"textannotation disambiguated wl-place\" itemid=\"http:\/\/data.wordlift.it\/be2\/entity\/india\">India<\/span>, <span id=\"urn:enhancement-2f889d06-743b-4d08-62dc-f63fa58f0231\" class=\"textannotation\">Nepal<\/span>, <span id=\"urn:enhancement-58f11dd4-bcd1-a9ef-cc27-08f73d44b9bb\" class=\"textannotation\">Bhutan<\/span>, <span id=\"urn:enhancement-d5f0f9d2-0122-b6c1-6348-768c3589d5a8\" class=\"textannotation\">Maldives<\/span>, <span id=\"urn:enhancement-3598e9fa-4f01-6fa0-3668-02fde9a54249\" class=\"textannotation\">Iran<\/span>, Afghanistan, <span id=\"urn:enhancement-0f36a813-6b27-2e31-c8f9-7ddb4425e364\" class=\"textannotation\">Pakistan<\/span>, <span id=\"urn:enhancement-8c4fb528-7f43-5530-a0ed-79da0838910a\" class=\"textannotation\">Bangladesh<\/span> and Sri Lanka, it provides an excellent platform for exchanging ideas and <span id=\"urn:enhancement-0ba503e1-a294-ae6c-43c0-524fa8fb4dcf\" class=\"textannotation\">best practices<\/span> that can help the countries learn from each other and consequently achieve better preparation, prevention and response with respect to disasters.\n\nInterestingly, while detailing their disaster management efforts in their respective countries, almost every speaker at the <span id=\"urn:enhancement-4c5af781-0cd3-4423-2617-3a56ccbbac2a\" class=\"textannotation\">workshop<\/span> reiterated the fact that \u2018technology\u2019 is the <span id=\"urn:enhancement-783a94f7-3eb9-7e83-6ea7-71c5f9ae8ef2\" class=\"textannotation\">driving<\/span> factor for a better, safer world. Be it modeling through <span id=\"urn:enhancement-ba5d05bb-a072-ebac-3aae-af3594e1977c\" class=\"textannotation\">early warning<\/span> systems or using <span id=\"urn:enhancement-adb3a616-095a-600a-a3ac-3d7c70b4ba18\" class=\"textannotation\">decision support systems<\/span> to understand which disaster is going to affect or affecting which area the most, the preparation can become better, efforts can be more directed and response can be faster.\n\n<span id=\"urn:enhancement-90dce542-7622-a45a-19e6-2c6f04f7581f\" class=\"textannotation\">Satellite imagery<\/span> is already enabling the world to <span id=\"urn:enhancement-8e3ce8d0-12dd-e654-5eb2-ee4461d926e9\" class=\"textannotation\">combat<\/span> disaster risks and carry out more effective response, and the <span id=\"urn:local-text-annotation-nlhoxfn3ucksb5j44sbcqb5bus4bd0eu\" class=\"textannotation disambiguated wl-thing\" itemid=\"http:\/\/data.wordlift.it\/be2\/entity\/developing_country\">developing economies<\/span> must follow suit. Few efforts have been made, but increased <span id=\"urn:enhancement-3d68a62e-0a33-d200-7861-8015dc9e91f4\" class=\"textannotation\">use<\/span> of <span id=\"urn:local-text-annotation-80nvidmf0qh7x3pzf1ia1mjkajo161vq\" class=\"textannotation disambiguated wl-thing\" itemid=\"http:\/\/data.wordlift.it\/be2\/entity\/technology\">technologies<\/span> including geospatial is required to be prepared in a better way. We need more systems like the Tsunami <span id=\"urn:enhancement-924273ac-b0a8-c010-c67a-3fb147fcdca1\" class=\"textannotation\">Early Warning System<\/span> developed by INCOIS. It is also very necessary to make such applications <span id=\"urn:enhancement-3d929cfa-6c9e-0a85-c988-5723a81eb8bd\" class=\"textannotation\">affordable<\/span>. Only when the technology <span id=\"urn:enhancement-39810f56-3990-4642-3363-3df470af6b23\" class=\"textannotation\">use<\/span> becomes so widespread that it becomes a <span id=\"urn:enhancement-55078839-1258-c32f-739e-c0e8e0fe64db\" class=\"textannotation\">household<\/span> name, we can think of making it <span id=\"urn:enhancement-b0931109-af78-33fb-d0dc-35afa8fba96d\" class=\"textannotation\">affordable<\/span>.\n\nAs Michael Williamson, <span id=\"urn:enhancement-ad90794c-3ae3-ec02-cf22-eb02ff10a87b\" class=\"textannotation\">Officer<\/span>-in-Charge, Subregional Office for South and <span id=\"urn:enhancement-3d29bcfa-a6a7-864a-ba38-ad3a06bd5722\" class=\"textannotation\">South-<\/span>West <span id=\"urn:enhancement-b721b172-2f9e-bf2c-efa8-c5bda21a7c5e\" class=\"textannotation\">Asia<\/span>, ESCAP puts it, <em>\u201cSpace applications can really <span id=\"urn:enhancement-ef6a896f-b795-2691-e8d2-c0458fa99c42\" class=\"textannotation\">aid<\/span> in disaster risk management. With more and more <span id=\"urn:enhancement-32c81e72-b12d-5c3e-78b5-a4d83ecae70c\" class=\"textannotation\">satellites<\/span> being put in the <span id=\"urn:enhancement-9e320c68-a772-3647-f124-acebcbe8611a\" class=\"textannotation\">orbit<\/span>, the <span id=\"urn:local-text-annotation-j2yty1jhkvw1wzgedsc5n5s9b80qa2v2\" class=\"textannotation disambiguated wl-thing\" itemid=\"http:\/\/data.wordlift.it\/be2\/entity\/technology\">technology<\/span> is going to be cheaper in the coming days. This <span id=\"urn:enhancement-909cd70e-330b-1fd9-0cf0-84a32c1f7a70\" class=\"textannotation\">will<\/span> help the <span id=\"urn:enhancement-55a160d9-c0c4-e17d-117e-60317278f84b\" class=\"textannotation\">community<\/span> be more prepared for disasters.\u201d<\/em>\n\nGovernments of <span id=\"urn:enhancement-a893d82e-a845-6575-b12d-c13224bb1deb\" class=\"textannotation\">developing economies<\/span> must seriously consider investing in <span id=\"urn:enhancement-4d9467ae-fc4f-0eda-7831-1ca53c625420\" class=\"textannotation\">technologies<\/span> for <span id=\"urn:enhancement-deff6c71-ed06-1afb-db1e-9b59942d9911\" class=\"textannotation\">disaster risk reduction<\/span>. <span id=\"urn:enhancement-4a724838-0294-89bc-1809-1f397a88df4a\" class=\"textannotation\">Geospatial<\/span> information management is necessary. The returns would go beyond monetary concerns. The number of lives <span id=\"urn:enhancement-013915d5-0b08-69a6-fd3b-1f1f7352538a\" class=\"textannotation\">early warning<\/span> systems and <span id=\"urn:enhancement-fc3267c9-1c4a-23e3-c866-9e964247c4f2\" class=\"textannotation\">mapping<\/span> using <span id=\"urn:enhancement-d7ecf4e6-83cf-4259-1402-56730c9a1b65\" class=\"textannotation\">remote sensing<\/span> technologies can save <span id=\"urn:enhancement-501c0862-5f89-1e14-0e26-3170a5a1d9a1\" class=\"textannotation\">will<\/span> make any investment seem small.\n\n<span id=\"urn:enhancement-7b5d156c-6a83-fdcd-5094-e8f5f2aa9abe\" class=\"textannotation\">Collaboration<\/span> between countries is another crucial factor achieving for <span id=\"urn:enhancement-9b34c976-4671-0faf-96ce-1e09fc719e6c\" class=\"textannotation\">disaster risk reduction<\/span>. Disasters do not know political boundaries. <span id=\"urn:enhancement-a7ad825a-3034-caaa-5da8-56b19109d80d\" class=\"textannotation\">Earthquakes<\/span> in <span id=\"urn:enhancement-238253df-a7eb-6a03-d070-c879b15c3cc3\" class=\"textannotation\">Nepal<\/span> or <span id=\"urn:enhancement-f2857fba-ead9-4523-ce55-612c3ae15847\" class=\"textannotation\">Bhutan<\/span> affect <span id=\"urn:local-text-annotation-zr44v0jxv3sxllcsv5s32edr42gmgbfa\" class=\"textannotation disambiguated wl-place\" itemid=\"http:\/\/data.wordlift.it\/be2\/entity\/india\">India<\/span> and <span id=\"urn:enhancement-1a1fab4f-6386-4359-d2cf-312d2646b1c2\" class=\"textannotation\">Bangladesh<\/span>. Rivers flow beyond boundaries, so <span id=\"urn:enhancement-c42d0641-e461-bbe5-9145-9cbc7b1ac4e0\" class=\"textannotation\">floods<\/span> impact lives beyond political boundaries. In such a scenario, <span id=\"urn:enhancement-501cb71e-2d2e-5b8b-9c36-c00107bcc830\" class=\"textannotation\">cooperation<\/span> is vital.\n\n<span id=\"urn:enhancement-ab0b0652-bd1e-775c-440a-2fd10e935fc3\" class=\"textannotation\">Asia<\/span>-Pacific countries agreed on a <span id=\"urn:enhancement-ac53e68e-0686-3aae-9c42-62344179f009\" class=\"textannotation\">regional<\/span> roadmap for implementing the 2030 Agenda for <span id=\"urn:enhancement-4b025f99-a0f4-8590-06cc-0c51fefa352b\" class=\"textannotation\">Sustainable Development<\/span> at the 4th <span id=\"urn:enhancement-8e4ab0a8-0c83-8873-eef5-71f4707446ca\" class=\"textannotation\">Asia<\/span>-<span id=\"urn:enhancement-d4653c7e-fa90-6fcb-57af-d1ad8cf972a9\" class=\"textannotation\">Pacific Forum<\/span> on <span id=\"urn:enhancement-133440d9-ab76-22cb-7f80-4ec80fc7ae30\" class=\"textannotation\">Sustainable Development<\/span> in 2017. The roadmap identifies priority areas of <span id=\"urn:enhancement-e65bd5cf-1930-f091-5b86-550b248a4501\" class=\"textannotation\">regional<\/span> <span id=\"urn:enhancement-b1ac3857-c222-334e-4ac9-7ca40a5327e4\" class=\"textannotation\">cooperation<\/span> for the means of implementation and partnerships, as well as six thematic areas including <span id=\"urn:enhancement-8cc806a0-d1e4-1052-d7fc-0fe8ffd27654\" class=\"textannotation\">disaster risk reduction<\/span> and <span id=\"urn:enhancement-67fe6b33-cdf4-368a-5be7-ccb32dbe7e53\" class=\"textannotation\">resilience<\/span> that correspond to major challenges still faced particularly in South <span id=\"urn:enhancement-6151d426-7c6a-2c1e-5b96-f724e3e2b1e5\" class=\"textannotation\">Asia<\/span>.\n\nThe <span id=\"urn:enhancement-499fba66-4c11-23ba-f9ab-6f29827b07fb\" class=\"textannotation\">Asia<\/span>-Pacific <span id=\"urn:enhancement-0efd8740-3939-d201-fc9f-3e5b939375d3\" class=\"textannotation\">Disaster<\/span> <span id=\"urn:enhancement-462fe2aa-a49c-92c2-9629-dddfe0e55e0e\" class=\"textannotation\">Report<\/span> 2017 published by UNESCAP highlights that developmental response to disaster risks must consider sub-<span id=\"urn:enhancement-2fac6a8d-e07d-b610-be32-9aebb4732fff\" class=\"textannotation\">regional<\/span> specificities of shared vulnerabilities and disaster risk. A better understanding of the sub-regional specificity would facilitate cooperation among countries and enhance the capacity of member States, particularly least <span id=\"urn:enhancement-36290ce0-43da-18c0-a864-27626a2365df\" class=\"textannotation\">developed<\/span> countries and land-locked <span id=\"urn:local-text-annotation-73ftedh2bij7skp2nm6w4v4falmuaaxs\" class=\"textannotation disambiguated wl-thing\" itemid=\"http:\/\/data.wordlift.it\/be2\/entity\/developing_country\">developing countries<\/span> to implement risk-sensitive sustainable <span id=\"urn:enhancement-ce44c9b8-edbf-06ed-34fe-a9512fa62172\" class=\"textannotation\">development<\/span> strategies, monitor the progress, and report their results towards pursuing the SDGs.\n\nThe UNESCAP Training Workshop is an effort to promote such cooperation. As experts from the nine participating countries share their experiences, learning and future plans, everyone in the room is motivated to do more once they reach back home. An area where a wide disparity is observed is the use of <span id=\"urn:local-text-annotation-o0ly5yjqgz8g7t7kuk43iw16v2ec2feq\" class=\"textannotation disambiguated wl-thing\" itemid=\"http:\/\/data.wordlift.it\/be2\/entity\/technology\">technology<\/span> to manage disasters. While a few <span id=\"urn:local-text-annotation-agrjae6fk87d45n7djmqblmvvt45j3w5\" class=\"textannotation disambiguated wl-thing\" itemid=\"http:\/\/data.wordlift.it\/be2\/entity\/developing_country\">developing economies<\/span> have started actually walking the talk, others seem to be still grappling with the intricacies of ideating and implementation. The gap between the industry and academia seems to a daunting <span id=\"urn:enhancement-945376c0-c908-79c2-97ef-fae79bb1991f\" class=\"textannotation\">issue<\/span> here. However, the future seems bright. With everyone on board agreeing with the fact that <span id=\"urn:local-text-annotation-k7riikpnba2cx4zv8ev343a431ap9fxg\" class=\"textannotation disambiguated wl-thing\" itemid=\"http:\/\/data.wordlift.it\/be2\/entity\/technology\">technology<\/span> is the answer to most of the worries, we can expect to see happier faces in this part of the world sooner!" }',
			'response' => array( 'code' => 200 ),
		);
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

		$this->factory()->post->create_many( 10 );

		$post_1_id = $this->factory()->post->create( array(
			'post_type'     => 'post',
			'post_content'  => '<span id="urn:ex:test" class="annotation" itemid="http://example.org/ex">',
			'post_date'     => '2017-06-01 00:00:00',
			'post_date_gmt' => '2017-06-01 00:00:00',

		) );

		$post_2_id = $this->factory()->post->create( array(
			'post_type'     => 'post',
			'post_content'  => '',
			'post_date'     => '2017-06-01 00:00:00',
			'post_date_gmt' => '2017-06-01 00:00:00',
		) );

		$post_3_id = $this->factory()->post->create( array(
			'post_type'     => 'page',
			'post_content'  => '',
			'post_date'     => '2017-06-01 00:00:00',
			'post_date_gmt' => '2017-06-01 00:00:00',
		) );

		$post_4_id = $this->factory()->post->create( array(
			'post_type'     => 'post',
			'post_content'  => '',
			'post_date'     => '2017-01-01 00:00:00',
			'post_date_gmt' => '2017-01-01 00:00:00',
		) );

		$post_5_id = $this->factory()->post->create( array(
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

}
