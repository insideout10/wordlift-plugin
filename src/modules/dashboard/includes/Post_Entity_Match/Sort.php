<?php

namespace Wordlift\Modules\Dashboard\Post_Entity_Match;

class Sort {
	/**
	 * @var string
	 */
	private $sort;

	public function __construct( $sort ) {
		$this->sort = $sort;
	}
	public function get_field_name() {
		$tmp_sort_field_name = substr( $this->sort, 1 );
		if ( 'id' === $tmp_sort_field_name ) {
			return 'p.ID';
		} else {
			return 'p.post_title';
		}
	}

	public function property_name() {
		$tmp_sort_field_name = substr( $this->sort, 1 );
		if ( 'id' === $tmp_sort_field_name ) {
			return 'id';
		} else {
			return 'name';
		}
	}

	public function is_ascending() {
		return strpos( $this->sort, '-' ) !== 0;
	}

	public function get_sort_order( $direction, $sort_ascending ) {
		switch ( array( $sort_ascending, $direction ) ) {
			case array( true, 'ASCENDING' ):
			case array( false, 'DESCENDING' ):
				return 'ASC';
			case array( true, 'DESCENDING' ):
			case array( false, 'ASCENDING' ):
				return 'DESC';
		}

		return 'ASC';
	}

}
