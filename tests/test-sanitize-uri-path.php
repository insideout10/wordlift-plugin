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

        $this->assertEquals( 'David_Riccitelli', wl_sanitize_uri_path( 'David Riccitelli' ) );
        $this->assertEquals( 'David_Luigi_Riccitelli', wl_sanitize_uri_path( 'David Luigi Riccitelli' ) );

        $this->assertEquals( 'David-Riccitelli', wl_sanitize_uri_path( 'David Riccitelli', '-' ) );
        $this->assertEquals( 'David-Luigi-Riccitelli', wl_sanitize_uri_path( 'David Luigi Riccitelli', '-' ) );
    }

    function testWithParentheses() {

        $this->assertEquals( 'David_(Riccitelli)', wl_sanitize_uri_path( 'David (Riccitelli)' ) );
        $this->assertEquals( 'David_(Luigi)_Riccitelli', wl_sanitize_uri_path( 'David (Luigi) Riccitelli' ) );

        $this->assertEquals( 'David-(Riccitelli)', wl_sanitize_uri_path( 'David (Riccitelli)', '-' ) );
        $this->assertEquals( 'David-(Luigi)-Riccitelli', wl_sanitize_uri_path( 'David (Luigi) Riccitelli', '-' ) );
    }

}