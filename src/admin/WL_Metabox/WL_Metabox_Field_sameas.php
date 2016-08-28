<?php

class WL_Metabox_Field_sameas extends WL_Metabox_Field {

	public function __construct( $args ) {
		parent::__construct( $args['sameas'] );
	}

	/**
	 * Only accept URIs
	 */
	public function sanitize_data_filter( $value ) {

		// Call our sanitizer helper.
		return Wordlift_Sanitizer::sanitize_url( $value );
	}
}
