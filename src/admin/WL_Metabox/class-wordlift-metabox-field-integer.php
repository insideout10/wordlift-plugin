<?php
/**
 * Metaxboxes: Integer Field.
 *
 * This file defines the Wordlift_Metabox_Field_Integer class which displays number field
 * in WordPress' entity posts pages.
 *
 * @since   3.18.0
 * @package Wordlift
 */

/**
 * The Wordlift_Metabox_Field_Integer class extends {@link WL_Metabox_Field} and provides
 * support for integer fields.
 *
 * @since   3.18.0
 * @package Wordlift
 */
class Wordlift_Metabox_Field_Integer extends WL_Metabox_Field {
	/**
	 * @inheritdoc
	 */
	public function html_input( $text ) {
		$esc_meta_name = esc_attr( $this->meta_name );
		$esc_text      = esc_attr( $text );

		$html = <<<EOF
			<div class="wl-input-wrapper">
				<input type="number" id="$esc_meta_name" class="$esc_meta_name" value="$esc_text" name="wl_metaboxes[$esc_meta_name][]" style="width:88%" min="0"/>
				<button class="button wl-remove-input wl-button" type="button">Remove</button>
				<div class="wl-input-notice"></div>
			</div>
EOF;
		return $html;
	}
}
