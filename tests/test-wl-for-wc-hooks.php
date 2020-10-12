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

	public function test_when_wl_feature_enable_setup_screen_added_should_disable_setup_screen() {
		global $wp_filter;
		$wp_filter = array();
		add_filter( 'wl_feature__enable__setup_screen', '__return_false' );
		$wordlift = new Wordlift();
		$wordlift->run();
		$this->assertFalse( remove_action( 'admin_init', array(
			Wordlift::get_instance()->admin_setup,
			'show_page'
		) ) );
	}

	public function test_when_wl_feature_enable_setup_screen_added_should_enable_setup_screen() {
		global $wp_filter;
		$wp_filter = array();
		add_filter( 'wl_feature__enable__setup_screen', '__return_true');
		$wordlift = new Wordlift();
		$wordlift->run();
		$this->assertTrue( remove_action( 'admin_init', array(
			Wordlift::get_instance()->admin_setup,
			'show_page'
		) ) );
	}

}