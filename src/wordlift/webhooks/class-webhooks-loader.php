<?php

namespace Wordlift\Webhooks;

use Wordlift\Cache\Ttl_Cache;
use Wordlift\Webhooks\Tabs\Settings_Tab;
use Wordlift\Webhooks\Api\Rest_Controller;


/**
 * @since 3.31.0
 * @author
 * Added for feature request 1496 (Webhooks)
 */
class Webhooks_Loader {

	const URLS_OPTION_NAME = 'wl_webhooks_urls';

	public function init_all_dependencies() {

		$settings_tab = new Settings_Tab();
		$settings_tab->init();

		return $settings_tab;
	}

	public function get_rest_controller_object() {
		return new Rest_Controller();
	}
}
