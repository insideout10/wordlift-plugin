<?php

/**
 * Provides a parent class to expose taxonomy terms through the REST api in a custom namespace.
 *
 * @since 3.10.0
 */
class Wordlift_REST_Terms_Controller extends WP_REST_Terms_Controller {
	public function __construct( $taxonomy ) {
		parent::__construct( $taxonomy );
        $this->namespace     = 'wordlift/v1';
    }
}
