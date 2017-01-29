<?php
require_once 'functions.php';

class UserTest extends Wordlift_Unit_Test_Case {

	/**
	 * Set up the test.
	 */
	function setUp() {
		parent::setUp();

		// We don't need to check the remote Linked Data store.
		Wordlift_Unit_Test_Case::turn_off_entity_push();;

		// Configure WordPress with the test settings.
		wl_configure_wordpress_test();

		// Empty the blog.
		wl_empty_blog();

	}

	function testUserWithFirstAndLastName() {

		$user_id = wp_insert_user( array(
			'user_login' => 'lorem_ipsum',
			'user_pass'  => 'tmppass',
			'first_name' => 'Lorem',
			'last_name'  => 'Ipsum',
		) );

		$this->assertEquals(
			$this->getURI( 'lorem_ipsum' ),
			Wordlift_User_Service::get_instance()->get_uri( $user_id )
		);

	}

	function testUserWithoutFirstAndLastName() {

		$user_id = wp_insert_user( array(
			'user_login' => 'lorem_ipsum',
			'user_pass'  => 'tmppass',
		) );

		$this->assertEquals(
			$this->getURI( 'lorem_ipsum' ),
			Wordlift_User_Service::get_instance()->get_uri( $user_id )
		);

		$update_user_id = wp_update_user( array(
			'ID'         => $user_id,
			'user_login' => 'lorem_ipsum',
			'user_pass'  => 'tmppass',
			'first_name' => 'Lorem',
			'last_name'  => 'Ipsum',
		) );

		$this->assertEquals( $update_user_id, $user_id );

		$this->assertEquals(
			$this->getURI( 'lorem_ipsum' ),
			Wordlift_User_Service::get_instance()->get_uri( $user_id )
		);
	}

	function testTwoUsersWithTheSameName() {

		$user_id_1 = wp_insert_user( array(
			'user_login' => 'mario_rossi',
			'user_pass'  => 'tmppass',
			'first_name' => 'Mario',
			'last_name'  => 'Rossi',
		) );

		$this->assertEquals(
			$this->getURI( 'mario_rossi' ),
			Wordlift_User_Service::get_instance()->get_uri( $user_id_1 )
		);

		$user_id_2 = wp_insert_user( array(
			'user_login' => 'mario_rossi_1',
			'user_pass'  => 'tmppass',
			'first_name' => 'Mario',
			'last_name'  => 'Rossi',
		) );

		$this->assertEquals(
			$this->getURI( 'mario_rossi_1' ),
			Wordlift_User_Service::get_instance()->get_uri( $user_id_2 )
		);
	}

	/**
	 * Get an URI for testing.
	 *
	 * @param $id
	 *
	 * @return string
	 */
	function getURI( $id ) {

		return wl_configuration_get_redlink_dataset_uri() . "/user/$id";

	}

}