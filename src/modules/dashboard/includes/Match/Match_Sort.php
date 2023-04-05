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

	abstract public function property_name();

	abstract public function is_ascending();

	abstract public function get_sort_order( $direction, $sort_ascending );

	abstract public function get_orderby_clause( $direction );

}
