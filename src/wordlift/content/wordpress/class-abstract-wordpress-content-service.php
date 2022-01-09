<?php

namespace Wordlift\Content\Wordpress;

use Wordlift\Assertions;
use Wordlift\Content\Content_Service;
use Wordlift_Configuration_Service;

abstract class Abstract_Wordpress_Content_Service implements Content_Service {

	protected function __construct() {

	}

	protected function get_dataset_uri() {
		return Wordlift_Configuration_Service::get_instance()->get_dataset_uri();
	}

	protected function is_absolute( $uri ) {
		return 1 === preg_match( '@^https?://@', $uri );
	}

	protected function is_internal( $uri ) {
		$dataset_uri = $this->get_dataset_uri();

		return ! empty( $dataset_uri ) && 0 === strpos( $uri, $dataset_uri );
	}

	protected function make_absolute( $uri ) {
		Assertions::not_empty( $this->get_dataset_uri(), '`dataset_uri` cannot be empty.' );

		if ( 1 !== preg_match( '@^https?://@', $uri ) ) {
			return untrailingslashit( $this->get_dataset_uri() ) . '/' . $uri;
		}

		return $uri;
	}

	protected function make_relative( $uri ) {
		$dataset_uri = trailingslashit( $this->get_dataset_uri() );
		if ( 0 === strpos( $uri, $dataset_uri ) ) {
			return substr( $uri, strlen( $dataset_uri ) );
		}

		return $uri;
	}

}