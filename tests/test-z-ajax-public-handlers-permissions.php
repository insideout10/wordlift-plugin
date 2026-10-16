<?php
/**
 * Tests: Permissions of public AJAX handlers.
 *
 * @since   3.54.18
 * @package Wordlift
 */

use Wordlift\Cache\Ttl_Cache;
use Wordlift\Cache\Ttl_Cache_Cleaner;

/**
 * Class Wordlift_Ajax_Public_Handlers_Permissions_Test
 *
 * @group   ajax
 *
 * @since   3.54.18
 * @package Wordlift
 */
class Wordlift_Ajax_Public_Handlers_Permissions_Test extends Wordlift_Ajax_Unit_Test_Case {

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();

		// Prevent the setup wizard redirect on `admin_init` for administrators.
		delete_transient( '_wl_activation_redirect' );
	}

	/**
	 * Calls a handler and returns the decoded JSON response.
	 *
	 * @param string $action The AJAX action.
	 * @param string $body Optional request body.
	 *
	 * @return mixed The decoded response.
	 */
	private function call_ajax( $action, $body = null ) {
		try {
			$this->_handleAjax( $action, $body );
		} catch ( WPAjaxDieContinueException $e ) {
			// Expected: the handler ends with wp_die().
		}

		return json_decode( $this->_last_response );
	}

	public function test_chord_with_non_public_root_post_returns_an_empty_graph() {
		wp_set_current_user( 0 );

		$post_id = self::factory()->post->create(
			array(
				'post_status' => 'draft',
				'post_title'  => 'Draft Title',
			)
		);

		$_POST['wl_chord_nonce'] = wp_create_nonce( 'wl_chord' );
		$_POST['post_id']        = $post_id;

		$response = $this->call_ajax( 'wl_chord' );

		$this->assertEmpty( $response->entities );
		$this->assertEmpty( $response->relations );
		$this->assertStringNotContainsString( 'Draft Title', $this->_last_response );
	}

	public function test_chord_with_public_root_post_returns_the_graph() {
		wp_set_current_user( 0 );

		$post_id = self::factory()->post->create(
			array(
				'post_status' => 'publish',
				'post_title'  => 'Public Title',
			)
		);

		$_POST['wl_chord_nonce'] = wp_create_nonce( 'wl_chord' );
		$_POST['post_id']        = $post_id;

		$response = $this->call_ajax( 'wl_chord' );

		$this->assertCount( 1, $response->entities );
		$this->assertEquals( 'Public Title', $response->entities[0]->label );
	}

	public function test_chord_with_non_public_root_post_returns_the_graph_to_users_who_can_read_it() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'editor' ) ) );

		$post_id = self::factory()->post->create(
			array(
				'post_status' => 'draft',
				'post_title'  => 'Draft Title',
			)
		);

		$_POST['wl_chord_nonce'] = wp_create_nonce( 'wl_chord' );
		$_POST['post_id']        = $post_id;

		$response = $this->call_ajax( 'wl_chord' );

		$this->assertCount( 1, $response->entities );
		$this->assertEquals( 'Draft Title', $response->entities[0]->label );
	}

	public function test_geomap_single_post_skips_non_public_posts() {
		$draft_id = self::factory()->post->create( array( 'post_status' => 'draft' ) );
		add_post_meta( $draft_id, 'wl_geo_latitude', '45.1' );
		add_post_meta( $draft_id, 'wl_geo_longitude', '9.1' );

		$public_id = self::factory()->post->create( array( 'post_status' => 'publish' ) );
		add_post_meta( $public_id, 'wl_geo_latitude', '45.2' );
		add_post_meta( $public_id, 'wl_geo_longitude', '9.2' );

		$this->assertEmpty( wl_shortcode_geomap_ajax_single_post( $draft_id ) );
		$this->assertCount( 1, wl_shortcode_geomap_ajax_single_post( $public_id ) );
	}

	public function test_related_posts_only_returns_the_fields_used_by_the_ui() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'contributor' ) ) );

		$entity_id = wl_create_post( '', 'entity0', 'An Entity', 'draft', 'entity' );
		Wordlift_Entity_Type_Service::get_instance()->set( $entity_id, 'http://schema.org/Event' );

		$post_1_id = wl_create_post( '', 'post1', 'A post', 'publish' );
		$post_2_id = self::factory()->post->create(
			array(
				'post_status' => 'publish',
				'post_title'  => 'Related post',
			)
		);

		wl_core_add_relation_instance( $post_1_id, WL_WHAT_RELATION, $entity_id );
		wl_core_add_relation_instance( $post_2_id, WL_WHAT_RELATION, $entity_id );

		$_GET['post_id'] = $post_1_id;

		$response = $this->call_ajax( 'wordlift_related_posts', wp_json_encode( array( wl_get_entity_uri( $entity_id ) ) ) );

		$this->assertCount( 1, $response );
		$this->assertEquals( $post_2_id, $response[0]->ID );
		$this->assertEquals( 'Related post', $response[0]->post_title );
		$this->assertEquals(
			array( 'ID', 'post_title', 'post_type', 'thumbnail', 'link', 'permalink' ),
			array_keys( get_object_vars( $response[0] ) )
		);
	}

	public function test_http_api_only_dispatches_allowed_actions() {
		$fired = 0;
		$count = function () use ( &$fired ) {
			$fired ++;
		};
		add_action( 'admin_post_nopriv_other_plugin_action', $count );
		add_action( 'admin_post_other_plugin_action', $count );

		$do_action = new ReflectionMethod( 'Wordlift_Http_Api', 'do_action' );
		$do_action->setAccessible( true );
		$http_api = new Wordlift_Http_Api();

		$do_action->invoke( $http_api, 'other_plugin_action' );
		$this->assertEquals( 0, $fired, 'Actions not in the allowlist must not be dispatched.' );

		$allow = function ( $actions ) {
			$actions[] = 'other_plugin_action';

			return $actions;
		};
		add_filter( 'wl_http_api_allowed_actions', $allow );
		$do_action->invoke( $http_api, 'other_plugin_action' );
		remove_filter( 'wl_http_api_allowed_actions', $allow );

		$this->assertEquals( 1, $fired, 'Actions added via the filter must be dispatched.' );
	}

	public function test_ttl_cache_cleaner_ajax_flush_requires_a_nonce() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );

		$this->setExpectedException( 'WPAjaxDieStopException', '-1' );
		$this->_handleAjax( 'wl_ttl_cache_cleaner__flush' );
	}

	public function test_ttl_cache_cleaner_ajax_cleanup_requires_a_nonce() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );

		$this->setExpectedException( 'WPAjaxDieStopException', '-1' );
		$this->_handleAjax( 'wl_ttl_cache_cleaner__cleanup' );
	}

	public function test_ttl_cache_cleaner_ajax_flush_requires_manage_options() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'editor' ) ) );

		$_POST['_wpnonce'] = wp_create_nonce( 'wl_ttl_cache_cleaner__flush' );

		$response = $this->call_ajax( 'wl_ttl_cache_cleaner__flush' );

		$this->assertFalse( $response->success );
		$this->assertEquals( 'Insufficient permissions.', $response->data );
	}

	public function test_ttl_cache_cleaner_ajax_flush_as_admin() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );

		$_POST['_wpnonce'] = wp_create_nonce( 'wl_ttl_cache_cleaner__flush' );

		$response = $this->call_ajax( 'wl_ttl_cache_cleaner__flush' );

		$this->assertTrue( $response->success );
		$this->assertInternalType( 'int', $response->data );
	}

	public function test_ttl_cache_cleaner_flush_action_does_not_end_other_ajax_requests() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'contributor' ) ) );

		$cache = new Ttl_Cache( 'test-flush' );
		$cache->put( 'key', 'value' );

		// Runs inside an AJAX request (DOING_AJAX is set) for a user without `manage_options`.
		$cleaner = new Ttl_Cache_Cleaner();
		$this->assertInternalType( 'int', $cleaner->flush() );
		$this->assertEmpty( $this->_last_response );
		$this->assertNull( $cache->get( 'key' ) );
	}

	public function test_file_cache_ajax_flush_all_requires_a_nonce() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );

		$this->setExpectedException( 'WPAjaxDieStopException', '-1' );
		$this->_handleAjax( 'wl_file_cache__flush_all' );
	}

	public function test_navigator_requires_a_nonce_on_cache_hits() {
		$post_id = self::factory()->post->create();

		$cache = new Ttl_Cache( 'navigator', 8 * 60 * 60 );
		$cache->put( array( 'request_params' => array( 'post_id' => (string) $post_id ) ), array( 'cached' ) );

		$_GET['post_id'] = (string) $post_id;

		$this->setExpectedException( 'WPAjaxDieStopException', '-1' );
		$this->_handleAjax( 'wl_navigator' );
	}

}
