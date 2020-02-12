<?php

use Wordlift\FAQ\Faq_To_Jsonld_Converter;

/**
 * Tests: Tests the FAQ Rest Controller
 * @since 3.26.0
 * @package wordlift
 * @subpackage wordlift/tests
 *
 */

class Faq_To_Jsonld_Converter_Test extends Wordlift_Unit_Test_Case {
	public function test_if_converter_returns_correct_type() {
		$converter = new Faq_To_Jsonld_Converter();
		$data = $converter->get_jsonld_for_faq();
		$this->assertArrayHasKey( '@type', $data );
		$this->assertEquals( $data['@type'], 'FAQPage');

	}
}