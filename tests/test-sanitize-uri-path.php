<?php
require_once 'functions.php';

class SanitizeUriPathTest extends WP_UnitTestCase
{

    /**
     * Set up the test.
     */
    function setUp()
    {
        parent::setUp();

        wl_configure_wordpress_test();
    }

    function testSimple() {

        $this->assertEquals( 'david_riccitelli', wl_sanitize_uri_path( 'David Riccitelli' ) );
        $this->assertEquals( 'david_luigi_riccitelli', wl_sanitize_uri_path( 'David Luigi Riccitelli' ) );

        $this->assertEquals( 'david-riccitelli', wl_sanitize_uri_path( 'David Riccitelli', '-' ) );
        $this->assertEquals( 'david-luigi-riccitelli', wl_sanitize_uri_path( 'David Luigi Riccitelli', '-' ) );
    }

    function testWithParentheses() {

        $this->assertEquals( 'david_riccitelli', wl_sanitize_uri_path( 'David (Riccitelli)' ) );
        $this->assertEquals( 'david_luigi_riccitelli', wl_sanitize_uri_path( 'David (Luigi) Riccitelli' ) );

        $this->assertEquals( 'david-riccitelli', wl_sanitize_uri_path( 'David (Riccitelli)', '-' ) );
        $this->assertEquals( 'david-luigi-riccitelli', wl_sanitize_uri_path( 'David (Luigi) Riccitelli', '-' ) );
    }

    function testEkkehardBohmer() {

        $this->assertEquals( 'ekkehard_bohmer', wl_sanitize_uri_path( 'Ekkehard Böhmer' ) );

    }

}