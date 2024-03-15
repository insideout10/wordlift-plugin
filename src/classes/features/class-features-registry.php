<?php

namespace Wordlift\Features;

class Features_Registry {

	/**
	 * @var array<Feature>
	 */
	private $features = array();

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new Features_Registry();
		}

		return self::$instance;
	}

	/**
	 * @param $feature Feature
	 */
	public function register_feature( $feature ) {
		$this->features[] = $feature;
	}

	/**
	 * @param $feature_slug string
	 * @param $default_value bool
	 * @param $callback callable
	 */
	public function register_feature_from_slug( $feature_slug, $default_value, $callback ) {
		$this->features[] = new Feature(
			$feature_slug,
			$default_value,
			$callback
		);
	}

	public function initialize_all_features() {
		foreach ( $this->features as $feature ) {
			/**
			 * @var $feature Feature
			 */
			$feature_slug = $feature->feature_slug;
			if ( apply_filters( "wl_feature__enable__{$feature_slug}", $feature->default_value ) ) {
				call_user_func( $feature->callback );
			}
		}
	}

	public function clear_all() {
		$this->features = array();
	}
}
