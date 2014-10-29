<?php
/**
 * Test for the Prefixes module.
 */
require_once( 'functions.php' );

/**
 * Class JsonPathTest
 */
class JsonPathTest extends WP_UnitTestCase
{

	/**
	 * Set up the test.
	 */
	function setUp()
	{

		parent::setUp();

		// Configure WordPress with the test settings.
//		wl_configure_wordpress_test();

		// Empty the blog.
//		wl_empty_blog();

	}


	/**
	 * Test the caching of a URL.
	 */
	function test_json_path()
	{

		echo( 'testing' );

		$wl_view = new WL_View( 'http://localhost:8000/jsons/' );
		$wl_view->load( '1' );

		$result_1 = $wl_view->get_property( '$[0]["http://example.org/title"][?(@.@language == "en")].@value' );
//		var_dump( $result_1 );

//		$result_2 = $wl_view->get_property( '$[0]["http://example.org/image"][!(@id)]" .
//			"[?(@.@type == "http://example.org/thumbnail")]["http://example.org/title"][?(@.@language == "en")].@value' );
//		var_dump( $result_2 );

		$result_3 = $wl_view->get_property( $this->expand( '$[0].example:image.[!(@id)]' .
			'[?(@.@type == "example:thumbnail")].example:title.[?(@.@language == "en")].@value' ) );
		var_dump( $result_3 );

	}

	function test_expand() {

		$expected_1 = '$[0]["http://example.org/image"][!(@id)][?(@.@type == "http://example.org/thumbnail")]["http://example.org/title"][?(@.@language == "en")].@value';

		$expr_1     = '$[0]["example:image"][!(@id)]' .
			'[?(@.@type == "example:thumbnail")]["example:title"][?(@.@language == "en")].@value';

		$expr_1_exp = $this->expand( $expr_1 );

		echo( $expr_1_exp . "\n" );

		$this->assertEquals( $expected_1, $expr_1_exp );

		$expected_2 = '$[0][\'http://example.org/image\'][!(@id)][?(@.@type == "http://example.org/thumbnail")][\'http://example.org/title\'][?(@.@language == "en")].@value';

		$expr_2     = '$[0].example:image.[!(@id)]' .
			'[?(@.@type == "example:thumbnail")].example:title.[?(@.@language == "en")].@value';

		$expr_2_exp = $this->expand( $expr_2 );

		echo( $expr_2_exp . "\n" );

		$this->assertEquals( $expected_2, $expr_2_exp );

	}

	function expand( $expr ) {

		$prefix    = 'example:';
		$namespace = 'http://example.org/';

		$expr      = preg_replace( "/(['\"])$prefix/", "\${1}$namespace", $expr );
		$expr      = preg_replace( "/\\.$prefix([^.]+)\\./", "['$namespace\${1}']", $expr );

		return $expr;
	}

}