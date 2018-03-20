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
	 * @access private
	 * @var string $date_format The date format.
	 */
	private $date_format;

	/**
	 * Boolean flag to decide if the calendar should include time or not
	 *
	 * @since  3.2.0
	 * @access private
	 * @var boolean $timepicker A boolean flag.
	 */
	private $timepicker;

	/**
	 * {@inheritdoc}
	 */
	public function __construct( $args ) {
		parent::__construct( $args );

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

		$picker_date = ( empty( $date ) ? '' : esc_attr( date( $this->date_format, strtotime( $date ) ) ) );

		return <<<EOF
			<div class="wl-input-wrapper">
				<input type="text" class="$this->meta_name" value="$picker_date" style="width:88%" />
				<input type="hidden" class="$this->meta_name" name="wl_metaboxes[$this->meta_name][]" value="$date" />
				<button class="button wl-remove-input wl-button" type="button" style="width:10%">Remove</button>
			</div>
EOF;
	}

	public function html_wrapper_close() {

		// Should the widget include time picker?
		$timepicker = json_encode( $this->timepicker );

		// Set up the datetimepicker.
		//
		// See https://github.com/trentrichardson/jQuery-Timepicker-Addon
		// See in http://trentrichardson.com/examples/timepicker.
		$html = <<<EOF
			<script type='text/javascript'>
				( function( $ ) {

					$( function() {

						$( '.$this->meta_name[type=text]' ).wldatetimepicker( {
							// Format of date time displayed at the input.
							dateFormat: 'yy/mm/dd',
							timeFormat: 'HH:mm',

							// The hidden field used to store the value, and the format.
							altField: $( '.$this->meta_name[type=hidden]' ),
							altFormat: 'yy-mm-dd',
							altFieldTimeOnly: false,
							altSeparator:'T',
							altTimeFormat: 'HH:mm',
							altTimeSuffix:':00Z',

							// Widget UI options.
							showTimepicker: $timepicker,
							changeMonth: true,
							changeYear: true,
							yearRange : 'c-15:c+15'
						});

					} );
				} ) ( jQuery );
			</script>
EOF;

		$html .= parent::html_wrapper_close();

		return $html;
	}

}
