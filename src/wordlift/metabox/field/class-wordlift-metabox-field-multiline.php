<?php
namespace Wordlift\Metabox\Field;

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
 * The Wordlift_Metabox_Field_Multiline class extends {@link Wl_Metabox_Field} and provides
 * support for time duration fields.
 *
 * @since   3.14.0
 * @package Wordlift
 */
class Wordlift_Metabox_Field_Multiline extends Wl_Metabox_Field {

	/**
	 * @inheritdoc
	 */
	public function html_input( $text ) {
        // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		@ob_start();
		?>
			<div class="wl-input-wrapper">
				<textarea
					id="<?php echo esc_attr( $this->meta_name ); ?>"
					class="<?php echo esc_attr( $this->meta_name ); ?>"
					name="wl_metaboxes[<?php echo esc_attr( $this->meta_name ); ?>][]"
					style="width:88%"
				><?php echo esc_textarea( $text ); ?></textarea>

				<button class="button wl-remove-input wl-button" type="button">
					<?php esc_html_e( 'Remove', 'wordlift' ); ?>
				</button>
			</div>
		<?php
		$html = ob_get_clean();

		return $html;
	}
}
