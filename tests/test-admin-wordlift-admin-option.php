<?php
/**
 * @since 3.30.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

use Wordlift\Admin\Admin_User_Option;

/**
 * Class Wordlift_Admin_Option_Test
 */
class Wordlift_Admin_Option_Test extends Wordlift_Unit_Test_Case {

	public function setUp() {
		parent::setUp();
		global $wp_filter;
		$wp_filter = array();
		run_wordlift();

		$wordlift_admin_checkbox = new Admin_User_Option();
		$wordlift_admin_checkbox->connect_hook();

	}

	public function test_should_render_admin_checkbox_when_user_is_admin() {
		$user_id = $this->factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		$checkbox_contents = $this->get_contents_for_checkbox_action();
		$this->assertNotEmpty( $checkbox_contents, "Should have checkbox contents" );
	}


	public function test_should_not_render_admin_checkbox_when_user_is_not_admin() {
		$user_id = $this->factory()->user->create( array( 'role' => 'editor' ) );
		wp_set_current_user( $user_id );
		$checkbox_contents = $this->get_contents_for_checkbox_action();
		$this->assertEmpty( $checkbox_contents, "Should  not have checkbox contents" );
	}

	public function test_should_save_the_option_when_the_user_is_admin() {
		$user_id = $this->factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		$_POST[ Admin_User_Option::WORDLIFT_ADMIN ] = true;
		do_action( 'edit_user_profile_update' );
		$this->assertTrue( Admin_User_Option::is_wordlift_admin() );
	}

	public function test_should_not_save_the_option_when_the_user_is_not_admin() {
		$user_id = $this->factory()->user->create( array( 'role' => 'editor' ) );
		wp_set_current_user( $user_id );
		$_POST[ Admin_User_Option::WORDLIFT_ADMIN ] = true;
		do_action( 'edit_user_profile_update' );
		$this->assertFalse( Admin_User_Option::is_wordlift_admin() );
	}

	public function test_should_save_the_option_when_the_user_is_admin_personal_options_update() {
		$user_id = $this->factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		$_POST[ Admin_User_Option::WORDLIFT_ADMIN ] = true;
		do_action( 'personal_options_update' );
		$this->assertTrue( Admin_User_Option::is_wordlift_admin() );
	}

	public function test_should_remove_the_option_when_the_user_is_admin_personal_options_update() {
		$user_id = $this->factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		update_user_meta( get_current_user_id(), Admin_User_Option::WORDLIFT_ADMIN, 1 );
		unset( $_POST[ Admin_User_Option::WORDLIFT_ADMIN ] );
		do_action( 'personal_options_update' );
		$this->assertFalse( Admin_User_Option::is_wordlift_admin() );
	}


	public function test_should_not_save_the_option_when_the_user_is_not_admin_personal_options_update() {
		$user_id = $this->factory()->user->create( array( 'role' => 'editor' ) );
		wp_set_current_user( $user_id );
		$_POST[ Admin_User_Option::WORDLIFT_ADMIN ] = true;
		do_action( 'personal_options_update' );
		$this->assertFalse( Admin_User_Option::is_wordlift_admin() );
	}

	/**
	 * @return false|string
	 */
	private function get_contents_for_checkbox_action() {
		ob_start();
		do_action( 'wordlift_user_settings_page' );
		$checkbox_contents = ob_get_contents();
		ob_end_clean();

		return $checkbox_contents;
	}


}