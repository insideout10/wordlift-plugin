<?php
/**
 * Tests: Ajax Navigator Test.
 *
 * @since   3.27.1.2
 * @package Wordlift
 */

use Wordlift\Cache\Ttl_Cache;

/**
 * Class Wordlift_Navigator_Test
 * Extend Wordlift_Ajax_Unit_Test_Case
 *
 * @group ajax
 *
 * @since   3.27.1.2
 * @package Wordlift
 */
class Wordlift_Navigator_Test extends Wordlift_Ajax_Unit_Test_Case {

	function setUp() {
		parent::setUp();
		remove_all_actions( 'doing_it_wrong_run' );
		add_filter( 'pre_http_request', array( $this, '_mock_api' ), 10, 3 );
	}

	function tearDown() {
		remove_filter( 'pre_http_request', array( $this, '_mock_api' ) );
		parent::tearDown();
	}

	function _mock_api( $response, $request, $url ) {

		if ( 'POST' === $request['method'] && preg_match( '@/datasets/key=key123/queries$@', $url ) ) {
			return array(
				'response' => array( 'code' => 200 ),
				'body'     => ''
			);
		}

		if ( 'POST' === $request['method'] && preg_match( '@/datasets/key=key123/index$@', $url ) ) {
			return array(
				'response' => array( 'code' => 200 ),
				'body'     => ''
			);
		}

		return $response;
	}

	public function test_navigator_without_post_id() {
		$_GET['_wpnonce'] = wp_create_nonce( 'wl_navigator' );
		$cache = new Ttl_Cache( 'navigator' );
		$cache->flush();

		try {
			$this->_handleAjax( 'wl_navigator' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );

		$this->assertInternalType( 'object', $response );
		$this->assertObjectHasAttribute( 'success', $response );
		$this->assertFalse( $response->success );
		$this->assertObjectHasAttribute( 'data', $response );
		$this->assertEquals( 'No post_id given', $response->data );
	}

	public function test_navigator_without_uniqid() {
		$_GET['_wpnonce'] = wp_create_nonce( 'wl_navigator' );
		$cache = new Ttl_Cache( 'navigator' );
		$cache->flush();

		$_GET['post_id'] = 1;

		try {
			$this->_handleAjax( 'wl_navigator' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );

		$this->assertInternalType( 'object', $response );
		$this->assertObjectHasAttribute( 'success', $response );
		$this->assertFalse( $response->success );
		$this->assertObjectHasAttribute( 'data', $response );
		$this->assertEquals( 'No uniqid given', $response->data );
	}

	public function test_data_for_post_with_no_entities() {
		$_GET['_wpnonce'] = wp_create_nonce( 'wl_navigator' );
		$cache = new Ttl_Cache( 'navigator' );
		$cache->flush();

		$post_1_id      = wl_create_post( '', 'post1', 'A post 1', 'publish', 'post' );
		$post_2_id      = wl_create_post( '', 'post2', 'A post 2', 'publish', 'post' );
		$thumbnail_2_id = $this->createPostThumbnail( 'http://example.org/post_1.png', 'Post 2 Thumbnail', 'image/png', 'dummy/image_1.png', $post_2_id );

		$_GET['post_id'] = $post_1_id;
		$_GET['uniqid']  = 'uniqid-123';

		try {
			$this->_handleAjax( 'wl_navigator' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );

		$this->assertInternalType( 'array', $response );
		$this->assertNotEmpty( $response );
		$this->assertEquals( 'A post 2', $response[0]->post->title );

	}

	private function createPostThumbnail( $guid, $label, $content_type, $file, $post_id ) {

		$attachment = array(
			'guid'           => $guid,
			'post_title'     => $label, // Set the title to the post title.
			'post_content'   => '',
			'post_status'    => 'inherit',
			'post_mime_type' => $content_type,
		);

		// Create the attachment in WordPress and generate the related metadata.
		$attachment_id = wp_insert_attachment( $attachment, $file, $post_id );
		wl_write_log( "createPostThumbnail [ attachment ID :: $attachment_id ]" );
		wl_write_log( "createPostThumbnail [ " . wp_get_attachment_image( $attachment_id, 'thumbnail' ) . " ]" );

		// Set it as the featured image.
		$this->assertTrue( false !== set_post_thumbnail( $post_id, $attachment_id ) );

		return $attachment_id;
	}

}
