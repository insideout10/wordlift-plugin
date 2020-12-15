<?php
/**
 * Test the {@link Wordlift_Uri_Service}.
 *
 * @since 3.7.2
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * This class tests the {@link Wordlift_Uri_Service} class.
 *
 * @since 3.7.2
 * @group entity
 */
class Uri_Service_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Uri_Service} to test.
	 *
	 * @since  3.7.2
	 * @access private
	 * @var \Wordlift_Uri_Service $uri_service The {@link Wordlift_Uri_Service} to test.
	 */
	private $uri_service;

	/**
	 * {@inheritdoc}
	 */
	function setUp() {
		parent::setUp();

		// We don't need to check the remote Linked Data store.
		Wordlift_Unit_Test_Case::turn_off_entity_push();;

		$this->uri_service = Wordlift_Uri_Service::get_instance();

	}

	/**
	 * Test the sanitization of a UTF-8 title.
	 *
	 * @see   https://github.com/insideout10/wordlift-plugin/issues/386
	 *
	 * @since 3.7.2
	 */
	function test_utf8_title() {

		// The following title has a UTF-8 character right after the 's'.
		$title = 'Mozarts﻿ Geburtshaus';

		// Check that the encoding is recognized as UTF-8.
		if ( function_exists( 'mb_detect_encoding' ) ) {
			$this->assertEquals( 'UTF-8', mb_detect_encoding( $title ) );
		}

		// Get the sanitized path.
		$path = $this->uri_service->sanitize_path( $title );

		// Check that the encoding is now ASCII.
		if ( function_exists( 'mb_detect_encoding' ) ) {
			$this->assertEquals( 'ASCII', mb_detect_encoding( $path ) );
		}

		// Check that the URI is good.
		$this->assertEquals( 'mozarts__geburtshaus', $path );

	}

	function test_simple() {

		$this->assertEquals( 'david_riccitelli', $this->uri_service->sanitize_path( 'David Riccitelli' ) );
		$this->assertEquals( 'david_luigi_riccitelli', $this->uri_service->sanitize_path( 'David Luigi Riccitelli' ) );

		$this->assertEquals( 'david-riccitelli', $this->uri_service->sanitize_path( 'David Riccitelli', '-' ) );
		$this->assertEquals( 'david-luigi-riccitelli', $this->uri_service->sanitize_path( 'David Luigi Riccitelli', '-' ) );
	}

	function test_with_parentheses() {

		$this->assertEquals( 'david_riccitelli', $this->uri_service->sanitize_path( 'David (Riccitelli)' ) );
		$this->assertEquals( 'david_luigi_riccitelli', $this->uri_service->sanitize_path( 'David (Luigi) Riccitelli' ) );

		$this->assertEquals( 'david-riccitelli', $this->uri_service->sanitize_path( 'David (Riccitelli)', '-' ) );
		$this->assertEquals( 'david-luigi-riccitelli', $this->uri_service->sanitize_path( 'David (Luigi) Riccitelli', '-' ) );
	}

	function test_ekkehard_bohmer() {

		$this->assertEquals( 'ekkehard_bohmer', $this->uri_service->sanitize_path( 'Ekkehard Böhmer' ) );

	}
}
