<?php

namespace Wordlift\Webhooks;

/**
 * @since 3.34.0
 */
class Webhooks_Loader {

	const URLS_OPTION_NAME = 'wl_webhooks_urls';

	public function init() {

		$settings_tab = new Webhooks_Settings();
		$settings_tab->init();

		new Webhooks_Manager();
	}

}
