<?php
/**
 * Tests: Image Service test.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the {@link Wordlift_Image_Service } class.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */
class Wordlift_Image_Service_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test failures.
	 *
	 * @since 3.18.0
	 */
	public function test_failures() {
		// Test with 404 page.
		$response = Wordlift_Remote_Image_Service::save_from_url( 'https://en.wikipedia.org/404' );

		// Test with SVG, which is not supported.
		$response_1 = Wordlift_Remote_Image_Service::save_from_url( 'http://upload.wikimedia.org/wikipedia/commons/a/a6/Flag_of_Rome.svg' );

		// Test with non image link.
		$response_2 = Wordlift_Remote_Image_Service::save_from_url( 'https://en.wikipedia.org/wiki/Main_Page' );

		$this->assertFalse( $response );
		$this->assertFalse( $response_1 );
		$this->assertFalse( $response_2 );
	}

	/**
	 * Test with png image.
	 *
	 * @since 3.18.0
	 */
	public function test_png() {
		$response = Wordlift_Remote_Image_Service::save_from_url( 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a6/Flag_of_Rome.svg/2000px-Flag_of_Rome.svg.png' );

		$this->assertInternalType( 'array', $response );
		$this->assertEquals( 3, count( $response ) );
		$this->assertEquals( 'image/png', $response['content_type'] );
	}

	/**
	 * Test with gif image.
	 *
	 * @since 3.18.0
	 */
	public function test_gif() {
		$response = Wordlift_Remote_Image_Service::save_from_url( 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2c/Rotating_earth_%28large%29.gif/200px-Rotating_earth_%28large%29.gif' );

		$this->assertInternalType( 'array', $response, 'Expecting array, instead got: ' .var_export( $response, true ) );
		$this->assertEquals( 3, count( $response ) );
		$this->assertEquals( 'image/gif', $response['content_type'] );
	}

	/**
	 * Test with jpg image.
	 *
	 * @since 3.18.0
	 */
	public function test_jpg() {
		$response = Wordlift_Remote_Image_Service::save_from_url( 'https://upload.wikimedia.org/wikipedia/commons/f/ff/Wikipedia_logo_593.jpg' );

		$this->assertInternalType( 'array', $response );
		$this->assertEquals( 3, count( $response ) );
		$this->assertEquals( 'image/jpeg', $response['content_type'] );
	}

	/**
	 * Test that an image 800x600 doesn't yield any source.
	 *
	 * @since 3.19.4
	 */
	public function test_attachment_800x600() {

		$sources = $this->_test_attachment( '/assets/cat-800x600.jpg' );

		$this->assertEmpty( $sources, 'Resolution too low, expect no additional sources.' );

	}

	/**
	 * Test that an image 1200x1200 yields 3 sources.
	 *
	 * @since 3.19.4
	 */
	public function test_attachment_1200x1200() {

		$sources = $this->_test_attachment( '/assets/cat-1200x1200.jpg' );

		$this->assertCount( 3, $sources, 'Expect 3 sources.' );
		$this->assertArraySubset( array( 1 => 1200, 2 => 675 ), $sources[0] );
		$this->assertArraySubset( array( 1 => 1200, 2 => 900 ), $sources[1] );
		$this->assertArraySubset( array( 1 => 1200, 2 => 1200 ), $sources[2] );

	}

	/**
	 * Test that an image 1280x960 yields 2 sources.
	 *
	 * @since 3.19.4
	 */
	public function test_attachment_1280x960() {

		$sources = $this->_test_attachment( '/assets/cat-1280x960.jpg' );

		$this->assertCount( 2, $sources, 'Expect 3 sources.' );
		$this->assertArraySubset( array( 1 => 1200, 2 => 675 ), $sources[0] );
		$this->assertArraySubset( array( 1 => 1200, 2 => 900 ), $sources[1] );

	}

	/**
	 * Test that an image 2392x2500 yields 3 sources.
	 *
	 * @since 3.19.4
	 */
	public function test_attachment_2392x2500() {

		$sources = $this->_test_attachment( '/assets/cat-2392x2500.jpg' );

		$this->assertCount( 3, $sources, 'Expect 3 sources.' );
		$this->assertArraySubset( array( 1 => 1200, 2 => 675 ), $sources[0] );
		$this->assertArraySubset( array( 1 => 1200, 2 => 900 ), $sources[1] );
		$this->assertArraySubset( array( 1 => 1200, 2 => 1200 ), $sources[2] );

	}

	/**
	 * Upload an attachment and return the generated sources.
	 *
	 * @since 3.19.4
	 *
	 * @param string $file The file name relative to the current folder.
	 *
	 * @return array The array of sources (or an empty array).
	 */
	private function _test_attachment( $file ) {

		$file = dirname( __FILE__ ) . $file;

		$attachment_id = $this->factory()->attachment->create_upload_object( $file );

		return Wordlift_Image_Service::get_sources( $attachment_id );
	}

}
