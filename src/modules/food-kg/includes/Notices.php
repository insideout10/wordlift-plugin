<?php

namespace Wordlift\Modules\Food_Kg;

class Notices {
	/**
	 * @var array{'type': string, 'html': string}
	 */
	private $notices = array();

	public function register_hooks() {
		add_action( 'admin_notices', array( $this, '__admin_notices' ) );
	}

	/**
	 * @param 'info'|'warning'|'error'|'success' $type
	 * @param string                             $html
	 *
	 * @return void
	 */
	public function queue( $type, $html ) {
		set_transient(
			'_wl_notices',
			array(
				'type' => $type,
				'html' => $html,
			),
			60
		);
	}

	public function __admin_notices() {
		/** @var false|array{'type': string, 'html': string} $notice */
		$notice = get_transient( '_wl_notices' );
		if ( ! $notice ) {
			return;
		}

		$type_e = esc_attr( $notice['type'] );
		?>
		<div class="notice notice-<?php echo esc_attr( $type_e ); ?> is-dismissible">
			<p><?php echo wp_kses( $notice['html'], array( 'a' ) ); ?></p>
		</div>
		<?php
	}

}
