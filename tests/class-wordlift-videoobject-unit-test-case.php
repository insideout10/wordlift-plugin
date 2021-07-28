<?php

use Wordlift\Videoobject\Loader;

abstract class Wordlift_Videoobject_Unit_Test_Case extends Wordlift_Unit_Test_Case {

	public function setUp() {
		parent::setUp();
		if ( ! taxonomy_exists( 'post_tag' ) ) {
			register_taxonomy( 'post_tag', 'post' );
		}
		// Reset all global filters.
		global $wp_filter, $wp_scripts, $wp_styles;
		$wp_filter  = array();
		$wp_scripts = null;
		$wp_styles  = null;
		$loader     = new Loader();
		$loader->init_all_dependencies();
		add_filter( 'pre_http_request', array( $this, 'mock_api' ), 10, 3 );
	}

	function tearDown() {
		parent::tearDown();
		remove_filter( 'pre_http_request', array( $this, 'mock_api' ) );
	}

	public function mock_api( $response, $request, $url ) {

		$mock_response_file             = __DIR__ . "/assets/videoobject/" . md5( $url ) . '.json';
		$response_file_exists = file_exists( $mock_response_file );

		if ( $response_file_exists ) {
			return array(
				'body'     => file_get_contents( $mock_response_file ),
				'response' => array( 'code' => 200, )
			);
		}

		return $response;

	}


	public static function remove_all_whitespaces( $string ) {
		$string = str_replace( " ", "", $string );
		$string = str_replace( "\n", "", $string );
		$string = str_replace( "\t", "", $string );
		$string = str_replace( "\r", "", $string );

		return $string;
	}
}