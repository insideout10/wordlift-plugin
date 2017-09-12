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
	public function save_data( $values ) {
		// The autocomplete select may send JSON arrays in input values.
		$merged = array_reduce( (array) $values, function ( $carry, $item ) {
			return array_merge( $carry, mb_split( "\x{2063}", wp_unslash( $item ) ) );
		}, array() );

		parent::save_data( $merged );
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

		// Return an element where the new Autocomplete Select will attach to.
		return '<p>'
			   . esc_html__( 'Type a URL or any text to find entities from the vocabulary and the cloud:', 'wordlift' )
			   . '</p><div id="wl-metabox-field-sameas"></div>';
	}

}
