<?php

namespace Wordlift\Webhooks;

use Wordlift\Cache\Ttl_Cache;
use Wordlift\Webhooks\Tabs\Settings_Tab;



/**
 * @since 3.31.0
 * @author
 * Added for feature request 1496 (Webhooks)
 */
class Webhooks_Loader {

	public function init_all_dependencies() {

		$settings_tab = new Settings_Tab();
		$settings_tab->init();

	}

	public function get_feature_slug() {
		return 'webhooksobject';
	}

	public function get_feature_default_value() {
		return false;
	}
}
