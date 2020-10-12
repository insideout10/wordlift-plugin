<?php
/**
 * Tests: Tests the hooks added for plugin wordlift for woocommerce.
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since ?.??.?
 * @package wordlift
 * @subpackage wordlift/tests
 */

/**
 * Class Test_Wl_For_Wc_Hooks
 * @group wl_for_wc
 */
class Test_Wl_For_Wc_Hooks extends Wordlift_Unit_Test_Case {

	public function test_when_wl_feature_disable_setup_screen_added_should_disable_setup_screen() {
		add_filter( 'wl_feature__enable__setup_screen', '__return_false' );
		global $wp_filter;
		$this->assertFalse( $this->is_action_exists( 'admin_init', array(
			Wordlift::get_instance()->admin_setup,
			'show_page'
		) ) );
	}

	public function test_when_wl_feature_disable_setup_screen_added_should_enable_setup_screen() {
		add_filter( 'wl_feature__enable__setup_screen', '__return_true' );
		global $wp_filter;
		$this->assertTrue( $this->is_action_exists( 'admin_init', array(
			Wordlift::get_instance()->admin_setup,
			'show_page'
		) ) );
	}

}