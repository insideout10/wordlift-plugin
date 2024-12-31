<?php

use Wordlift\Content\WordPress\Wordpress_Content_Id;
use Wordlift\Content\WordPress\Wordpress_Content_Service;

/**
 * Tests: Tests the hooks added for plugin wordlift for woocommerce.
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @package wordlift
 * @subpackage wordlift/tests
 *
 * @group woocommerce-extension
 */
class Test_Wl_For_Wc_Hooks extends Wordlift_Unit_Test_Case {

	public function test_all_blocks_disabled_except_product_navigator() {
		if ( ! function_exists( 'register_block_type' ) ) {
			$this->markTestSkipped( "This test requires register_block_type function to be present" );
		}

		$block_type_registry = WP_Block_Type_Registry::get_instance();
		$this->assertFalse( $block_type_registry->is_registered( 'wordlift/chord' ) );
		$this->assertFalse( $block_type_registry->is_registered( 'wordlift/navigator' ) );
		$this->assertFalse( $block_type_registry->is_registered( 'wordlift/geomap' ) );
		$this->assertFalse( $block_type_registry->is_registered( 'wordlift/timeline' ) );
		$this->assertFalse( $block_type_registry->is_registered( 'wordlift/cloud' ) );
		$this->assertFalse( $block_type_registry->is_registered( 'wordlift/vocabulary' ) );
		$this->assertFalse( $block_type_registry->is_registered( 'wordlift/faceted-search' ) );

		$this->assertTrue( $block_type_registry->is_registered( 'wordlift/products-navigator' ) );
	}

	public function test_on_filter_activated_widgets_should_not_be_present() {
		global $wp_widget_factory;
		$this->assertArrayNotHasKey( 'WordLift_Chord_Widget', $wp_widget_factory->widgets );
		$this->assertArrayNotHasKey( 'WordLift_Geo_Widget', $wp_widget_factory->widgets );
		$this->assertArrayNotHasKey( 'WordLift_Timeline_Widget', $wp_widget_factory->widgets );
	}

	public function test_when_filter_turned_on_1_screen_should_be_registered() {

		// @todo: it's not clear how this test should work.
		$this->markTestSkipped( 'Revise this test.' );

		global $submenu;

		$current_user = get_current_user_id();
		$admin_user   = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $admin_user );
		set_current_screen( 'dashboard' );

		do_action( 'admin_menu' );

		$this->assertCount( 1, $submenu, 'This is what I got: ' . var_export( $submenu, true ) );

		$this->assertEqualSets( array(
			'index.php' => array(
				array(
					'',
					'manage_options',
					'wl-setup',
					''
				)
			)
		), $submenu );

		wp_set_current_user( $current_user );

		// Clean up the temporary user.
		wp_delete_user( $admin_user );

	}


	public function test_when_filter_not_active_should_show_two_notices() {
		// set key to empty
		update_option( 'wl_general_settings', array() );
		global $wp_filter;
		$wp_filter = array();
		$wordlift  = new Wordlift();
		$wordlift->run();
		/**
		 * @var $wp_hook WP_Hook
		 */
		$wp_hook = $wp_filter['admin_notices'];

		if ( gettype( $wp_hook ) !== 'object' ) {
			$this->markTestSkipped( '$wp_hook is not an object so skipping test' );
		}

		$this->assertNotEquals( 0, count( $wp_hook->callbacks ) );
	}

	public function test_when_filter_active_should_not_show_notices() {
		// set key to empty
		update_option( 'wl_general_settings', array() );
		global $wp_filter;
		$wp_filter = array();
		add_filter( 'wl_feature__enable__notices', '__return_false' );
		$wordlift = new Wordlift();
		$wordlift->run();

		$this->assertFalse( array_key_exists( 'admin_notices', $wp_filter ) );
	}

	public function test_when_filter_enabled_show_in_menu_should_be_false() {

		if ( ! function_exists( 'unregister_post_type' ) ) {
			$this->markTestSkipped( "This test requires unregister_post_type function to be present" );
		}

		unregister_post_type( 'entity' );
		add_filter( 'wl_feature__enable__vocabulary', '__return_false' );
		Wordlift_Entity_Post_Type_Service::get_instance()->register();
		$post_type = get_post_type_object(
			'entity'
		);
		$this->assertFalse( $post_type->show_in_menu );
	}

	public function test_when_filter_not_enabled_should_do_analysis() {
		global $wp_filter;
		// since the filter is enabled, we should be able to do analysis.
		$hook = $wp_filter['wp_ajax_wl_analyze'];

		if ( gettype( $hook ) !== 'object' ) {
			$this->markTestSkipped( '$hook is not an object so skipping test' );
		}

		$this->assertCount( 1, $hook->callbacks );
		$callback      = array_pop( $hook->callbacks );
		$callback_data = $callback['wl_ajax_analyze_action'];
		$this->assertEquals( 'wl_ajax_analyze_action', $callback_data['function'] );
	}

	public function test_when_filter_enabled_should_analyze_and_return_empty_results() {
		global $wp_filter;
		$wp_filter = array();
		add_filter( 'wl_feature__enable__analysis', '__return_false' );
		run_wordlift();
		// since the filter is enabled, we should be able to do analysis.
		$hook = $wp_filter['wp_ajax_wl_analyze'];

		if ( gettype( $hook ) !== 'object' ) {
			$this->markTestSkipped( '$hook is not an object so skipping test' );
		}

		$this->assertCount( 1, $hook->callbacks );
		$callback      = array_pop( $hook->callbacks );
		$callback_data = $callback['wl_ajax_analyze_disabled_action'];
		$this->assertEquals( 'wl_ajax_analyze_disabled_action', $callback_data['function'] );
	}

	public function test_when_filter_enabled_should_return_in_the_correct_format() {
		$post_id  = $this->factory()->post->create();
		$uri      = Wordpress_Content_Service::get_instance()
		                                     ->get_entity_id( Wordpress_Content_Id::create_post( $post_id ) );
		$this->assertEquals( $uri, "http://example.org/?p=$post_id#post/$post_id" );
	}


	public function test_should_return_valid_entity_uri_when_dataset_is_not_active() {
		$post_id = $this->factory()->post->create();
		$entity_uri = Wordlift_Entity_Service::get_instance()->get_uri( $post_id );
		$this->assertNotNull( $entity_uri, 'The entity uri should not be null' );
	}

}
