<?php

namespace Wordlift\Modules\Super_Resolution;

class Attachment_Field {

	public function register_hooks() {
		add_filter(
			'attachment_fields_to_edit',
			array( $this, 'attachment_fields_to_edit' ),
			10,
			2
		);
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

	}

	/**
	 * @param $form_fields
	 * @param \WP_Post    $post The WP_Post attachment object.
	 *
	 * @return mixed
	 */
	public function attachment_fields_to_edit( $form_fields, $post ) {
		// We want to show this button only on the attachment sidebar, which is loaded via ajax.
		if ( ! wp_doing_ajax() ) {
			return $form_fields;
		}

		// Add your custom HTML code here
		$form_fields['wl_super_resolution'] = array(
			'label'  => __( 'WordLift Image Upscale', 'wordlift' ),
			'input'  => 'custom',
			'html'   => '',
			'custom' => $this->get_html( $post->ID ),
		);

		return $form_fields;
	}

	public function admin_enqueue_scripts() {
		/**
		 * I only need these files when the media library is rendered.
		 */
		if ( ! did_action( 'wp_enqueue_media' ) ) {
			return;
		}

		wp_enqueue_style( 'wl-super-resolution', WL_DIR_URL . 'modules/super-resolution/css/super-resolution.css', array( 'thickbox' ), WORDLIFT_VERSION );
		wp_enqueue_script( WL_ANGULAR_APP_SCRIPT_HANDLE );

	}

	private function get_html( $attachment_id ) {
		$base_url                      = WL_ANGULAR_APP_URL . "#(dialog:dialogs/super-resolution/$attachment_id/upscale)";
		$is_smaller_than_required_size = $this->is_smaller_than_the_required_width( $attachment_id );

		return '<div class="wl-super-resolution-container">' . ( $is_smaller_than_required_size
				? sprintf(
					'<span class="wl-warning-icon"></span><div><strong>%s</strong>: %s</div>',
					__( 'Image too small', 'wordlift' ),
					__( 'upscale to boost web traffic', 'wordlift' )
				)
				: sprintf(
					'<span class="wl-success-icon"></span><div><strong>%s</strong>: %s</div>',
					__( 'Size is good', 'wordlift' ),
					__( 'no recommended actions', 'wordlift' )
				) ) .
			   '</div><button ' . ( $is_smaller_than_required_size ? '' : 'disabled="disabled"' ) . ' onclick="wlOpenFullscreenIframe(\'' . $base_url . '\')">Upscale Image</button>';
	}

	private function is_smaller_than_the_required_width( $attachment_id ) {
		// Get the attachment metadata
		$attachment_metadata = wp_get_attachment_metadata( $attachment_id );

		// Get the image dimensions
		return isset( $attachment_metadata['width'] ) && is_numeric( $attachment_metadata['width'] ) && $attachment_metadata['width'] < 1200;
	}

}
