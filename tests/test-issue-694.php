<?php

/**
 * Created by PhpStorm.
 * User: david
 * Date: 10.11.17
 * Time: 15:44
 */
class Wordlift_Issue_694 extends Wordlift_Unit_Test_Case {

	public function test() {

		$post_id = $this->factory->post->create( array(
			'post_type' => 'entity',
		) );

		$user = new WP_User( $this->factory->user->create( array( 'role' => 'administrator' ) ) );
		wp_set_current_user( $user->ID );

		$current_user_can_read_post = current_user_can( 'read_post', $post_id );

		$this->assertTrue( $current_user_can_read_post );

	}

}
