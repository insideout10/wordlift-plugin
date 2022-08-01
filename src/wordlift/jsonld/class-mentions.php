<?php
/**
 * This file adds the mentions property for all the entities which are descendant of creativework.
 *
 * @see https://github.com/insideout10/wordlift-plugin/issues/1557
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @package Wordlift\Jsonld
 * @since 3.37.1
 */

namespace Wordlift\Jsonld;

use Wordlift\Object_Type_Enum;
use Wordlift\Relation\Object_Relation_Service;

class Mentions {

	public function __construct() {
		add_filter( 'wl_after_get_jsonld', array( $this, 'wl_after_get_jsonld' ), 10, 2 );
	}

	public function wl_after_get_jsonld( $jsonld, $post_id ) {

		if ( count( $jsonld ) === 0 || ! array_key_exists( '@type', $jsonld[0] ) || array_key_exists( 'mentions', $jsonld[0] ) ) {
			return $jsonld;
		}


		$type = $jsonld[0]['@type'];



		if ( ! $this->entity_is_descendant_of_creative_work( $type ) && ! $this->entity_is_creative_work( $type ) ) {
			return $jsonld;
		}

		$entity_references = Object_Relation_Service::get_instance()
		                                            ->get_references( $post_id, Object_Type_Enum::POST );



		$jsonld[0]['mentions'] = array_values( array_filter( array_map( function ( $item ) {
			$id = \Wordlift_Entity_Service::get_instance()->get_uri( $item->get_id() );
			if ( ! $id ) {
				return false;
			}

			return array( '@id' => $id );

		}, $entity_references ) ) );

		// Remove mentions if the count is zero.
		if ( count( $jsonld[0]['mentions'] ) === 0 ) {
			unset( $jsonld[0]['mentions'] );
		}


		return $jsonld;
	}


	private function entity_is_descendant_of_creative_work( $type ) {

		if ( ! is_array( $type ) ) {
			$type = array( $type );
		}

		$creative_work_term = get_term_by( 'name', 'CreativeWork', \Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		if ( ! $creative_work_term ) {
			return false;
		}

		$descendants = get_term_meta( $creative_work_term->term_id, '_wl_parent_of' );

		$slugs_to_schema_name = array_map( function ( $item ) {
			return implode( '', array_map( 'ucfirst', explode( '-', $item ) ) );
		}, $descendants );

		return count( array_intersect( $type, $slugs_to_schema_name ) ) > 0;

	}

	private function entity_is_creative_work( $type ) {
		return ( $type === 'CreativeWork' ) || ( is_array( $type ) && in_array( 'CreativeWork', $type ) );
	}

}