<?php
/**
 * Tests: Schemaorg Class Service Test.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the Wordlift_Schemaorg_Class_Service_Test class.
 *
 * @since 3.20.0
 * @group ajax
 */
class Wordlift_Schemaorg_Class_Service_Test extends Wordlift_Ajax_Unit_Test_Case {

	public function test_schemaorg_class() {

		try {
			$this->_handleAjax( 'wl_schemaorg_class' );
		} catch ( WPAjaxDieContinueException $ignored ) {
			// This is expected.
		}

		$json = json_decode( $this->_last_response, true );

		$this->assertArrayHasKey( 'success', $json, 'The json must contain the `success` key.' );
		$this->assertTrue( $json['success'], '`success` must be `true`.' );
		$this->assertArrayHasKey( 'data', $json, 'The json must contain the `success` key.' );
		$this->assertArrayHasKey( 'schemaClasses', $json['data'], '`data` must contain `schemaClasses`.' );
		$this->assertNotEmpty( $json['data']['schemaClasses'], '`schemaClasses` must be non empty.' );

	}

}
