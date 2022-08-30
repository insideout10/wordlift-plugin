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
 *
 * @package Wordlift\Term
 */
class Type_Service extends Singleton {
	/**
	 * @var \Wordlift_Schema_Service
	 */
	private $schema_service;

	public function __construct() {
		parent::__construct();
		$this->schema_service = \Wordlift_Schema_Service::get_instance();
	}

	public function get_entity_types_labels( $term_id ) {
		$entity_types = $this->get_entity_types( $term_id );
		return array_map(
			function ( $entity_type ) {
				return $entity_type->name;
			},
			$entity_types
		);
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

		$types = array_filter(
			array_map(
				function ( $type_slug ) {
					$term = get_term_by( 'slug', $type_slug, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
					if ( ! $term ) {
						  return false;
					}

					return $term;
				},
				$entity_type_slugs
			)
		);

		$types = array_filter( $types );

		if ( 0 !== count( $types ) ) {
			return $types;
		}

		return array( get_term_by( 'slug', 'thing', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME ) );
	}

	/**
	 * @param $term_id int
	 *
	 * @return array|null
	 */
	public function get_schema( $term_id ) {
		$entity_types = $this->get_entity_types( $term_id );
		foreach ( $entity_types as $entity_type ) {
			$schema = $this->schema_service->get_schema( $entity_type->slug );
			if ( ! $schema ) {
				break;
			}
		}

		return $this->schema_service->get_schema( 'thing' );
	}

	/**
	 * Removes all the existing entity types and set the entity types for the term.
	 *
	 * @param $term_id int
	 * @param $entity_types array
	 */
	public function set_entity_types( $term_id, $entity_types ) {
		delete_term_meta( $term_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
		foreach ( $entity_types as $entity_type ) {
			add_term_meta( $term_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, $entity_type );
		}
	}

}
