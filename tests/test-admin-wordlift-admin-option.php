<?php
/**
 * @since 3.30.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * Class Wordlift_Admin_Option_Test
 */
class Wordlift_Admin_Option_Test extends Wordlift_Unit_Test_Case {

	public function setUp() {
		parent::setUp();
		global $wp_filter;
		$wp_filter = array();
		run_wordlift();
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