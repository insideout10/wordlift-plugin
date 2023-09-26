<?php
namespace Wordlift\Metabox\Field;

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
 * The Wordlift_Metabox_Field_Integer class extends {@link Wl_Metabox_Field} and provides
 * support for integer fields.
 *
 * @since   3.18.0
 * @package Wordlift
 */
class Wordlift_Metabox_Field_Integer extends Wl_Metabox_Field {
	/**
	 * @inheritdoc
	 */
	public function html_input( $text ) {
        // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		@ob_start();
		?>
			<div class="wl-input-wrapper">
				<input
					type="number"
					id="<?php echo esc_attr( $this->meta_name ); ?>"
					class="<?php echo esc_attr( $this->meta_name ); ?>"
					value="<?php echo esc_attr( $text ); ?>"
					name="wl_metaboxes[<?php echo esc_attr( $this->meta_name ); ?>][]"
					style="width:88%"
					min="0"
				/>

				<button class="button wl-remove-input wl-button" type="button">
					<?php esc_html_e( 'Remove', 'wordlift' ); ?>
				</button>

				<div class="wl-input-notice"></div>
			</div>

		<?php
		$html = ob_get_clean();

		return $html;
	}
}
