<?php

/**
 * This file defines the WL_Metabox_Field_date class which displays a date field
 * in WordPress' entity posts pages.
 */

/**
 * The WL_Metabox_Field_date class extends {@link WL_Metabox_Field} and provides
 * support for date fields.
 *
 * @since 3.2.0
 */
class WL_Metabox_Field_date extends WL_Metabox_Field {

	/**
	 * Attribute to distinguish between date formats, inferred from the schema property export type
	 *
	 * @access private
	 * @var string $date_format The date format.
	 * @since 3.2.0
	 */
	private $date_format;

	/**
	 * Boolean flag to decide if the calendar should include time or not
	 *
	 * @access private
	 * @var boolean $timepicker A boolean flag.
	 * @since 3.2.0
	 */
	private $timepicker;

	public function __construct( $args ) {
		parent::__construct( $args );

		// Distinguish between date and datetime	
		if ( isset( $this->raw_custom_field['export_type'] ) && 'xsd:datetime' === $this->raw_custom_field['export_type'] ) {
			$this->date_format = 'Y/m/d H:i';
			$this->timepicker  = TRUE;
		} else {
			$this->date_format = 'Y/m/d';
			$this->timepicker  = FALSE;
		}
	}

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

		$html = <<<EOF
			<script type='text/javascript'>
				( function( $ ) {

					$( function() {

						$( '.$this->meta_name[type=text]' ).wlDateTimePicker( {
							scrollInput: false,			
							format: '$this->date_format',
							timepicker:$timepicker,
							onChangeDateTime:function(dp, input){

								// format must be: 'YYYY-MM-DDTHH:MM:SSZ' from '2014/11/21 04:00'
								var currentDate = input.val()
									.replace(/(\d{4})\/(\d{2})\/(\d{2}) (\d{2}):(\d{2})/,'$1-$2-$3T$4:$5:00Z');

								// store value to save in the hidden input field
								$( '.$this->meta_name[type=hidden]' ).val( currentDate );
							}
						});

					} );
				} ) ( jQuery );
			</script>
EOF;

		$html .= parent::html_wrapper_close();

		return $html;
	}
}
