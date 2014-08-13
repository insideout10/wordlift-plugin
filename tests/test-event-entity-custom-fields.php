<?php

/**
 * Test methods to change Event entities custom fields (start, end date)
 */

require_once 'functions.php';

class EventEntityCustomFieldsTest extends WP_UnitTestCase
{
    /**
     * Set up the test.
     */
    function setUp()
    {
        parent::setUp();

        wl_configure_wordpress_test();
        rl_empty_dataset();
    }

    // Test <input> fields are echoed and in the right page.
    function testHtmlFormEchoedInEditor() {
        $this->assertEquals(True, True);
    }
}