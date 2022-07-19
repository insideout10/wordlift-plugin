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

use Wordlift_Schemaorg_Class_Service;

class Mentions {

	public function __construct() {
		add_filter( 'wl_entity_jsonld_array', array( $this, 'wl_entity_jsonld_array' ), 10, 2 );
	}

	public function wl_entity_jsonld_array( $arr, $post_id ) {
		$jsonld     = $arr['jsonld'];
		$references = $arr['references'];

		$type = $jsonld['@type'];

		if ( ! $this->entity_is_descendant_of_creative_work( $type ) && ! $this->entity_is_creative_work( $type ) ) {
			return $arr;
		}

		$related_entity_ids = \Wordlift_Entity_Service::get_instance()->get_related_entities( $post_id );


		$jsonld['mentions'] = array_filter( array_map( function ( $item ) {
			$id = \Wordlift_Entity_Service::get_instance()->get_uri( $item );
			if ( ! $id ) {
				return false;
			}

			return array( '@id' => $id );

		}, $related_entity_ids ) );


		return array(
			'jsonld'     => $jsonld,
			'references' => $references
		);
	}

	public function get_mentions() {

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