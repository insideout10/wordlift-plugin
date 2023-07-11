<?php
namespace Wordlift\Vocabulary\Data\Term_Data;

use Wordlift\Vocabulary\Analysis_Service;

class Term_Data_Factory {
	/**
	 * @var Analysis_Service
	 */
	private $analysis_service;

	/**
	 * @var Analysis_Service
	 */
	public function __construct( $analysis_service ) {
		$this->analysis_service = $analysis_service;
	}

	/**
	 * @param $term \WP_Term
	 *
	 * @return Term_Data
	 */
	public function get_term_data( $term ) {
		$entities = $this->analysis_service->get_entities( $term );
		return new Default_Term_Data( $term, $entities );
	}

}
