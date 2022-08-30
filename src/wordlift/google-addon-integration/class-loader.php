<?php

namespace Wordlift\Google_Addon_Integration;

use Wordlift\Common\Loader\Default_Loader;
use Wordlift\Google_Addon_Integration\Pages\Import_Page;

/**
 * @since 3.35.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Loader extends Default_Loader {

	public function init_all_dependencies() {
		new Import_Page();
		$rest_endpoint = new Rest_Endpoint();
		$rest_endpoint->init();
	}

	public function get_feature_slug() {
		return 'google-addon-integration';
	}

	public function get_feature_default_value() {
		return true;
	}
}
