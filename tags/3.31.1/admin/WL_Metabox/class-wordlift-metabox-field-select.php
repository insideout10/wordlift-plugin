<?php
/**
 * Metaxboxes: Integer Field.
 *
 * This file defines the Wordlift_Metabox_Field_Select class which displays select field
 * in WordPress' entity posts pages.
 *
 * @since   3.18.0
 * @package Wordlift
 */

/**
 * The Wordlift_Metabox_Field_Select class extends {@link WL_Metabox_Field} and provides
 * support for select fields.
 *
 * @since   3.18.0
 * @package Wordlift
 */
class Wordlift_Metabox_Field_Select extends WL_Metabox_Field {

	/**
	 * @inheritdoc
	 */
	public function html_input( $text ) {
		@ob_start();
		?>
		<div class="wl-input-wrapper">

			<select name="wl_metaboxes[<?php echo $this->meta_name ?>]" id="<?php echo esc_attr( $this->meta_name ); ?>" style="width:88%;">
				<?php foreach ( $this->raw_custom_field['options'] as $option => $label ): ?>

					<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $text, $option ); ?>>
						<?php echo $label; ?>
					</option>

				<?php endforeach ?>
			</select>

			<div class="wl-input-notice"></div>
		</div>
		<?php

		$html = ob_get_clean();
		return $html;
	}

}
