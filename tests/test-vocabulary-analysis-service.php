<?php

use Wordlift\Vocabulary\Analysis_Service;

/**
 * @group vocabulary
 * Class Analysis_Progress_Endpoint_Test
 */
class Analysis_Service_Test extends \Wordlift_Vocabulary_Unit_Test_Case {

	public function test_should_return_service_data_url_in_correct_format() {
		$url = "http://www.wikidata.org/entity/Q275367";
		$expected_url = "http/www.wikidata.org/entity/Q275367";
		$this->assertEquals($expected_url, Analysis_Service::format_entity_url($url));
	}


}