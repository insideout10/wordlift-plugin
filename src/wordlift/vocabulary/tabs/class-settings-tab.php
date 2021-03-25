<?php

namespace Wordlift\Vocabulary\Tabs;

/**
 * This class adds a tab to the wordlift settings screen.
 * @since 3.30.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * Class Settings_Tab
 * @package Wordlift\Vocabulary\Tabs
 */
class Settings_Tab {

	public function connect_hook() {
		add_filter( 'wl_admin_page_tabs', function ( $tabs ) {
			$tabs[] = __( 'Match Terms', 'wordlift' );
			return $tabs;
		} );
	}

}