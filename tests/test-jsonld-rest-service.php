<?php
/**
 * Tests: JSON-LD Service Test.
 *
 * This file contains the tests for the {@link Wordlift_Jsonld_Service} class.
 *
 * @since   3.8.0
 * @package Wordlift
 */

/**
 * Define the test class.
 *
 * @since   3.8.0
 * @package Wordlift
 * @group jsonld
 */
class Wordlift_Jsonld_Endpoint_Test extends Wordlift_Unit_Test_Case {

	/**
	 * @var WP_REST_Server
	 */
	private $server;

	private const META_KEY = 'META_KEY';

	public function setUp() {
		parent::setUp();

		/** @var WP_REST_Server $wp_rest_server */
		global $wp_rest_server;
		$wp_rest_server = new WP_REST_Server();
		$this->server   = $wp_rest_server;
		do_action( 'rest_api_init' );
	}

	public function tearDown() {
		parent::tearDown();

		/** @var WP_REST_Server $wp_rest_server */
		global $wp_rest_server;
		$wp_rest_server = null;
		unset( $this->server );
	}

	/**
	 * @return Generator<array<string,array<string>>>
	 */
	private static function provide_encoded_meta_cases() {
		yield 'handle url with white spaces' => array(
			'https://www.example.org/with spaces.htm',
			array(
				'https://www.example.org/with spaces.htm',
				'https://www.example.org/with+spaces.htm',
			),
		);

		yield 'handle url with encoded spaces' => array(
			'https://www.abc_example.org/with+spaces.htm',
			array(
				'https://www.abc_example.org/with+spaces.htm',
				'https://www.abc_example.org/with spaces.htm',
			),
		);

		yield 'handle url with mixed spaces (encoded or not)' => array(
			'https://www.def_example.org/with+spaces+page.htm',
			array(
				'https://www.def_example.org/with+spaces page.htm',
				'https://www.def_example.org/with+spaces+page.htm',
				'https://www.def_example.org/with spaces page.htm',
			),
		);

		yield 'handle url with white space as first character' => array(
			' https://www.ghi_example.org/page.htm',
			array(
				' https://www.ghi_example.org/page.htm',
				'+https://www.ghi_example.org/page.htm',
			),
		);

		yield 'handle url with encoded space as first character' => array(
			'+https://www.lmn_example.org/page.htm',
			array(
				'+https://www.lmn_example.org/page.htm',
				' https://www.lmn_example.org/page.htm',
			),
		);

		yield 'should avoid to decode query params from url' => array(
			'https://www.opq_example.org/with?encoded=%0A123%24',
			array(
				'https://www.opq_example.org/with?encoded=%0A123%24',
			),
		);

		yield 'should avoid to decode whitespaces as query params' => array(
			'https://www.rns_example.org/with_space.htm?a=aaa%20bbb',
			array(
				'https://www.rns_example.org/with_space.htm?a=aaa%20bbb',
			),
		);

		yield 'should avoid to decode encoded url path' => array(
			'https://www.pqr_example.org/with%20space/url.htm',
			array(
				'https://www.pqr_example.org/with%20space/url.htm',
			),
		);

		yield 'should not match a different url' => array(
			'https://www.stu_foobar.org/url.htm',
			array(
				'https://www.stu_example.org/url.htm',
			),
			false,
		);
	}

	public static function provide_encoded_meta() {
		// permutate a test case for each of provided query strings
		foreach ( self::provide_encoded_meta_cases() as $test_title => $args ) {
			list($persisted_value, $search_strings, $should_match) =
				// fill the array with null values to ensure that the array has at least 3 elements
				array_merge( $args, array_fill( count( $args ), 3, null ) );
			if ( isset( $search_strings ) && is_array( $search_strings ) ) {
				$cnt = 0;
				foreach ( $search_strings as $search_string ) {
					++$cnt;
					$tmp_key = sprintf( '%s - case #%d', $test_title, $cnt );
					yield $tmp_key => array(
						$persisted_value,
						$search_string,
						null !== $should_match ? $should_match : true,
					);
				}
			}
		}
	}

	/** @dataProvider provide_encoded_meta */
	public function test_should_handle_encoded_meta( string $stored_url, string $search_string, bool $should_match ) {
		$this->load_fixture(
			$stored_url,
			$meta_key = self::META_KEY
		);

		$request = new WP_REST_Request(
			'GET',
			'/' . WL_REST_ROUTE_DEFAULT_NAMESPACE . '/jsonld/meta/' . rawurlencode( $meta_key )
		);

		$request->set_query_params(
			array(
				'meta_value' => $search_string,
			)
		);

		$response = $this->server->dispatch( $request );
		self::assertSame( 200, $response->get_status() );
		$data = $response->get_data();

		self::assertIsArray( $data );
		if ( false === $should_match ) {
			self::assertEmpty( $data );
		} else {
			self::assertCount( 1, $data, 'Unable to find the expected data in the response' );
			$actual_content = &$data[0];
			self::assertNotNull( $actual_content );
			self::assertArrayHasKey( '@id', $actual_content );
		}
	}

	/**
	 * @param string $url
	 * @param string $meta_key
	 * @return void
	 */
	private function load_fixture( $url, $meta_key ) {
		$post_id = $this->factory()->post->create(
			array(
				'post_status' => 'publish',
			)
		);

		add_post_meta( $post_id, $meta_key, $url );
	}
}
