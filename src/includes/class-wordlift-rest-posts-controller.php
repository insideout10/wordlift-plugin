<?php

/**
 * Provides a parent class to expose vocabulary items through the REST api in a custom namespace.
 *
 * @since 3.10.0
 */
class Wordlift_REST_Posts_Controller extends WP_REST_Posts_Controller {
	public function __construct( $post_type ) {
		parent::__construct( $post_type );
        $this->namespace     = 'wordlift/v1';
    }
}
