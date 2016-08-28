<?php

require_once( 'functions.php' );

/**
 */
class Wordlift_Unit_Test_Case extends WP_UnitTestCase {

	function setUp() {
		parent::setUp();

		// Configure WordPress with the test settings.
		wl_configure_wordpress_test();

	}

}