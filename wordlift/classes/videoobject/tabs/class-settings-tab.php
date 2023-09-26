<?php
/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
namespace Wordlift\Videoobject\Tabs;

class Settings_Tab {

	public function init() {

		add_filter(
			'wl_admin_page_tabs',
			function ( $tabs ) {
				$tabs[] = array(
					'slug'  => 'videoobject-settings',
					'title' => __( 'Video Settings', 'wordlift' ),
				);
				return $tabs;
			}
		);

	}

}
