<?php
/**
 * Metaxboxes: Multiline text Field.
 *
 * This file defines the Wordlift_Metabox_Field_Multiline class which displays a texttarea field
 * in WordPress' entity posts pages.
 *
 * @since   3.14.0
 * @package Wordlift
 */

/**
 * The Wordlift_Metabox_Field_Multiline class extends {@link WL_Metabox_Field} and provides
 * support for time duration fields.
 *
 * @since   3.14.0
 * @package Wordlift
 */
class Wordlift_Metabox_Field_Multiline extends WL_Metabox_Field {

	/**
	 * @inheritdoc
	 */
	public function html_input( $text ) {

		$esc_text      = esc_textarea( $text );
		$esc_meta_name = esc_attr( $this->meta_name );
		$html          = <<<EOF
			<div class="wl-input-wrapper">
				<textarea id="$esc_meta_name" class="$esc_meta_name" name="wl_metaboxes[$esc_meta_name][]" style="width:88%">$esc_text</textarea>
				<button class="button wl-remove-input wl-button" type="button">Remove</button>
			</div>
EOF;

		return $html;
	}
}
