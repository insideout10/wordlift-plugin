<?php
/**
 * This file provides a Wordlift_Ajax_Unit_Test_Case abstract class to support
 * WP's AJAX testing while configuring Wordlift.
 *
 * @since   3.8.0
 * @package Wordlift
 */

require_once( 'functions.php' );

/**
 * Define the {@link Wordlift_Ajax_Unit_Test_Case} class.
 *
 * @since 3.8.0
 */
abstract class Wordlift_Ajax_Unit_Test_Case extends WP_Ajax_UnitTestCase {

	function setUp() {
		parent::setUp();

		// Configure WordPress with the test settings.
		wl_configure_wordpress_test();

	}

}
