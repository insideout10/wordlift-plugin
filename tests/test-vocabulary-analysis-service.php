<?php

use Wordlift\Vocabulary\Analysis_Service;
use Wordlift\Vocabulary\Data\Entity_List\Default_Entity_List;

/**
 * @group vocabulary
 * Class Analysis_Progress_Endpoint_Test
 */
class Analysis_Service_Test extends \Wordlift_Vocabulary_Unit_Test_Case {

	public function test_should_return_service_data_url_in_correct_format() {
		$url          = 'http://www.wikidata.org/entity/Q275367';
		$expected_url = 'http/www.wikidata.org/entity/Q275367';
		$this->assertEquals( $expected_url, Analysis_Service::format_entity_url( $url ) );
	}

	public function test_when_there_are_network_dataset_ids_scope_should_be_network_only() {
		Wordlift_Configuration_Service::get_instance()->set_network_dataset_ids( array( 'one' ) );
		$service = new Analysis_Service( null, null );
		$this->assertEquals( 'network-only', $service->get_scope() );
	}

	public function test_when_there_are_no_network_dataset_ids_scope_should_be_cloud_only() {
		Wordlift_Configuration_Service::get_instance()->set_network_dataset_ids( array() );
		$service = new Analysis_Service( null, null );
		$this->assertEquals( 'cloud-only', $service->get_scope() );
	}

	public function test_given_wordlift_entity_data_should_return_the_compact_version() {
		$mock_data = array(
			'@type'       => array( 0 => 'http://schema.org/Thing' ),
			'name'        => array(
				0 => array(
					'@language' => 'en',
					'@value'    => 'pie',
				),
			),
			'description' => array(
				0 => array(
					'@language' => 'en',
					'@value'    => 'A pie is a baked dish which is usually made of a pastry dough casing that covers or completely contains a filling of various sweet or savoury ingredients. Pies are defined by their crusts. A filled pie (also single-crust or bottom-crust), has pastry lining the baking dish, and the filling is placed on top of the pastry but left open. A top-crust pie has the filling in the bottom of the dish and is covered with a pastry or other covering before baking. A two-crust pie has the filling completely enclosed in the pastry shell. Shortcrust pastry is a typical kind of pastry used for pie crusts, but many things can be used, including baking powder biscuits, mashed potatoes, and crumbs.',
				),
			),
			'@id'         => 'https://knowledge.cafemedia.com/food/entity/pie',
			'sameAs'      => array(
				0  => array( '@id' => 'http://fr.dbpedia.org/resource/Tourte_(plat)' ),
				1  => array( '@id' => 'http://ja.dbpedia.org/resource/パイ' ),
				2  => array( '@id' => 'http://id.dbpedia.org/resource/Pastei' ),
				3  => array( '@id' => 'http://wikidata.dbpedia.org/resource/Q13360264' ),
				4  => array( '@id' => 'http://ko.dbpedia.org/resource/파이' ),
				5  => array( '@id' => 'http://rdf.freebase.com/ns/m.0mjqn' ),
				6  => array( '@id' => 'http://pl.dbpedia.org/resource/Pieróg' ),
				7  => array( '@id' => 'http://dbpedia.org/resource/Pie' ),
				8  => array( '@id' => 'http://www.wikidata.org/entity/Q13360264' ),
				9  => array( '@id' => 'http://purl.obolibrary.org/obo/FOODON_03401296' ),
				10 => array( '@id' => 'https://en.wikipedia.org/wiki/Pie' ),
			),
		);

		$expected_data = array(
			'@type'       => array( 'Thing' ),
			'name'        => array( 'pie' ),
			'description' => array( 'A pie is a baked dish which is usually made of a pastry dough casing that covers or completely contains a filling of various sweet or savoury ingredients. Pies are defined by their crusts. A filled pie (also single-crust or bottom-crust), has pastry lining the baking dish, and the filling is placed on top of the pastry but left open. A top-crust pie has the filling in the bottom of the dish and is covered with a pastry or other covering before baking. A two-crust pie has the filling completely enclosed in the pastry shell. Shortcrust pastry is a typical kind of pastry used for pie crusts, but many things can be used, including baking powder biscuits, mashed potatoes, and crumbs.' ),
			'@id'         => 'https://knowledge.cafemedia.com/food/entity/pie',
			'sameAs'      => array(
				'http://ja.dbpedia.org/resource/パイ',
				'http://id.dbpedia.org/resource/Pastei',
				'http://wikidata.dbpedia.org/resource/Q13360264',
				'http://fr.dbpedia.org/resource/Tourte_(plat)',
				'http://ko.dbpedia.org/resource/파이',
				'http://rdf.freebase.com/ns/m.0mjqn',
				'http://pl.dbpedia.org/resource/Pieróg',
				'http://dbpedia.org/resource/Pie',
				'http://www.wikidata.org/entity/Q13360264',
				'http://purl.obolibrary.org/obo/FOODON_03401296',
				'https://en.wikipedia.org/wiki/Pie',
			),
		);

		$result = Default_Entity_List::compact_jsonld( $mock_data );
		sort( $expected_data['sameAs'] );
		sort( $result['sameAs'] );

		$this->assertEquals( $expected_data, $result );
	}

}
