<?php

namespace Wordlift\Metabox\Field;

/**
 * Metaxboxes: Date Field.
 *
 * This file defines the WL_Metabox_Field_date class which displays a date field
 * in WordPress' entity posts pages.
 *
 * @since   3.2.0
 * @package Wordlift
 */

/**
 * The WL_Metabox_Field_date class extends {@link Wl_Metabox_Field} and provides
 * support for date fields.
 *
 * @since   3.2.0
 * @package Wordlift
 */
class Wl_Metabox_Field_Date extends Wl_Metabox_Field {

	/**
	 * Attribute to distinguish between date formats, inferred from the schema property export type
	 *
	 * @since  3.2.0
	 * @access protected
	 * @var string $date_format The date format.
	 */
	protected $date_format;

	/**
	 * Boolean flag to decide if the calendar should include time or not
	 *
	 * @since  3.2.0
	 * @access protected
	 * @var boolean $timepicker A boolean flag.
	 */
	protected $timepicker;

	/**
	 * Whether the calendar should be displayed or not.
	 *
	 * @since  3.14.0
	 * @access protected
	 * @var boolean $no_calendar Whether the calendar should be displayed or not.
	 */
	protected $no_calendar;

	/**
	 * {@inheritdoc}
	 */
	public function __construct( $args, $id, $type ) {
		parent::__construct( $args, $id, $type );

		$this->no_calendar = false;

		// Distinguish between date and datetime
		if ( isset( $this->raw_custom_field['export_type'] ) && 'xsd:dateTime' === $this->raw_custom_field['export_type'] ) {
			$this->date_format = 'Y/m/d H:i';
			$this->timepicker  = true;
		} else {
			$this->date_format = 'Y/m/d';
			$this->timepicker  = false;
		}

	}

	/**
	 * @param mixed $date
	 *
	 * @return string
	 */
	public function html_input( $date ) {

		$this->log->debug( "Creating date input with date value $date..." );

		ob_start();
		?>
		<div class="wl-input-wrapper">
			<input
					type="text"
					class="<?php echo esc_attr( $this->meta_name ); ?>"
					name="wl_metaboxes[<?php echo esc_attr( $this->meta_name ); ?>][]"
					value="<?php echo esc_attr( $date ); ?>"
					style="width:88%"
			/>

			<button class="button wl-remove-input wl-button" type="button">
				<?php esc_html_e( 'Remove', 'wordlift' ); ?>
			</button>
		</div>
		<?php
		$html = ob_get_clean();

		return $html;
	}

	public function html_wrapper_close() {

		// Should the widget include time picker?
		$timepicker  = wp_json_encode( $this->timepicker );
		$date_format = wp_json_encode( $this->date_format );
		$no_calendar = wp_json_encode( $this->no_calendar );

		// Set up the datetimepicker.
		//
		// See https://github.com/trentrichardson/jQuery-Timepicker-Addon
		// See in http://trentrichardson.com/examples/timepicker.

		$js = wp_json_encode(
			array(
				'enableTime' => $timepicker,
				'noCalendar' => $no_calendar,
				'time_24hr'  => true,
				'dateFormat' => $date_format,
			)
		);

        // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		@ob_start();
		?>
		<script type='text/javascript'>
			(function ($) {
				$(function () {
					$('.<?php echo esc_js( $this->meta_name ); ?>[type=text]').flatpickr(<?php echo esc_html( $js ); ?>);
				});
			})(jQuery);
		</script>
		<?php
		$html = ob_get_clean();

		$html .= parent::html_wrapper_close();

		return $html;
	}

}
