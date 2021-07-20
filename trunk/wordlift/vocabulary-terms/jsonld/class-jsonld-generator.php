<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Vocabulary_Terms\Jsonld;

use Wordlift\Object_Type_Enum;
use Wordlift\Term\Type_Service;

class Jsonld_Generator {

	/**
	 * @var \Wordlift_Entity_Type_Service
	 */
	private $entity_type_service;
	/**
	 * @var \Wordlift_Property_Getter
	 */
	private $property_getter;
	/**
	 * @var Type_Service
	 */
	private $term_entity_type_service;

	public function __construct( $entity_type_service, $property_getter ) {
		$this->entity_type_service      = $entity_type_service;
		$this->property_getter          = $property_getter;
		$this->term_entity_type_service = Type_Service::get_instance();
	}

	public function init() {
		add_filter( 'wl_term_jsonld_array', array( $this, 'wl_term_jsonld_array' ), 10, 2 );
	}

	public function wl_term_jsonld_array( $data, $term_id ) {
		$jsonld     = $data['jsonld'];
		$references = $data['references'];

		$term_jsonld_data = $this->get_jsonld_data_for_term( $term_id );

		$term_jsonld = $term_jsonld_data['jsonld'];

		$references = array_merge( $references, $term_jsonld_data['references'] );

		array_unshift( $jsonld, $term_jsonld );

		return array(
			'jsonld'     => $jsonld,
			'references' => $references
		);
	}

	private function get_jsonld_data_for_term( $term_id ) {
		$term          = get_term( $term_id );
		$permalink     = get_term_link( $term );
		$custom_fields = $this->entity_type_service->get_custom_fields_for_term( $term_id );
		$term          = get_term( $term_id );
		$jsonld        = array(
			'@context'    => 'http://schema.org',
			'name'        => $term->name,
			'@type'       => $this->term_entity_type_service->get_entity_types_labels( $term_id ),
			'@id'         => wl_get_term_entity_uri( $term_id ),
			'description' => $term->description,
		);

		if ( ! $custom_fields || ! is_array( $custom_fields ) ) {
			return $jsonld;
		}

		foreach ( $custom_fields as $key => $value ) {
			$name  = $this->relative_to_schema_context( $value['predicate'] );
			$value = $this->property_getter->get( $term_id, $key, Object_Type_Enum::TERM );
			if ( $value ) {
				$jsonld[ $name ] = $value;
			}
		}

		if ( $permalink ) {
			$jsonld['url']              = $permalink;
			$jsonld['mainEntityOfPage'] = $permalink;
		}

		return apply_filters( 'wl_no_vocabulary_term_jsonld_array', array(
			'jsonld'     => $jsonld,
			'references' => array()
		), $term_id );


	}


	private function relative_to_schema_context( $predicate ) {
		return str_replace( 'http://schema.org/', '', $predicate );
	}

}