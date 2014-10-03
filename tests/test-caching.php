<?php
/**
 * Test for the Prefixes module.
 */
require_once( 'functions.php' );

/**
 * Class PrefixesTest
 */
class PrefixesTest extends WP_UnitTestCase
{

    /**
     * Set up the test.
     */
    function setUp()
    {
        parent::setUp();

        // Configure WordPress with the test settings.
        wl_configure_wordpress_test();

        // Empty the blog.
        wl_empty_blog();
    }


    /**
     * Test the caching of a URL.
     */
    function test_caching_remote_request()
    {

        $url  = 'http://example.org/';
        $args = array( 'method' => 'GET' );

        // ensure a previous cache file doesn't exists.
        $hash_0 = wl_caching_hash( $url, $args );
        wl_caching_delete( $hash_0 );

        $hash_2 = wl_caching_hash( 'http://example.org/2', $args );
        wl_caching_delete( $hash_2 );

        $hash_3 = wl_caching_hash( $url, array( 'method' => 'POST' ) );
        wl_caching_delete( $hash_3 );

        $response_0 = wl_caching_remote_request( $url, $args );
        $this->assertArrayNotHasKey( 'wl_cached', $response_0 );

        $response_1 = wl_caching_remote_request( $url, $args );
        $this->assertArrayHasKey( 'wl_cached', $response_1 );

        // Force refreshing the cache.
        $response_refresh = wl_caching_remote_request( $url, $args, true );
        $this->assertArrayNotHasKey( 'wl_cached', $response_refresh );

        // Try another request and see that the response is not cached.
        $response_2 = wl_caching_remote_request( 'http://example.org/2', $args );
        $this->assertArrayNotHasKey( 'wl_cached', $response_2 );

        // Try another request same URL but different method and see that the response is not cached.
        $response_3 = wl_caching_remote_request( $url, array( 'method' => 'POST' ) );
        $this->assertArrayNotHasKey( 'wl_cached', $response_3 );

    }

    function test_caching_expired() {

        $url  = 'http://example.org/';
        $args = array( 'method' => 'GET' );

        // ensure a previous cache file doesn't exists.
        $hash_0 = wl_caching_hash( $url, $args );
        wl_caching_delete( $hash_0 );

        // Cache for 5 seconds.
        $response_0 = wl_caching_remote_request( $url, $args, false, 5 );
        $this->assertArrayNotHasKey( 'wl_cached', $response_0 );

        // Check that the first request is still cached.
        $response_1 = wl_caching_remote_request( $url, $args );
        $this->assertArrayHasKey( 'wl_cached', $response_1 );

        // Wait 5 seconds and check that another request is not cached.
        sleep( 5 );
        $response_2 = wl_caching_remote_request( $url, $args );
        $this->assertArrayNotHasKey( 'wl_cached', $response_2 );

    }

}