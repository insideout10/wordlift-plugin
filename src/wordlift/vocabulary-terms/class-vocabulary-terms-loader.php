<?php

namespace Wordlift\Vocabulary_Terms;

use Wordlift\Common\Loader\Default_Loader;
use Wordlift\Vocabulary_Terms\Jsonld\Jsonld_Generator;


class Vocabulary_Terms_Loader extends Default_Loader {


	public function init_all_dependencies() {
		new Entity_Type();
		new Term_Metabox();
		$jsonld = new Jsonld_Generator();
		$jsonld->init();
	}

	protected function get_feature_slug() {
		return 'no-vocabulary-terms';
	}

	protected function get_feature_default_value() {
		return false;
	}


}