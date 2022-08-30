<?php
namespace Wordlift\Synonym;

use Wordlift\Common\Loader\Default_Loader;

/**
 * @since 3.31.7
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Loader extends Default_Loader {

	public function init_all_dependencies() {
		new Rest_Field();
	}

	protected function get_feature_slug() {
		return 'add-synonyms';
	}

	protected function get_feature_default_value() {
		return true;
	}
}
