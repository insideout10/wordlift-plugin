<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 19.02.18
 * Time: 15:41
 */

class Wordlift_Install_1_0_0 implements Wordlift_Install {

	function get_version() {
		return '1.0.0';
	}

	function install() {

		wp_insert_post( array(
			'post_status' => 'draft',
			'post_title'  => 'Hello WordLift!',
		) );

	}

}