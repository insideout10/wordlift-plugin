<?php
require_once 'functions.php';

class UserTest extends WP_UnitTestCase
{

    /**
     * Set up the test.
     */
    function setUp()
    {
        parent::setUp();

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
            'last_name'  => 'Ipsum'
        ) );

        $this->assertEquals(
            $this->getURI( 'Lorem_Ipsum' ),
            wl_get_user_uri( $user_id )
        );

    }

    function testUserWithoutFirstAndLastName() {

        $user_id = wp_insert_user( array(
            'user_login' => 'lorem_ipsum',
            'user_pass'  => 'tmppass'
        ) );

        $this->assertEquals(
            $this->getURI( $user_id ),
            wl_get_user_uri( $user_id )
        );

        $update_user_id = wp_update_user( array(
            'ID'         => $user_id,
            'user_login' => 'lorem_ipsum',
            'user_pass'  => 'tmppass',
            'first_name' => 'Lorem',
            'last_name'  => 'Ipsum'
        ) );

        $this->assertEquals( $update_user_id, $user_id );

        $this->assertEquals(
            $this->getURI( $user_id ),
            wl_get_user_uri( $user_id )
        );
    }

    function testTwoUsersWithTheSameName() {

        $user_id_1 = wp_insert_user( array(
            'user_login' => 'mario_rossi',
            'user_pass'  => 'tmppass',
            'first_name' => 'Mario',
            'last_name'  => 'Rossi'
        ) );

        $this->assertEquals(
            $this->getURI( 'Mario_Rossi' ),
            wl_get_user_uri( $user_id_1 )
        );

        $user_id_2 = wp_insert_user( array(
            'user_login' => 'mario_rossi_1',
            'user_pass'  => 'tmppass',
            'first_name' => 'Mario',
            'last_name'  => 'Rossi'
        ) );

        $this->assertEquals(
            $this->getURI( 'Mario_Rossi_1' ),
            wl_get_user_uri( $user_id_2 )
        );
    }

    /**
     * Get an URI for testing.
     * @param $id
     * @return string
     */
    function getURI( $id ) {

        return sprintf(
            'http://data.redlink.io/%s/%s/%s/%s',
            wl_configuration_get_redlink_user_id(),
            wl_configuration_get_redlink_dataset_name(),
            'user',
            $id
        );
    }

}