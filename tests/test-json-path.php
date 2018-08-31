<?php

/**
 * Class JsonPathTest
 */
class JsonPathTest extends Wordlift_Unit_Test_Case {

	function test_expand() {

		$expected_1 = '$[0]["http://example.org/image"][!(@id)][?(@.@type == "http://example.org/thumbnail")]["http://example.org/title"][?(@.@language == "en")].@value';

		$expr_1 = '$[0]["example:image"][!(@id)]' .
		          '[?(@.@type == "example:thumbnail")]["example:title"][?(@.@language == "en")].@value';

		$expr_1_exp = $this->expand( $expr_1 );

		$this->assertEquals( $expected_1, $expr_1_exp );

		$expected_2 = '$[0][\'http://example.org/image\'][!(@id)][?(@.@type == "http://example.org/thumbnail")][\'http://example.org/title\'][?(@.@language == "en")].@value';

		$expr_2 = '$[0].example:image.[!(@id)]' .
		          '[?(@.@type == "example:thumbnail")].example:title.[?(@.@language == "en")].@value';

		$expr_2_exp = $this->expand( $expr_2 );

		$this->assertEquals( $expected_2, $expr_2_exp );

	}

	function expand( $expr ) {

		$prefix    = 'example:';
		$namespace = 'http://example.org/';

		$expr = preg_replace( "/(['\"])$prefix/", "\${1}$namespace", $expr );
		$expr = preg_replace( "/\\.$prefix([^.]+)\\./", "['$namespace\${1}']", $expr );

		return $expr;
	}

}
