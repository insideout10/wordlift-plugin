<?php
/**
 * Metaxboxes: Duration Field.
 *
 * This file defines the WL_Metabox_Field_duration class which displays a time duration field
 * in WordPress' entity posts pages.
 *
 * @since   3.14.0
 * @package Wordlift
 */

/**
 * The WL_Metabox_Field_duration class extends {@link WL_Metabox_Field} and provides
 * support for time duration fields.
 *
 * @since   3.14.0
 * @package Wordlift
 */
class WL_Metabox_Field_duration extends WL_Metabox_Field {

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
	public function html_input( $duration ) {

		$esc_duration = esc_attr( $duration );
		$esc_meta_name = esc_attr( $this->meta_name );
		$html = <<<EOF
			<div class="wl-input-wrapper">
				<input type="text" pattern="\s*((([01]?[0-9]{1}|2[0-3]{1}):)?[0-5]{1})?[0-9]{1}\s*" id="$esc_meta_name" class="$esc_meta_name" name="wl_metaboxes[$esc_meta_name][]" value="$esc_duration" style="width:88%" />
				<button class="button wl-remove-input wl-button" type="button" style="width:10 % ">Remove</button>
			</div>
EOF;

		return $html;
	}

	public function html_wrapper_close() {

		$invalid_message = esc_html__( 'Invalid format, should be time in HH:MM format or just MM', 'wordlift' );
		$html = <<<EOF
			<script type='text/javascript'>
				( function( $ ) {

					$( function() {

						$( '.$this->meta_name' ).each(function() {
							var invalid = function (e) {
							    if ( e.target.validity.patternMismatch ) {
							      e.target.setCustomValidity('$invalid_message');
								}
							  };
							this.oninvalid = invalid;
							this.onchange = function (e) {
								e.target.setCustomValidity('');
							}
						});

					} );
				} ) ( jQuery );
			</script>
EOF;

		$html .= parent::html_wrapper_close();

		return $html;
	}

	/**
	 * Sanitize a single value. Called from $this->sanitize_data. Default sanitization excludes empty values.
	 * make sure the value is either empty, an integer representing valid number of minutes
	 * or an HH:MM time format.
	 *
	 * @param mixed $value	The value being sanitized.
	 *
	 * @return mixed Returns sanitized value, or null.
	 */
	public function sanitize_data_filter( $value ) {

		if ( ! is_null( $value ) && '' !== $value ) {         // do not use 'empty()' -> https://www.virendrachandak.com/techtalk/php-isset-vs-empty-vs-is_null/ .
			preg_match( '#((([01]?[0-9]{1}|2[0-3]{1}):)?[0-5]{1})?[0-9]{1}#',
				trim( $value ),
				$matches
			);

			if ( count( $matches ) > 0 ) {
				return $matches[0];
			}
		}

		return null;
	}
}
