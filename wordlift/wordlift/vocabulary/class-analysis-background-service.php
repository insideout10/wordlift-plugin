<?php

namespace Wordlift\Vocabulary;

class Analysis_Background_Service {

	const ANALYSIS_DONE_FLAG        = '_wl_cmkg_analysis_complete_for_term_options_cache';
	const TERMS_COUNT_TRANSIENT     = '_wl_cmkg_analysis_background_service_terms_count';
	const ENTITIES_PRESENT_FOR_TERM = '_wl_cmkg_analysis_entities_present_for_term_options_cache';

	/**
	 * @var Analysis_Service
	 */
	private $analysis_service;
	/**
	 * @var Analysis_Background_Process
	 */
	private $analysis_background_process;

	/**
	 * @var \Wordlift_Log_Service
	 */
	private $log;

	public function __construct( $analysis_service ) {

		$this->analysis_service = $analysis_service;

		$this->analysis_background_process = new Analysis_Background_Process( $this );

		$this->log = \Wordlift_Log_Service::get_logger( get_class() );
	}

	public function start() {
		$this->analysis_background_process->start();
	}

	public function cancel() {
		$this->analysis_background_process->cancel();
	}

	public function stop() {
		$this->analysis_background_process->cancel();
		$this->analysis_background_process->request_cancel();
	}

	/**
	 * A list of term ids.
	 *
	 * @return int|\WP_Error|\WP_Term[]
	 */
	public function next() {

		return Terms_Compat::get_terms(
			Terms_Compat::get_public_taxonomies(),
			array(
				'fields'                             => 'ids',
				'hide_empty'                         => false,
				'number'                             => $this->get_batch_size(),
				// 'offset'     => $state->index,
										'meta_query' => array(
											array(
												'key'     => self::ANALYSIS_DONE_FLAG,
												'compare' => 'NOT EXISTS',
											),
										),
			)
		);
	}

	public function count() {

		$count = count(
			Terms_Compat::get_terms(
				Terms_Compat::get_public_taxonomies(),
				array(
					'fields'     => 'ids',
					'hide_empty' => false,
					// return all terms, we cant pass -1 here.
					'number'     => 0,
					'meta_query' => array(
						array(
							'key'     => self::ANALYSIS_DONE_FLAG,
							'compare' => 'NOT EXISTS',
						),
					),
				)
			)
		);

		$this->log->debug( "Count returned as $count" );

		return $count;
	}

	public function get_batch_size() {
		return 10;
	}

	public function info() {
		return Analysis_Background_Process::get_state();
	}

	/**
	 * @param $term_ids
	 *
	 * @return bool
	 */
	public function perform_analysis_for_terms( $term_ids ) {

		foreach ( $term_ids as $term_id ) {

			$tag = get_term( $term_id );

			// This adds the entities to ttl cache
			$result = $this->analysis_service->get_entities( $tag );

			// then set the analysis complete flag.
			update_term_meta( $term_id, self::ANALYSIS_DONE_FLAG, 1 );

			if ( false !== $result ) {
				if ( count( $result ) > 0 ) {
					update_term_meta( $term_id, self::ENTITIES_PRESENT_FOR_TERM, 1 );
				}
			}
		}

		/**
		 * This action fires when the analysis is complete for the current batch
		 *
		 * @since 3.30.0
		 */
		do_action( 'wordlift_vocabulary_analysis_complete_for_terms_batch' );

		return true;

	}

}
