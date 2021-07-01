<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class returns the term by URI.
 */

namespace Wordlift\Term;

use Wordlift\Common\Singleton;
use Wordlift_Entity_Type_Taxonomy_Service;

/**
 * This class is used to provide entity types for the terms.
 * Class Type_Service
 * @package Wordlift\Term
 */
class Type_Service extends Singleton {

	/**
	 * @return Type_Service
	 */
	public static function get_instance() {
		return parent::get_instance();
	}

	/**
	 * Returns the entity types selected for the term.
	 *
	 * @param $term_id int
	 *
	 * @return \WP_Term[]
	 */
	public function get_entity_types( $term_id ) {

		$entity_type_slugs = get_term_meta( $term_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		$types = array_filter( array_map( function ( $type_slug ) {
			$term = get_term_by( 'slug', $type_slug, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
			if ( ! $term ) {
				return false;
			}

			return $term;
		}, $entity_type_slugs ) );

		$types = array_filter( $types );

		if ( 0 !== count( $types ) ) {
			return $types;
		}

		return array( get_term_by( 'slug', 'thing', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME ) );
	}


}