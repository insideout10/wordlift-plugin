<?php
require_once( 'functions.php' );

/**
 * Test the {@link Wordlift_User_Service}.
 *
 * @since 3.1.7
 */
class UserServiceTest extends Wordlift_Unit_Test_Case {

	/**
	 * The Log service.
	 *
	 * @since  3.1.7
	 * @access private
	 * @var \Wordlift_Log_Service $log_service The Log service.
	 */
	private $log_service;

	/**
	 * Set up the test.
	 */
	function setUp() {
		parent::setUp();

		// We don't need to check the remote Linked Data store.
		Wordlift_Unit_Test_Case::turn_off_entity_push();;

		$this->log_service = Wordlift_Log_Service::get_logger( 'UserServiceTest' );

		wl_configure_wordpress_test();
		// wl_empty_blog();

	}

	/**
	 * Test the User service.
	 *
	 * @since 3.1.7
	 */
	function test_user_service() {

		$users_ids = get_users( array( 'fields' => 'id' ) );

		foreach ( $users_ids as $user_id ) {

			// Get the URI.
			$user_uri = Wordlift_User_Service::get_instance()->get_uri( $user_id );

			// Check that the URI is valid.
			$this->assertNotEmpty( $user_uri );
			$this->assertTrue( false !== $user_uri );

			// Try to change the nicename and check that the URI doesn't change.
			wp_update_user( array(
				'ID'            => $user_id,
				'user_nicename' => uniqid( 'nicename-' ),
			) );

			// Get the URI again and check that it didn't change.
			$user_uri_1 = Wordlift_User_Service::get_instance()->get_uri( $user_id );

			$this->assertEquals( $user_uri, $user_uri_1 );

		}

	}

}
