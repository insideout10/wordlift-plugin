<?php

namespace Wordlift\Features;

class Feature {

	/**
	 * @var string
	 */
	public $feature_slug;

	/**
	 * @var bool
	 */
	public $default_value;

	/**
	 * @var Callable
	 */
	public $callback;

	/**
	 * Feature constructor.
	 *
	 * @param $feature_slug string Slug for feature without wl__feature__enable
	 * @param $default_value bool Default value for the flag, true or false.
	 * @param $callback Callable Callback for the feature.
	 */
	public function __construct( $feature_slug, $default_value, $callback ) {
		$this->feature_slug  = $feature_slug;
		$this->default_value = $default_value;
		$this->callback      = $callback;
	}

}
