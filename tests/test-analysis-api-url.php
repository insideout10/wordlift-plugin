<?php
/**
 * Tests: the analysis requests can be sent to another host (e.g. the v3 engine) via `wl_analysis_api_url`.
 *
 * @group analysis
 */
class Wordlift_Analysis_Api_Url_Test extends Wordlift_Unit_Test_Case {

	const V3_URL = 'https://resolve.example.org';

	public function tearDown() {
		remove_all_filters( 'wl_analysis_api_url' );
		parent::tearDown();
	}

	private function use_v3() {
		add_filter(
			'wl_analysis_api_url',
			function () {
				return self::V3_URL . '/';
			}
		);
	}

	public function test_without_override_urls_are_unchanged() {
		$url = WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE . 'analysis/single';
		$this->assertEquals( $url, wl_rewrite_analysis_api_url( $url ) );
	}

	public function test_with_override_analysis_urls_are_rewritten() {
		$this->use_v3();

		$this->assertEquals(
			self::V3_URL . '/analysis/single',
			wl_rewrite_analysis_api_url( WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE . 'analysis/single' )
		);
		$this->assertEquals(
			self::V3_URL . '/analysis/v2/analyze',
			apply_filters( 'wl_api_service_api_url_path', WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE . 'analysis/v2/analyze' )
		);
		$this->assertEquals(
			self::V3_URL . '/analysis/single',
			apply_filters( 'wl_api_service__request_url', untrailingslashit( WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE ) . '/analysis/single', '/analysis/single' )
		);
	}

	public function test_with_override_other_urls_are_unchanged() {
		$this->use_v3();

		$url = WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE . 'accounts/me';
		$this->assertEquals( $url, wl_rewrite_analysis_api_url( $url ) );
	}

	public function test_with_override_the_v1_analysis_request_goes_to_v3() {
		$this->use_v3();

		$requested_url = null;
		add_filter(
			'pre_http_request',
			function ( $preempt, $args, $url ) use ( &$requested_url ) {
				$requested_url = $url;

				return array(
					'headers'  => array(),
					'body'     => '{ "entities": {}, "annotations": {}, "topics": {} }',
					'response' => array(
						'code'    => 200,
						'message' => 'OK',
					),
					'cookies'  => array(),
				);
			},
			PHP_INT_MAX,
			3
		);

		\Wordlift\Analysis\V1_Analysis_Service::get_instance()
			->get_analysis_response( '{"content":"x"}', 'application/json', 0 );

		$this->assertEquals( self::V3_URL . '/analysis/single', $requested_url );
	}

}
