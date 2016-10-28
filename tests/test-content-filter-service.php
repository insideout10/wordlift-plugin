<?php
/**
 * Define the test for the {@link Wordlift_Content_Filter_Service}.
 */

/**
 * Test the {@link Wordlift_Content_Filter_Service} class.
 *
 * @since 3.6.0
 */
class Wordlift_Content_Filter_Service_Test extends WP_UnitTestCase {

	/**
	 * The {@link Wordlift_Content_Filter_Service} instance to test.
	 * @since 3.8.0
	 * @access private
	 * @var Wordlift_Content_Filter_Service $content_filter_service A {@link Wordlift_Content_Filter_Service} instance.
	 */
	private $content_filter_service;

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();

		$this->content_filter_service = new Wordlift_Content_Filter_Service( NULL );
	}

}
