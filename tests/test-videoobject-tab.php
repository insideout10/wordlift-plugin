<?php

/**
 * Class Videoobject_Validation_Test
 * @group videoobject
 */
class Videoobject_Tab_Test extends \Wordlift_Videoobject_Unit_Test_Case {

	public function test_should_have_entry_on_wordlift_admin_settings() {
		$tabs = apply_filters( 'wl_admin_page_tabs', array() );
		$this->assertCount( 1, $tabs );
		$video_tab = $tabs[0];
		$this->assertSame( 'video-object-settings', $video_tab['slug'] );
	}

}