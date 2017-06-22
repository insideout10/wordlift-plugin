<?php
/**
 * Metaxboxes: Multiline text Field.
 *
 * This file defines the WL_Metabox_Field_multiline class which displays a texttarea field
 * in WordPress' entity posts pages.
 *
 * @since   3.14.0
 * @package Wordlift
 */

/**
 * The WL_Metabox_Field_multiline class extends {@link WL_Metabox_Field} and provides
 * support for time duration fields.
 *
 * @since   3.14.0
 * @package Wordlift
 */
class WL_Metabox_Field_multiline extends WL_Metabox_Field {

	/**
	 * {@inheritdoc}
	 */
	public function __construct( $args ) {
		parent::__construct( $args );
	}

	/**
	 * @param mixed $duration
	 *
	 * @return string HTML for the duration input element
	 */
	public function html_input( $text ) {

		$esc_text = esc_textarea( $text );
		$esc_meta_name = esc_attr( $this->meta_name );
		$html = <<<EOF
			<div class="wl-input-wrapper">
				<textarea id="$esc_meta_name" class="$esc_meta_name" name="wl_metaboxes[$esc_meta_name][]" style="width:88%">$esc_text</textarea>
				<button class="button wl-remove-input wl-button" type="button" style="width:10 % ">Remove</button>
			</div>
EOF;

		return $html;
	}
}
