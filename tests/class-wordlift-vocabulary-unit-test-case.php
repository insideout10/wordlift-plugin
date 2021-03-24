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
		global $wp_filter, $wp_scripts;
		$wp_filter = array();
		$wp_scripts = null;

		$loader = new Vocabulary_Loader();
		$loader->init_vocabulary();
	}

	public function create_tag($name) {
		$data = wp_insert_term( $name, "post_tag" );
		return $data["term_id"];
	}


	public function create_tags( $n ) {

		$tag_ids = array();
		for ( $i = 0; $i < $n; $i++) {
			$tag_id = $this->create_tag("tag_${i}");
			$tag_ids[] = $tag_id;
			update_term_meta( $tag_id, Analysis_Background_Service::ENTITIES_PRESENT_FOR_TERM, 1);
		}

		return $tag_ids;
	}
}