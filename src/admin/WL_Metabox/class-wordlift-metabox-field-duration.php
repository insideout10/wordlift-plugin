<?php
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
 * The Wordlift_Metabox_Field_Duration class extends {@link WL_Metabox_Field} and provides
 * support for time duration fields.
 *
 * @since   3.14.0
 * @package Wordlift
 */
class Wordlift_Metabox_Field_Duration extends WL_Metabox_Field_date {

	/**
	 * {@inheritdoc}
	 */
	public function __construct( $args ) {
		parent::__construct( $args );

		$this->date_format = 'H:i';
		$this->timepicker  = true;
		$this->no_calendar = true;

	}

}
