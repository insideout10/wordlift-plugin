<?php
/**
 * @since 3.31.2
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Vocabulary\Jsonld;

class Term_Jsonld {

	public function init() {
		add_action( 'wl_term_jsonld_array', array( $this, 'wl_term_jsonld_array' ), 10, 2 );
	}

	public function wl_term_jsonld_array( $jsonld_array, $term_id ) {

		$entities = Jsonld_Utils::get_matched_entities_for_term( $term_id );

		if ( count( $entities ) > 0 ) {

			$entities_with_context = array_map( function ( $entity ) use ( $term_id ) {
				$entity['@context'] = 'http://schema.org';
				$entity['@id']      = get_term_link( $term_id ) . "/#id";
				$entity['url']      = get_term_link( $term_id );
				$entity['mainEntityOfPage']      = get_term_link( $term_id );
				return $entity;
			}, $entities );

			$jsonld_array['jsonld'] = array_merge( $jsonld_array['jsonld'], $entities_with_context );
		}

		return $jsonld_array;
	}

}