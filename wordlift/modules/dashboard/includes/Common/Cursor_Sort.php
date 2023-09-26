<?php

namespace Wordlift\Modules\Dashboard\Common;

class Cursor_Sort {

	private static $sort_property_to_colname = array(
		'date_modified_gmt' => 'post_modified_gmt',
		'term_name'         => 'name',
	);
	/**
	 * @var false|string
	 */
	private $sort_property;
	/**
	 * @var mixed
	 */
	private $sort_colname;
	/**
	 * @var string
	 */
	private $sort_direction;

	public function __construct( $sort ) {
		$this->sort_property  = substr( $sort, 1 );
		$this->sort_colname   = isset( self::$sort_property_to_colname[ $this->sort_property ] ) ? self::$sort_property_to_colname[ $this->sort_property ] : $this->sort_property;
		$this->sort_direction = substr( $sort, 0, 1 ) === '+' ? 'ASC' : 'DESC';
	}

	/**
	 * @return false|string
	 */
	public function get_sort_property() {
		return $this->sort_property;
	}

	/**
	 * @return false|mixed|string
	 */
	public function get_sort_colname() {
		return $this->sort_colname;
	}

	/**
	 * @return string
	 */
	public function get_sort_direction() {
		return $this->sort_direction;
	}

}
