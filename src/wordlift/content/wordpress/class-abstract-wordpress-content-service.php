<?php

namespace Wordlift\Content\Wordpress;

use Exception;
use Wordlift\Assertions;
use Wordlift\Content\Content_Service;

abstract class Abstract_Wordpress_Content_Service implements Content_Service {

	private $dataset_uri;

	/**
	 * @throws Exception when the `dataset_uri` isn't a URL.
	 */
	public function __construct( $dataset_uri ) {
		$this->dataset_uri = $dataset_uri;
	}

	protected function get_dataset_uri() {
		return $this->dataset_uri;
	}

	protected function is_absolute( $uri ) {
		return 1 === preg_match( '@^https?://@', $uri );
	}

	protected function is_internal( $uri ) {
		return 0 === strpos( $uri, $this->dataset_uri );
	}

	protected function make_absolute( $uri ) {
		if ( 1 !== preg_match( $uri, '@^https?://@' ) ) {
			return $this->dataset_uri . '/' . $uri;
		}

		return $uri;
	}

	protected function make_relative( $uri ) {
		if ( 0 === strpos( $uri, $this->get_dataset_uri() ) ) {
			return substr( $uri, strlen( $this->get_dataset_uri() ) );
		}

		return $uri;
	}

}