<?php
/**
 * Metaboxes: sameAs Field Metabox.
 *
 * @since      3.0.0
 * @package    Wordlift
 * @subpackage Wordlift/admin/WL_Metabox
 */

/**
 * Define the {@link WL_Metabox_Field_sameas} class.
 *
 * @since      3.0.0
 * @package    Wordlift
 * @subpackage Wordlift/admin/WL_Metabox
 */
class WL_Metabox_Field_sameas extends WL_Metabox_Field {

	/**
	 * @inheritdoc
	 */
	public function __construct( $args ) {
		parent::__construct( $args['sameas'] );
	}

	/**
	 * @inheritdoc
	 */
	public function sanitize_data_filter( $value ) {

		// Call our sanitizer helper.
		return Wordlift_Sanitizer::sanitize_url( $value );
	}

	/**
	 * @inheritdoc
	 */
	protected function get_add_button_html( $count ) {

		// Return an empty string.
		return '<div id="wl-metabox-field-sameas">loading...</div>';
	}

}
