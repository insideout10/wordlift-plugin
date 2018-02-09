<?php
/**
 * Tests: Country Select Element Test.
 *
 * Test the {@link Wordlift_Admin_Country_Select_Element} class.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the {@link Wordlift_Admin_Country_Select_Element_Ajax_Test} class.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */
class Wordlift_Admin_Country_Select_Element_Ajax_Test extends Wordlift_Ajax_Unit_Test_Case {

	/**
	 * @inheritdoc
	 */
	function setUp() {
		parent::setUp();
	}

	/**
	 * Test that the ajax request will filter the country select options
	 *
	 * @since 3.18.0
	 */
	public function test_get_options_html() {
		// Set $_POST variable: this means we will perform data selection for $entity_1_id
		$_POST['lang']  = 'en';
		$_POST['value'] = 'bg';

		try {
			$this->_handleAjax( 'wl_update_country_options' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response, true );

		$matches = array();
		preg_match_all( '/<option\s+value="bg"\s+selected=\'selected\'/', $response['data'], $matches );

		$this->assertCount( 1, $matches[0] );
	}

	/**
	 * Test an invalid lang (zzz).
	 *
	 * @since 3.18.0
	 */
	public function test_get_options_html_unknown_lang() {
		$_POST['lang']  = 'zzz';
		$_POST['value'] = 'us';

		try {
			$this->_handleAjax( 'wl_update_country_options' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response, true );

		$matches = array();
		preg_match_all( '/<option\s+value="us"\s+selected=\'selected\'/', $response['data'], $matches );

		$this->assertCount( 1, $matches[0] );

	}

	/**
	 * Test an invalid value (zzz).
	 *
	 * @since 3.18.0
	 */
	public function test_get_options_html_unknown_value() {
		$_POST['lang']  = 'en';
		$_POST['value'] = 'zzz';

		try {
			$this->_handleAjax( 'wl_update_country_options' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response, true );

		$matches = array();
		preg_match_all( '/<option\s+value="uk"\s+selected=\'selected\'/', $response['data'], $matches );

		$this->assertCount( 1, $matches[0] );

	}

}
