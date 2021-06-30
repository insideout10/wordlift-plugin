<?php

namespace Wordlift\Dataset;

use Wordlift\Object_Type_Enum;

class Sync_Term_Adapter extends Abstract_Sync_Object_Adapter {
	/**
	 * @var int
	 */
	private $term_id;

	/**
	 * Sync_Term_Adapter constructor.
	 *
	 * @param int $term_id
	 *
	 * @throws \Exception
	 */
	function __construct( $term_id ) {
		parent::__construct( Object_Type_Enum::TERM, $term_id );

		$this->term_id = $term_id;
	}

	function is_published() {
		return $this->is_public();
	}

	function is_public() {
		$term = get_term( $this->term_id );

		return get_taxonomy( $term->taxonomy )->public;
	}

}
