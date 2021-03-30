<?php

namespace Wordlift\Admin;
/**
 * @since 3.30.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Admin_User_Option {

	const WORDLIFT_ADMIN = 'wl_is_wordlift_admin';

	public function connect_hook() {
		add_action( 'edit_user_profile', array( $this, 'render_checkbox' ) );
	}

	public function render_checkbox() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$is_checked = intval( get_user_meta( get_current_user_id(), self::WORDLIFT_ADMIN, true ) ) === 1;
		echo $this->get_checkbox( $is_checked );
	}

	public static function get_checkbox( $is_checked ) {
		$checked = $is_checked ? 'checked' : '';

		return "<input type='checkbox' name='wl_is_wordlift_admin' checked='$checked'> Admin";
	}

}