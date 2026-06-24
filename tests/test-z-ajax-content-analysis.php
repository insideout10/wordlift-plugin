<?php
/**
 * Test the Content Analysis.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @package Wordlift/Tests
 * @since 3.24.2
 * @group ajax
 */

class Ajax_Content_Analysis_Test extends Wordlift_Ajax_Unit_Test_Case {

	private $capture_analysis_request = false;

	private $captured_analysis_request_body = null;

	function setUp() {
		parent::setUp();

		add_filter( 'pre_http_request', array( $this, '_mock_api' ), 10, 3 );
	}

	function tearDown() {
		remove_filter( 'pre_http_request', array( $this, '_mock_api' ) );

		parent::tearDown();
	}

	function _mock_api( $response, $request, $url ) {

		if ( $this->capture_analysis_request && 'POST' === $request['method'] && preg_match( '@/analysis/single$@', $url ) ) {
			$this->captured_analysis_request_body = $request['body'];

			return array(
				'response' => array( 'code' => 200 ),
				'body'     => '{ "entities": {}, "annotations": {}, "topics": {} }',
			);
		}

		if ( 'POST' === $request['method'] && preg_match( '@/analysis/single$@', $url )
		     && '5d169a6aa4c5e711d0353c0b5be4a0ef' === md5( $request['body'] ) ) {
			return array(
				'response' => array( 'code' => 200 ),
				'body'     => file_get_contents( __DIR__ . '/assets/analysis-response-1.json' ),
			);
		}

		if ( 'POST' === $request['method'] && preg_match( '@/datasets/key=key123/queries$@', $url )
		     && in_array( md5( $request['body'] ), array(
				'db44d756903e452e6488148869b6955d',
				'ede80ec288a8c3a2ab759b6a0ce1b7d4',
				'9b695ede396a81cb75e60af2fff251ed'
			) ) ) {
			return array(
				'response' => array( 'code' => 200 ),
				'body'     => file_get_contents( __DIR__ . '/assets/analysis-response-1.json' ),
			);
		}

		if ( 'POST' === $request['method'] && preg_match( '@/datasets/key=key123/index$@', $url ) ) {
			return array(
				'response' => array( 'code' => 200 ),
				'body'     => file_get_contents( __DIR__ . '/assets/analysis-response-1.json' ),
			);
		}

		return $response;
	}

	public function test_analysis_request_preserves_backticks_in_content() {
		$this->capture_analysis_request       = true;
		$this->captured_analysis_request_body = null;

		$content = '<pre><code>`Bearer ${process.env.KINSTA_API_KEY}`</code></pre><p>However, when you implement this, you should ensure you watch for key infrastructure security indicators.</p>';

		$_POST = array(
			'action'   => 'wl_analyze',
			'_wpnonce' => wp_create_nonce( 'wl_analyze' ),
			'data'     => wp_json_encode( array(
				'content'     => $content,
				'annotations' => array(),
				'contentType' => 'text/html',
				'version'     => '1.0.0',
			) ),
		);

		try {
			$this->_handleAjax( 'wl_analyze' );
		} catch ( WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		$this->assertNotNull( $this->captured_analysis_request_body );

		$forwarded_request = json_decode( $this->captured_analysis_request_body, true );

		$this->assertTrue( is_array( $forwarded_request ) );
		$this->assertArrayHasKey( 'content', $forwarded_request );

		$this->assertEquals( $content, $forwarded_request['content'] );
		$this->assertContains( '`Bearer ${process.env.KINSTA_API_KEY}`', $forwarded_request['content'] );
	}

	public function test() {
		// Create an entity, by also setting its entity URL and type.
		$post_id = $this->factory()->post->create( array(
			'post_type'    => 'entity',
			'post_title'   => 'Content Analysis Test 1',
			'post_status'  => 'publish',
			'post_content' => 'Post Content Analysis Test 1'
		) );
		update_post_meta( $post_id, 'entity_url', 'http://example.org/content_analysis_test_1' );
		wp_add_object_terms( $post_id, 'thing', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		$_POST = array(
			'action'   => 'wl_analyze',
			'_wpnonce' => wp_create_nonce( 'wl_analyze' ),
			'data'     => file_get_contents( dirname( __FILE__ ) . '/assets/content-analysis-request-1.json' ),
		);

		try {
			$this->_handleAjax( 'wl_analyze' );
		} catch ( WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		$response = json_decode( $this->_last_response, true );

		$this->assertTrue( is_array( $response ), 'We expect `response` to be an `array`, instead got: ' . var_export( $this->_last_response, true ) );
		$this->assertArrayHasKey( 'success', $response,
			'We expect the response to contain a `success` key, instead got: ' . var_export( $response, true ) );
		$this->assertTrue( $response['success'], 'Success must be `true`.' );

	}

}
