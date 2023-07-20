<?php
/**
 * @since 3.31.2
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Vocabulary\Jsonld;

use Wordlift_Log_Service;

/**
 * Class Term_Jsonld
 *
 * @package Wordlift\Vocabulary\Jsonld
 */
class Term_Jsonld {
	/**
	 * Init.
	 */
	public function init() {
		add_filter( 'wl_term_jsonld_array', array( $this, 'wl_term_jsonld_array' ), 10, 2 );
	}

	/**
	 * Wl term jsonld array.
	 *
	 * @param $jsonld_array
	 * @param $term_id
	 *
	 * @return array|mixed
	 */
	public function wl_term_jsonld_array( $jsonld_array, $term_id ) {

		$entities = Jsonld_Utils::get_matched_entities_for_term( $term_id );

		if ( count( $entities ) > 0 ) {
			$entity             = array_shift( $entities );
			$entity['@context'] = 'http://schema.org';

			$term_link = get_term_link( $term_id );
			if ( is_wp_error( $term_link ) ) {
				Wordlift_Log_Service::get_logger( get_class() )
					->error( "Term $term_id returned an error: " . $term_link->get_error_message() );

				return $jsonld_array;
			}

			$entity['@id']              = $term_link . '/#id';
			$entity['url']              = $term_link;
			$entity['mainEntityOfPage'] = $term_link;
			$jsonld_array['jsonld'][]   = $entity;
		}

		return $jsonld_array;
	}
}
