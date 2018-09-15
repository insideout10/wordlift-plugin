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
		$this->assertEquals( 'image/png' , $response['content_type'] );
	}

	/**
	 * Test with gif image.
	 *
	 * @since 3.18.0
	 */
	public function test_gif() {
		$response = Wordlift_Remote_Image_Service::save_from_url( 'https://upload.wikimedia.org/wikipedia/commons/7/70/New-Wikipedia-explode.gif' );

		$this->assertInternalType( 'array', $response );
		$this->assertEquals( 3, count( $response ) );
		$this->assertEquals( 'image/gif' , $response['content_type'] );
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
		$this->assertEquals( 'image/jpeg' , $response['content_type'] );
	}

}
