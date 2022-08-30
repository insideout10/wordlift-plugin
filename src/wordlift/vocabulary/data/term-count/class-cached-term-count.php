<?php

namespace Wordlift\Vocabulary\Data\Term_Count;

/**
 * This class is used as decorator around Term_Count interface for
 * providing a cache layer.
 *
 * @since 3.30.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Cached_Term_Count implements Term_Count {

	const TRANSIENT_KEY = '_wl_vocabulary_term_count_transient';
	/**
	 * @var Term_Count
	 */
	private $term_count;

	/**
	 * Cached_Term_Count constructor.
	 *
	 * @param $term_count Term_Count
	 */
	public function __construct( $term_count ) {
		$this->term_count = $term_count;
	}

	public function get_term_count() {
		$data = get_transient( self::TRANSIENT_KEY );

		if ( ! $data ) {
			$data = $this->term_count->get_term_count();
			set_transient( self::TRANSIENT_KEY, $data, 8 * 60 * 60 );

			return $data;
		}

		return $data;
	}
}
