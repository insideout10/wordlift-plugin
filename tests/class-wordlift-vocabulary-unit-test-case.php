<?php

use Wordlift\Vocabulary\Analysis_Background_Service;
use Wordlift\Vocabulary\Vocabulary_Loader;

abstract class Wordlift_Vocabulary_Unit_Test_Case  extends Wordlift_Unit_Test_Case {

	public function setUp() {
		parent::setUp();
		if ( ! taxonomy_exists('post_tag') ) {
			register_taxonomy('post_tag', 'post');
		}
		// Reset all global filters.
		global $wp_filter, $wp_scripts, $wp_styles;
		$wp_filter = array();
		$wp_scripts = null;
		$wp_styles = null;

		$loader = new Vocabulary_Loader();
		$loader->init_vocabulary();
	}

	/**
	 * @return array
	 */
	public function getMockEntityData() {
		return array(
			'@context'         => 'http://schema.org',
			'@id'              => 'https://knowledge.cafemedia.com/food/entity/pie',
			'@type'            => 'Thing',
			'description'      => 'A pie is a baked dish which is usually made of a pastry dough casing that covers or completely contains a filling of various sweet or savoury ingredients. Pies are defined by their crusts. A filled pie (also single-crust or bottom-crust), has pastry lining the baking dish, and the filling is placed on top of...',
			'mainEntityOfPage' => 'https://app.wordlift.io/knowledge-cafemedia-com-food/entity/pie/',
			'name'             => 'pie',
			'sameAs'           =>
				array(
					0  => 'https://en.wikipedia.org/wiki/Pie',
					1  => 'http://purl.obolibrary.org/obo/FOODON_03401296',
					2  => 'http://www.wikidata.org/entity/Q13360264',
					3  => 'http://dbpedia.org/resource/Pie',
					4  => 'http://pl.dbpedia.org/resource/Pieróg',
					5  => 'http://rdf.freebase.com/ns/m.0mjqn',
					6  => 'http://ko.dbpedia.org/resource/파이',
					7  => 'http://wikidata.dbpedia.org/resource/Q13360264',
					8  => 'http://dbpedia.org/resource/Pie',
					9  => 'http://id.dbpedia.org/resource/Pastei',
					10 => 'http://www.wikidata.org/entity/Q13360264',
					11 => 'http://ja.dbpedia.org/resource/パイ',
					12 => 'http://fr.dbpedia.org/resource/Tourte_(plat)',
				),
			'url'              => 'https://app.wordlift.io/knowledge-cafemedia-com-food/entity/pie/',

		);
	}

	public function create_tag($name) {
		$data = wp_insert_term( $name, "post_tag" );
		return $data["term_id"];
	}

	public function create_unmatched_tag($name) {
		$data = wp_insert_term( $name, "post_tag" );
		$term_id =  $data["term_id"];
		update_term_meta( $term_id, Analysis_Background_Service::ENTITIES_PRESENT_FOR_TERM, 1);
		return $term_id;
	}


	public function create_unmatched_tags( $n ) {

		$tag_ids = array();
		for ( $i = 0; $i < $n; $i++) {
			$tag_id = $this->create_tag("tag_${i}");
			$tag_ids[] = $tag_id;
			update_term_meta( $tag_id, Analysis_Background_Service::ENTITIES_PRESENT_FOR_TERM, 1);
		}

		return $tag_ids;
	}
}