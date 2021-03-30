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
		ob_start();
		do_action( 'edit_user_profile' );
		$checkbox_contents = ob_get_contents();
		ob_end_clean();
		$this->assertNotEmpty( $checkbox_contents, "Should have checkbox contents" );

	}


}