<?php

namespace Wordlift\Vocabulary_Terms;

use Wordlift\Common\Loader\Default_Loader;


class Vocabulary_Terms_Loader extends Default_Loader {

	public function init_all_dependencies() {
		new Entity_Type();
	}

	protected function get_feature_slug() {
		return 'no-vocabulary-terms';
	}

	protected function get_feature_default_value() {
		return false;
	}


}