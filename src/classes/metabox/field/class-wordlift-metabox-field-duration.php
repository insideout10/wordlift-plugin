<?php
namespace Wordlift\Metabox\Field;

/**
 * Metaxboxes: Duration Field.
 *
 * This file defines the Wordlift_Metabox_Field_Duration class which displays a time duration field
 * in WordPress' entity posts pages.
 *
 * @since   3.14.0
 * @package Wordlift
 */

/**
 * The Wordlift_Metabox_Field_Duration class extends {@link Wl_Metabox_Field} and provides
 * support for time duration fields.
 *
 * @since   3.14.0
 * @package Wordlift
 */
class Wordlift_Metabox_Field_Duration extends Wl_Metabox_Field_date {

	/**
	 * {@inheritdoc}
	 */
	public function __construct( $args, $id, $type ) {
		parent::__construct( $args, $id, $type );

		$this->date_format = 'H:i';
		$this->timepicker  = true;
		$this->no_calendar = true;

	}

	/**
	 * Sanitize a single value. Called from $this->sanitize_data. Default sanitization excludes empty values.
	 * make sure the value is either empty, an integer representing valid number of minutes
	 * or an HH:MM time format.
	 *
	 * @param mixed $value The value being sanitized.
	 *
	 * @return mixed Returns sanitized value, or null.
	 */
	public function sanitize_data_filter( $value ) {

		if ( $value !== null && '' !== $value ) {         // do not use 'empty()' -> https://www.virendrachandak.com/techtalk/php-isset-vs-empty-vs-is_null/ .
			preg_match(
				'#((([01]?[0-9]{1}|2[0-3]{1}):)?[0-5]{1})?[0-9]{1}#',
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
