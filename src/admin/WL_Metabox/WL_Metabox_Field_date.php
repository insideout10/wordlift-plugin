<?php
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
 * The WL_Metabox_Field_date class extends {@link WL_Metabox_Field} and provides
 * support for date fields.
 *
 * @since   3.2.0
 * @package Wordlift
 */
class WL_Metabox_Field_date extends WL_Metabox_Field {

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
	public function __construct( $args ) {
		parent::__construct( $args );

		$this->no_calendar = false;

		// Distinguish between date and datetime
		if ( isset( $this->raw_custom_field['export_type'] ) && 'xsd:datetime' === $this->raw_custom_field['export_type'] ) {
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

		// $picker_date = ( empty( $date ) ? '' : esc_attr( date( $this->date_format, strtotime( $date ) ) ) );

		return <<<EOF
			<div class="wl-input-wrapper">
				<input type="text" class="$this->meta_name" name="wl_metaboxes[$this->meta_name][]" value="$date" style="width:88%" />
				<button class="button wl-remove-input wl-button" type="button">Remove</button>
			</div>
EOF;
	}

	public function html_wrapper_close() {

		// Should the widget include time picker?
		$timepicker  = json_encode( $this->timepicker );
		$date_format = json_encode( $this->date_format );
		$no_calendar = json_encode( $this->no_calendar );

		// Set up the datetimepicker.
		//
		// See https://github.com/trentrichardson/jQuery-Timepicker-Addon
		// See in http://trentrichardson.com/examples/timepicker.
		$html = <<<EOF
			<script type='text/javascript'>
				( function( $ ) {

					$( function() {

						$( '.$this->meta_name[type=text]' ).flatpickr( {
							enableTime: $timepicker,
							noCalendar: $no_calendar,
							time_24hr: true,
							dateFormat: $date_format
						 } );
					} );
				} ) ( jQuery );
			</script>
EOF;

		$html .= parent::html_wrapper_close();

		return $html;
	}

}
