<?php

namespace Wordlift\Modules\Include_Exclude;

class Configuration {

	private static $instance;

	private $type;
	private $urls;

	protected function __construct() {
		$include_exclude_data = get_option( 'wl_exclude_include_urls_settings', array() );
		$include_exclude      = isset( $include_exclude_data['include_exclude'] ) ? $include_exclude_data['include_exclude'] : 'exclude';

		$this->type = in_array(
			$include_exclude,
			array(
				'include',
				'exclude',
			),
			true
		)
			? $include_exclude : 'exclude';
		$this->urls = isset( $include_exclude_data['urls'] ) ? $include_exclude_data['urls'] : '';
	}

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function get_type() {
		return $this->type;
	}

	/**
	 * Gets the default action, which is the inverse of the configuration type.
	 *
	 * @return string
	 */
	public function get_default() {
		return ( $this->type === 'exclude' ? 'include' : 'exclude' );
	}

	public function get_urls() {
		return $this->urls;
	}

}
