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
 * @group ajax
 */
class Wordlift_Admin_Country_Select_Element_Ajax_Test extends Wordlift_Ajax_Unit_Test_Case {

	/**
	 * Test that the ajax request will filter the country select options
	 *
	 * @since 3.18.0
	 */
	public function test_get_options_html() {
		// Set $_POST variable: this means we will perform data selection for $entity_1_id
		$_POST['lang']  = 'bg';
		$_POST['value'] = 'bg';

		// Only roles with manage_options permission.
		$user_id   = $this->factory->user->create( array( 'role' => 'administrator' ) );
		$user      = wp_set_current_user( $user_id );	

		echo "USER ID: " . $user_id . "\n";
		$this->assertTrue( current_user_can('manage_options' ), 'User should have manage_options capability.' );



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

		$this->markTestSkipped( 'We include all countries as of 3.34.0' );

		$_POST['lang']  = 'zzz';
		$_POST['value'] = 'uk';

		try {
			$this->_handleAjax( 'wl_update_country_options' );
		} catch ( WPAjaxDieContinueException $e ) {
		}
		$response = json_decode( $this->_last_response, true );

		// Since an unknown language is posted, there would be no countries returned.
		$this->assertEquals( '', $response['data'] );

	}

	/**
	 * Test an invalid value (zzz).
	 *
	 * @since 3.18.0
	 */
	public function test_get_options_html_unknown_value() {
		$_POST['lang']  = 'en';
		$_POST['value'] = 'zzz';

		// Only roles with manage_options permission.
		$user_id   = $this->factory->user->create( array( 'role' => 'administrator' ) );
		$user      = wp_set_current_user( $user_id );	

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
