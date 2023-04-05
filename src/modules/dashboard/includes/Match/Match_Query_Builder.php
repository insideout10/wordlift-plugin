<?php

namespace Wordlift\Modules\Dashboard\Match;

use Wordlift\Assertions;

abstract class Match_Query_Builder {
	/**
	 * @var array
	 */
	protected $params;
	/**
	 * @var Match_Sort
	 */
	protected $sort;

	/**
	 * @param $params array
	 * @param $sort Match_Sort
	 *
	 * @throws \Exception Throws Exception if the parameters arent of right type.
	 */
	public function __construct( $params, $sort ) {

		Assertions::is_array( $params );
		Assertions::is_a( $sort, Match_Sort::class );

		$this->params = $params;
		$this->sort   = $sort;
	}

	/**
	 * This method should build the query from params and sort object,
	 * then return the prepared query.
	 *
	 * @return string
	 */
	abstract public function build();

}
