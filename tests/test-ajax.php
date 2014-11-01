<?php
require_once 'functions.php';

/**
 * Testing ajax response class
 */
class AjaxTest extends WP_UnitTestCase
{

    /**
     * Saved error reporting level
     * @var int
     */
    protected $_error_level = 0;

    /**
     * Set up the test fixture.
     * Override wp_die(), pretend to be ajax, and suppres E_WARNINGs
     */
    public function setUp()
    {

        parent::setUp();

        // Suppress warnings from "Cannot modify header information - headers already sent by"
        $this->_error_level = error_reporting();
        error_reporting( $this->_error_level & ~E_WARNING );

        wl_configure_wordpress_test();

        add_filter('wp_die_ajax_handler', array($this, 'getDieHandler'), 1, 1);
        if (!defined('DOING_AJAX'))
            define('DOING_AJAX', true);

        // Disable the *wl_write_log* as it can create issues with AJAX tests.
        add_filter( 'wl_write_log_handler', array( $this, 'get_write_log_handler' ), 1, 1 );

        wl_empty_blog();

    }

    /**
     * Tear down the test fixture.
     * Remove the wp_die() override, restore error reporting
     */
    public function tearDown()
    {
        parent::tearDown();

        remove_filter('wp_die_ajax_handler', array($this, 'getDieHandler'), 1, 1);
        remove_filter( 'wl_write_log_handler', array( $this, 'get_write_log_handler'), 1, 1 );
        error_reporting($this->_error_level);
    }


    public function get_write_log_handler() {

        return array( $this, 'write_log_handler' );
    }

    public function write_log_handler( $log ) {

    }

    /**
     * Return our callback handler
     * @return callback
     */
    public function getDieHandler()
    {
        return array($this, 'dieHandler');
    }

    /**
     * Handler for wp_die()
     * Don't die, just continue on.
     * @param string $message
     */
    public function dieHandler($message)
    {
    }

    public function test_shortcode_chord_ajax()
    {

        // TODO: fix content-type tests.
        $this->markTestSkipped('Content Type tests are failing, needs fix');

        if (!function_exists('xdebug_get_headers')) {
            $this->markTestSkipped('xdebug is required for this test');
        }

        $post_id = wl_create_post('This is Post 1', 'post-1', 'Post 1', 'publish');

        $entity_1_id = wl_create_post('This is Entity 1', 'entity-1', 'Entity 1', 'publish', 'entity');
        wl_set_entity_main_type($entity_1_id, 'http://schema.org/Thing');

        $entity_2_id = wl_create_post('This is Entity 2', 'entity-2', 'Entity 2', 'publish', 'entity');
        wl_set_entity_main_type($entity_2_id, 'http://schema.org/Thing');

        wl_add_referenced_entities($post_id, array($entity_1_id, $entity_2_id));

        $_REQUEST['post_id'] = $post_id;
        $_REQUEST['depth'] = 3;

        ob_start();
        wl_shortcode_chord_ajax();
        $headers = xdebug_get_headers();
        ob_end_clean();

        wl_write_log( $headers );
        $this->assertTrue( in_array( 'Content-Type: application/json', $headers ) );
    }

    public function test_shortcode_timeline_ajax()
    {

        // TODO: fix content-type tests.
        $this->markTestSkipped('Content Type tests are failing, needs fix');

        if (!function_exists('xdebug_get_headers')) {
            $this->markTestSkipped('xdebug is required for this test');
        }

        $post_id = wl_create_post('This is Post 1', 'post-1', 'Post 1', 'publish');

        $entity_1_id = wl_create_post('This is Entity 1', 'entity-1', 'Entity 1', 'publish', 'entity');
        wl_set_entity_main_type($entity_1_id, 'http://schema.org/Thing');
        add_post_meta($entity_1_id, WL_CUSTOM_FIELD_CAL_DATE_START, '2014-01-02', true);
        add_post_meta($entity_1_id, WL_CUSTOM_FIELD_CAL_DATE_END, '2014-01-03', true);

        $entity_2_id = wl_create_post('This is Entity 2', 'entity-2', 'Entity 2', 'publish', 'entity');
        wl_set_entity_main_type($entity_2_id, 'http://schema.org/Thing');
        add_post_meta($entity_2_id, WL_CUSTOM_FIELD_CAL_DATE_START, '2014-01-03', true);
        add_post_meta($entity_2_id, WL_CUSTOM_FIELD_CAL_DATE_END, '2014-01-04', true);

        wl_add_referenced_entities($post_id, array($entity_1_id, $entity_2_id));

        $_REQUEST['post_id'] = $post_id;

        wl_shortcode_timeline_ajax();
        $headers = xdebug_get_headers();

        $this->assertTrue(in_array('Content-Type: application/json', $headers));
    }

    public function test_shortcode_geomap_ajax()
    {

        // TODO: fix content-type tests.
        $this->markTestSkipped('Content Type tests are failing, needs fix');

        if (!function_exists('xdebug_get_headers')) {
            $this->markTestSkipped('xdebug is required for this test');
        }

        $post_id = wl_create_post('This is Post 1', 'post-1', 'Post 1', 'publish');

        $entity_1_id = wl_create_post( "Entity 1 Text", 'entity-1', "Entity 1 Title", 'publish', 'entity' );
        wl_set_entity_main_type( $entity_1_id, 'http://schema.org/Place' );
        add_post_meta( $entity_1_id, WL_CUSTOM_FIELD_GEO_LATITUDE, 40.12, true );
        add_post_meta( $entity_1_id, WL_CUSTOM_FIELD_GEO_LONGITUDE, 72.3, true );

        $entity_2_id = wl_create_post( "Entity 2 Text", 'entity-2', "Entity 2 Title", 'publish', 'entity' );
        wl_set_entity_main_type( $entity_2_id, 'http://schema.org/Place' );
        add_post_meta( $entity_2_id, WL_CUSTOM_FIELD_GEO_LATITUDE, 41.20, true );
        add_post_meta( $entity_2_id, WL_CUSTOM_FIELD_GEO_LONGITUDE, 78.2, true );

        wl_add_referenced_entities($post_id, array($entity_1_id, $entity_2_id));

        $_REQUEST['post_id'] = $post_id;

        wl_shortcode_geomap_ajax();
        $headers = xdebug_get_headers();

        $this->assertTrue(in_array('Content-Type: application/json', $headers));
    }
}