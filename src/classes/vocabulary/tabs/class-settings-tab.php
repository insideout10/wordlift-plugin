<?php

namespace Wordlift\Vocabulary\Tabs;

use Wordlift\Vocabulary\Api\Api_Config;

/**
 * This class adds a tab to the wordlift settings screen.
 *
 * @since 3.30.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * Class Settings_Tab
 * @package Wordlift\Vocabulary\Tabs
 */
class Settings_Tab {

	public function connect_hook() {
		add_filter(
			'wl_admin_page_tabs',
			function ( $tabs ) {
				$tabs[] = array(
					'slug'  => 'match-terms',
					'title' => __( 'Match Terms', 'wordlift' ),
				);
				return $tabs;
			}
		);

		add_filter(
			'wl_admin_settings',
			function ( $settings ) {
				$settings['matchTerms'] = Api_Config::get_api_config();
				return $settings;
			}
		);
	}

}
