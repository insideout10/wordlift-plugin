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

	protected $sql = '';

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
	public function get() {
		$this->build();

		return $this->sql;
	}

	/**
	 * @return void
	 */
	abstract protected function build();

	/**
	 * Apply the sort for the cursor.
	 *
	 * @return Match_Query_Builder
	 */
	protected function cursor() {
		global $wpdb;

		// If there is no position set, the condition doesnt need to be applied
		// This is necessary in case of `last` and `first` attribute.
		if ( empty( $this->params['position'] ) ) {
			return $this;
		}

		$tmp_sql             = " AND {$this->sort->get_field_name()} ";
		$is_included         = ( $this->params['element'] !== 'EXCLUDED' );
		$is_ascending        = ( $this->params['direction'] !== 'DESCENDING' );
		$is_sorted_ascending = $this->sort->is_ascending();
		switch ( array( $is_ascending, $is_sorted_ascending ) ) {
			case array( true, true ):   // Forward & Ascending Order
			case array( false, false ): // Backward & Descending Order
				$tmp_sql .= ' >';
				break;
			case array( true, false ):  // Forward & Ascending Order
			case array( false, true ):  // Backward & Descending Order
				$tmp_sql .= ' <';
				break;
		}
		if ( $is_included ) {
			$tmp_sql .= '=';
		}
		$tmp_sql .= ' %s';

		// `$tmp_sql` is built dynamically in this function
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$this->sql .= $wpdb->prepare( $tmp_sql, $this->params['position'] );
		return $this;
	}

	protected function has_match() {
		$value = $this->params['has_match'];

		if ( true === $value ) {
			$this->sql .= ' AND e.about_jsonld IS NOT NULL ';
		} elseif ( false === $value ) {
			$this->sql .= ' AND e.about_jsonld IS NULL ';
		}

		return $this;
	}

	protected function limit() {
		$limit = $this->params['limit'];
		global $wpdb;
		$this->sql .= $wpdb->prepare( ' LIMIT %d', $limit );
		return $this;
	}

	protected function order_by() {
		$direction  = $this->params['direction'];
		$this->sql .= $this->sort->get_orderby_clause( $direction );
		return $this;
	}

}
