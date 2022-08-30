<?php

namespace Wordlift\Admin;

/**
 * @since 3.30.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Admin_User_Option {

	const WORDLIFT_ADMIN = 'wl_is_wordlift_admin';

	public static function is_wordlift_admin() {
		return intval( get_user_meta( get_current_user_id(), self::WORDLIFT_ADMIN, true ) ) === 1;
	}

	public function connect_hook() {
		add_action( 'wordlift_user_settings_page', array( $this, 'render_checkbox' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_checkbox' ) );
		add_action( 'personal_options_update', array( $this, 'save_checkbox' ) );
	}

	public function save_checkbox() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! isset( $_POST[ self::WORDLIFT_ADMIN ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			delete_user_meta( get_current_user_id(), self::WORDLIFT_ADMIN );

			return;
		}
		update_user_meta( get_current_user_id(), self::WORDLIFT_ADMIN, 1 );
	}

	public function render_checkbox() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$is_checked   = intval( get_user_meta( get_current_user_id(), self::WORDLIFT_ADMIN, true ) ) === 1;
		$allowed_html = array(
			'tr'    => array(),
			'th'    => array(),
			'td'    => array(),
			'input' => array(
				'type'    => array(),
				'name'    => array(),
				'checked' => array(),
			),
		);
		echo wp_kses( $this->get_checkbox( $is_checked ), $allowed_html );
	}

	public static function get_checkbox( $is_checked ) {
		$checked    = $is_checked ? "checked='checked'" : '';
		$admin_text = __( 'Wordlift Admin', 'wordlift' );

		return "<tr><th>$admin_text</th><td><input type='checkbox' name='wl_is_wordlift_admin' $checked></td></tr>";
	}

}
