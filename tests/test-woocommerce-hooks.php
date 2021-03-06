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
 * @group integrations
 */
class Test_Wl_For_Wc_Hooks extends Wordlift_Unit_Test_Case {

	public function test_blocks_enabled_correctly_without_filter() {
		if ( ! function_exists( 'register_block_type' ) ) {
			$this->markTestSkipped( "This test requires register_block_type function to be present" );
		}
		/**
		 * Removing this action because register_block_type triggers doing_it_wrong which causes phpunit error.
		 */
		remove_all_actions( 'doing_it_wrong_run' );
		$this->assertFalse( register_block_type( 'wordlift/chord' ) );
		$this->assertFalse( register_block_type( 'wordlift/navigator' ) );
		$this->assertFalse( register_block_type( 'wordlift/geomap' ) );
		$this->assertFalse( register_block_type( 'wordlift/timeline' ) );
		$this->assertFalse( register_block_type( 'wordlift/cloud' ) );
		$this->assertFalse( register_block_type( 'wordlift/vocabulary' ) );
		$this->assertFalse( register_block_type( 'wordlift/faceted-search' ) );
		$this->assertFalse( register_block_type( 'wordlift/products-navigator' ) );
	}

	public function test_blocks_disabled_with_filter_should_still_allow_products_navigator() {
		/**
		 * Removing this action because register_block_type triggers doing_it_wrong which causes phpunit error.
		 */
		remove_all_actions( 'doing_it_wrong_run' );

		if ( ! class_exists( 'WP_Block_Type_Registry' ) ) {
			$this->markTestSkipped( "This test requires WP_Block_Type_Registry class to be present" );
		}

		// remove all registered blocks
		$registered_blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();
		foreach ( $registered_blocks as $block ) {
			WP_Block_Type_Registry::get_instance()->unregister( $block );
		}

		add_filter( 'wl_feature__enable__blocks', '__return_false' );
		remove_all_actions( 'init' );

		new Wordlift();

		do_action( 'init' );
		$this->assertFalse( register_block_type( 'wordlift/products-navigator' ) );
		// should be able to register these blocks.
		$registry = WP_Block_Type_Registry::get_instance();
		$this->assertFalse( $registry->is_registered( 'wordlift/chord' ) );
		$this->assertFalse( $registry->is_registered( 'wordlift/navigator' ) );
		$this->assertFalse( $registry->is_registered( 'wordlift/geomap' ) );
		$this->assertFalse( $registry->is_registered( 'wordlift/timeline' ) );
		$this->assertFalse( $registry->is_registered( 'wordlift/cloud' ) );
		$this->assertFalse( $registry->is_registered( 'wordlift/vocabulary' ) );
		$this->assertFalse( $registry->is_registered( 'wordlift/faceted-search' ) );
	}

	public function test_on_default_state_three_widgets_should_be_registered() {
		global $wp_widget_factory;
		$this->assertTrue( isset( $wp_widget_factory->widgets['WordLift_Chord_Widget'] ) );
		$this->assertTrue( isset( $wp_widget_factory->widgets['WordLift_Geo_Widget'] ) );
		$this->assertTrue( isset( $wp_widget_factory->widgets['WordLift_Timeline_Widget'] ) );
	}

	public function test_on_filter_activated_widgets_should_not_be_present() {
		add_filter( 'wl_feature__enable__widgets', '__return_false' );
		global $wp_widget_factory;
		$wp_widget_factory->widgets = array();
		run_wordlift();
		// These 3 widgets shouldnt be present.
		$this->assertFalse( isset( $wp_widget_factory->widgets['WordLift_Chord_Widget'] ) );
		$this->assertFalse( isset( $wp_widget_factory->widgets['WordLift_Geo_Widget'] ) );
		$this->assertFalse( isset( $wp_widget_factory->widgets['WordLift_Timeline_Widget'] ) );
	}


	public function test_by_default_all_screens_should_be_registered() {
		$user = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user );

		global $wp_filter;
		$wp_filter = array();
		$wordlift  = new Wordlift();
		$wordlift->run();
		do_action( 'admin_menu' );

		global $submenu;

		$this->assertCount( 2, $submenu );
	}

	public function test_when_filter_turned_on_1_screen_should_be_registered() {
		global $wp_filter;
		$wp_filter = array();
		$user      = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user );
		add_filter( 'wl_feature__enable__screens', '__return_false' );
		add_filter( 'wl_feature__enable__match-terms', '__return_false' );
		global $submenu;
		$submenu = array();

		$wordlift = new Wordlift();
		$wordlift->run();
		do_action( 'admin_menu' );

		$this->assertCount( 1, $submenu );

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


	public function test_when_filter_not_active_show_in_menu_should_be_true() {
		$post_type = get_post_type_object(
			'entity'
		);
		$this->assertTrue( $post_type->show_in_menu );
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


	public function test_when_post_excerpt_filter_is_inactive_then_should_use_it() {
		global $wp_filter;
		$wp_filter = array();
		$wordlift  = new Wordlift();
		$wordlift->run();
		$this->assertArrayHasKey( 'do_meta_boxes', $wp_filter );
		$hook = $wp_filter['do_meta_boxes'];

		if ( gettype( $hook ) !== 'object' ) {
			$this->markTestSkipped( '$hook is not an object so skipping test' );
		}

		// Currently we only render post excerpt on this hook.
		$this->assertEquals( 1, count( $hook->callbacks ) );
	}

//	public function test_when_post_excerpt_filter_is_active_then_post_excerpt_should_not_be_generated() {
//		global $wp_filter;
//		$wp_filter = array();
//		add_filter( 'wl_feature__enable__post-excerpt', '__return_false' );
//		$wordlift = new Wordlift();
//		$wordlift->run();
//		$this->assertFalse( array_key_exists( 'do_meta_boxes', $wp_filter ) );
//	}


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

	public function test_filter_not_enabled_should_return_dataset_uri() {
		$post_id = $this->factory()->post->create();
		$uri     = Wordlift_Entity_Service::get_instance()->get_uri( $post_id );
		$this->assertNotNull( $uri );
	}

	public function test_when_filter_enabled_should_return_in_the_correct_format() {
		/**
		 * Provide local item IDs when Cloud Services aren’t activated (you don’t have a dataset URI)
		 * so that item IDs could be the permalink + “#” + cpt type slug,
		 * e.g. if I have a product at http://example.org/my-product the item ID would be http://example.org/my-product#product
		 */
		add_filter( 'wl_features__enable__dataset', '__return_false' );

		$post_id      = $this->factory()->post->create();
		$uri          = Wordlift_Entity_Service::get_instance()->get_uri( $post_id );
		$cpt_slug     = get_post_type( $post_id );
		$expected_uri = get_permalink( $post_id ) . "#${cpt_slug}";
		$this->assertEquals( $uri, $expected_uri );
	}


}
