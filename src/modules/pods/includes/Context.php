<?php

namespace Wordlift\Modules\Pods;

class Context {

	const POST       = 0;
	const TERM       = 1;
	const ADMIN_AJAX = 2;
	const UNKNOWN    = -1;
	/**
	 * @var $object_type int
	 */
	private $object_type;
	/**
	 * @var $identifier int
	 */
	private $identifier;
	/**
	 * @var $custom_fields Schema_Field_Group[]
	 */
	private $custom_fields;

	private static $pod_types_map = array(
		self::POST => 'post_type',
		self::TERM => 'taxonomy',
	);

	/**
	 * @param int           $object_type
	 * @param $identifier
	 * @param $custom_fields
	 */
	public function __construct( $object_type, $identifier, $custom_fields ) {
		$this->object_type   = $object_type;
		$this->identifier    = $identifier;
		$this->custom_fields = $custom_fields;
	}

	/**
	 * @return int
	 */
	public function get_object_type() {
		return $this->object_type;
	}

	public function get_pod_name() {
		if ( self::POST === $this->object_type ) {
			return get_post_type( $this->identifier );
		} elseif ( self::TERM === $this->object_type ) {
			return get_term( $this->identifier )->taxonomy;
		}
	}

	public function get_pod_type() {
		return self::$pod_types_map[ $this->object_type ];
	}

	public function get_custom_fields() {
		return $this->custom_fields;
	}

}
