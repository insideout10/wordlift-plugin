<?php
/**
 * Tests: Sparql Service Test.
 *
 * @since      3.13.2
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the {@link Wordlift_Sparql_Service_Test} class.
 *
 * @since      3.13.2
 * @package    Wordlift
 * @subpackage Wordlift/tests
 * @group sparql
 */
class Wordlift_Sparql_Service_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Sparql_Service} instance to test.
	 *
	 * @since  3.13.2
	 * @access private
	 * @var \Wordlift_Sparql_Service $sparql_service The {@link Wordlift_Sparql_Service} instance to test.
	 */
	private $sparql_service;

	/**
	 * The request id set in `wl_run_sparql_query` calls.
	 *
	 * @since  3.13.2
	 * @access private
	 * @var int $request_id The request id set in `wl_run_sparql_query` calls.
	 */
	private $request_id = null;

	/**
	 * Number of time the `wl_run_sparql_query` is called.
	 *
	 * @since  3.13.2
	 * @access private
	 * @var int $run_sparql_query_count Number of time the `wl_run_sparql_query` is called.
	 */
	private $run_sparql_query_count = 0;

	/**
	 * @inheritdoc
	 */
	function setUp() {
		parent::setUp();

		$this->sparql_service = $this->get_wordlift_test()->get_sparql_service();

	}

	/**
	 * Test the `queue` function.
	 */
	public function test_queue() {

		// Add our action hook to check that `wl_run_sparql_query` is called.
		add_action( 'wl_run_sparql_query', array(
			$this,
			'catch_wl_run_sparql_query',
		), 10, 1 );


		for ( $index = 1; $index <= 10; $index ++ ) {

			// Get the name for the first file.
			$filename = WL_TEMP_DIR . WL_REQUEST_ID . "-$index.sparql";

			// Sample contents.
			$contents = "Lorem Ipsum $index";

			// Check that the file doesn't exist.
			$this->assertFalse( file_exists( $filename ) );

			// Queue the same contents.
			$this->sparql_service->queue( $contents );

			// Check that the file now exists.
			$this->assertTrue( file_exists( $filename ) );

			// Get the contents.
			$actual = file_get_contents( $filename );

			// Check for equality.
			$this->assertEquals( "$contents\n", $actual );

			// Check that `wl_run_sparql_query` has been called once with the
			// request id.
			$this->assertEquals( WL_REQUEST_ID, $this->request_id );

			$this->assertEquals( 1, $this->run_sparql_query_count );

		}

		// Delete the test files.
		for ( $index = 1; $index <= 10; $index ++ ) {

			// Get the name for the first file.
			$filename = WL_TEMP_DIR . WL_REQUEST_ID . "-$index.sparql";

			// Delete the file.
			unlink( $filename );

		}

		// Remove the action hook.
		remove_action( 'wl_run_sparql_query', array(
			$this,
			'catch_wl_run_sparql_query',
		) );

	}

	/**
	 * Hook to the `wl_run_sparql_query` action.
	 *
	 * @param string $request_id The unique request id.
	 *
	 * @since 3.13.2
	 *
	 */
	public function catch_wl_run_sparql_query( $request_id ) {

		$this->run_sparql_query_count ++;

		$this->request_id = $request_id;

	}

}
