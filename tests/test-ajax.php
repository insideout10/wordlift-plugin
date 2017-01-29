<?php
require_once 'functions.php';

/**
 * Testing ajax response class
 */
class AjaxTest extends Wordlift_Ajax_Unit_Test_Case {

	/**
	 * Saved error reporting level
	 * @var int
	 */
	protected $_error_level = 0;

	/**
	 * Set up the test fixture.
	 * Override wp_die(), pretend to be ajax, and suppres E_WARNINGs
	 */
	public function setUp() {
		parent::setUp();

		wl_empty_blog();

	}

	public function test_shortcode_chord_ajax() {

		// TODO: fix content-type tests.
		$this->markTestSkipped( 'Content Type tests are failing, needs fix' );

		if ( ! function_exists( 'xdebug_get_headers' ) ) {
			$this->markTestSkipped( 'xdebug is required for this test' );
		}

		$post_id = wl_create_post( 'This is Post 1', 'post-1', 'Post 1', 'publish' );

		$entity_1_id = wl_create_post( 'This is Entity 1', 'entity-1', 'Entity 1', 'publish', 'entity' );
		wl_set_entity_main_type( $entity_1_id, 'http://schema.org/Thing' );

		$entity_2_id = wl_create_post( 'This is Entity 2', 'entity-2', 'Entity 2', 'publish', 'entity' );
		wl_set_entity_main_type( $entity_2_id, 'http://schema.org/Thing' );

		wl_core_add_relation_instances( $post_id, WL_WHAT_RELATION, array(
			$entity_1_id,
			$entity_2_id,
		) );

		$_REQUEST['post_id'] = $post_id;
		$_REQUEST['depth']   = 3;

		ob_start();
		wl_shortcode_chord_ajax();
		$headers = xdebug_get_headers();
		ob_end_clean();

		wl_write_log( $headers );
		$this->assertTrue( in_array( 'Content-Type: application/json', $headers ) );
	}

	public function test_shortcode_timeline_ajax() {

		// TODO: fix content-type tests.
		$this->markTestSkipped( 'Content Type tests are failing, needs fix' );

		if ( ! function_exists( 'xdebug_get_headers' ) ) {
			$this->markTestSkipped( 'xdebug is required for this test' );
		}

		$post_id = wl_create_post( 'This is Post 1', 'post-1', 'Post 1', 'publish' );

		$entity_1_id = wl_create_post( 'This is Entity 1', 'entity-1', 'Entity 1', 'publish', 'entity' );
		wl_set_entity_main_type( $entity_1_id, 'http://schema.org/Thing' );
		add_post_meta( $entity_1_id, Wordlift_Schema_Service::FIELD_DATE_START, '2014-01-02', true );
		add_post_meta( $entity_1_id, Wordlift_Schema_Service::FIELD_DATE_END, '2014-01-03', true );

		$entity_2_id = wl_create_post( 'This is Entity 2', 'entity-2', 'Entity 2', 'publish', 'entity' );
		wl_set_entity_main_type( $entity_2_id, 'http://schema.org/Thing' );
		add_post_meta( $entity_2_id, Wordlift_Schema_Service::FIELD_DATE_START, '2014-01-03', true );
		add_post_meta( $entity_2_id, Wordlift_Schema_Service::FIELD_DATE_END, '2014-01-04', true );

		wl_core_add_relation_instances( $post_id, WL_WHAT_RELATION, array(
			$entity_1_id,
			$entity_2_id,
		) );

		$_REQUEST['post_id'] = $post_id;

		Wordlift_Timeline_Service::get_instance()->ajax_timeline();
		$headers = xdebug_get_headers();

		$this->assertTrue( in_array( 'Content-Type: application/json', $headers ) );
	}

	public function test_shortcode_geomap_ajax() {

		// TODO: fix content-type tests.
		$this->markTestSkipped( 'Content Type tests are failing, needs fix' );

		if ( ! function_exists( 'xdebug_get_headers' ) ) {
			$this->markTestSkipped( 'xdebug is required for this test' );
		}

		$post_id = wl_create_post( 'This is Post 1', 'post-1', 'Post 1', 'publish' );

		$entity_1_id = wl_create_post( "Entity 1 Text", 'entity-1', "Entity 1 Title", 'publish', 'entity' );
		wl_set_entity_main_type( $entity_1_id, 'http://schema.org/Place' );
		add_post_meta( $entity_1_id, Wordlift_Schema_Service::FIELD_GEO_LATITUDE, 40.12, true );
		add_post_meta( $entity_1_id, Wordlift_Schema_Service::FIELD_GEO_LONGITUDE, 72.3, true );

		$entity_2_id = wl_create_post( "Entity 2 Text", 'entity-2', "Entity 2 Title", 'publish', 'entity' );
		wl_set_entity_main_type( $entity_2_id, 'http://schema.org/Place' );
		add_post_meta( $entity_2_id, Wordlift_Schema_Service::FIELD_GEO_LATITUDE, 41.20, true );
		add_post_meta( $entity_2_id, Wordlift_Schema_Service::FIELD_GEO_LONGITUDE, 78.2, true );

		wl_core_add_relation_instances( $post_id, WL_WHAT_RELATION, array(
			$entity_1_id,
			$entity_2_id,
		) );

		$_REQUEST['post_id'] = $post_id;

		wl_shortcode_geomap_ajax();
		$headers = xdebug_get_headers();

		$this->assertTrue( in_array( 'Content-Type: application/json', $headers ) );
	}

}
