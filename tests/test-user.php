<?php
require_once 'functions.php';

class UserTest extends WP_UnitTestCase
{

    /**
     * Set up the test.
     */
    function setUp()
    {
        parent::setUp();

        // Configure WordPress with the test settings.
        wl_configure_wordpress_test();

    }

    function testSimple() {


        $this->assertNull( wl_get_user_uri( 0 ) );
        $this->assertNotNull( wl_get_user_uri( 1 ) );

    }

}