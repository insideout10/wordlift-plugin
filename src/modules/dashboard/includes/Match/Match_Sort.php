<?php

namespace Wordlift\Modules\Dashboard\Match;

abstract class Match_Sort {

	/**
	 * The sort string, example: +name,-name,-id,+id
	 *
	 * @var string
	 */
	protected $sort;

	public function __construct( $sort ) {
		$this->sort = $sort;
	}
	abstract public function get_field_name();

	public function property_name() {
		$tmp_sort_field_name = substr( $this->sort, 1 );
		// @todo: Need to discuss this code due to unit tests error - Undefined property: stdClass::$name.
		if ( 'id' === $tmp_sort_field_name || str_contains( $tmp_sort_field_name, 'date_modified_gmt' ) ) {
			return 'id';
		} else {
			return 'name';
		}
	}

	public function is_ascending() {
		return strpos( $this->sort, '-' ) !== 0;
	}

	private function get_sort_order( $direction, $sort_ascending ) {
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

	public function get_orderby_clause( $direction ) {
		$sort_order = $this->get_sort_order( $direction, $this->is_ascending() );
		return " ORDER BY {$this->get_field_name()} $sort_order";
	}

	public function apply( &$items ) {
		$sort_ascending     = $this->is_ascending();
		$sort_property_name = $this->property_name();
		usort(
			$items,
			function ( $a, $b ) use ( $sort_ascending, $sort_property_name ) {
				if ( $a->{$sort_property_name} === $b->{$sort_property_name} ) {
					return 0;
				}

				switch ( array(
					$sort_ascending,
					$a->{$sort_property_name} > $b->{$sort_property_name},
				) ) {
					case array( true, true ):
					case array( false, false ):
						return 1;
					case array( true, false ):
					case array( false, true ):
						return - 1;
				}

				return 0;
			}
		);
	}

}
