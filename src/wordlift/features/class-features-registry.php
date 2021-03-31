<?php

namespace Wordlift\Features;

class Features_Registry {

	/**
	 * @var array<Feature>
	 */
	private $features_list = array();

	private static $instance = null;

	public static function get_instance() {
		if ( self::$instance === null ) {
			self::$instance = new Features_Registry();
		}

		return self::$instance;
	}

	/**
	 * @param $feature Feature
	 */
	public function register_feature( $feature ) {
		$this->features_list[] = $feature;
	}

	/**
	 * @param $feature_slug string
	 * @param $default_value bool
	 * @param $callback callable
	 */
	public function register_feature_from_slug( $feature_slug, $default_value, $callback ) {
		$this->features_list[] = new Feature(
			$feature_slug,
			$default_value,
			$callback
		);
	}

}