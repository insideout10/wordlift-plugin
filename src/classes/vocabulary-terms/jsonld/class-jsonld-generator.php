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
	/**
	 * @var \Wordlift_Entity_Service
	 */
	private $entity_service;

	public function __construct( $entity_type_service, $property_getter ) {
		$this->entity_type_service      = $entity_type_service;
		$this->property_getter          = $property_getter;
		$this->term_entity_type_service = Type_Service::get_instance();
		$this->entity_service           = \Wordlift_Entity_Service::get_instance();
	}

	public function init() {
		add_filter( 'wl_term_jsonld_array', array( $this, 'wl_term_jsonld_array' ), 10, 2 );
	}

	public function wl_term_jsonld_array( $data, $term_id ) {
		$jsonld     = $data['jsonld'];
		$references = $data['references'];

		$term_jsonld_data = $this->get_jsonld_data_for_term( $term_id );

		// Return early if we dont have the entity data
		// for the term.
		if ( ! $term_jsonld_data ) {
			return $data;
		}

		$term_jsonld = $term_jsonld_data['jsonld'];

		$references = array_merge( $references, $term_jsonld_data['references'] );

		array_unshift( $jsonld, $term_jsonld );

		return array(
			'jsonld'     => $jsonld,
			'references' => $references,
		);
	}

	private function get_jsonld_data_for_term( $term_id ) {

		$id = $this->entity_service->get_uri( $term_id, Object_Type_Enum::TERM );

		// If we don't have a dataset  URI, then don't publish the term data
		// on this page.
		if ( ! $id ) {
			return false;
		}

		$references = array();
		$term       = get_term( $term_id );
		$permalink  = get_term_link( $term );

		$custom_fields = $this->entity_type_service->get_custom_fields_for_term( $term_id );
		$term          = get_term( $term_id );
		$jsonld        = array(
			'@context'    => 'http://schema.org',
			'name'        => $term->name,
			'@type'       => $this->term_entity_type_service->get_entity_types_labels( $term_id ),
			'@id'         => $id,
			'description' => wp_strip_all_tags( self::strip_all_shortcodes( $term->description ) ),
		);

		if ( ! $custom_fields || ! is_array( $custom_fields ) ) {
			return $jsonld;
		}

		foreach ( $custom_fields as $key => $value ) {
			$name  = $this->relative_to_schema_context( $value['predicate'] );
			$value = $this->property_getter->get( $term_id, $key, Object_Type_Enum::TERM );
			$value = $this->process_value( $value, $references );
			if ( ! isset( $value ) ||
				 is_array( $value ) && empty( $value ) ||
				 is_string( $value ) && empty( $value ) ) {
				continue;
			}
			$jsonld[ $name ] = $value;

		}

		$jsonld = \Wordlift_Entity_Post_To_Jsonld_Converter::post_process( $jsonld );

		if ( $permalink ) {
			$jsonld['mainEntityOfPage'] = $permalink;
		}

		return apply_filters(
			'wl_no_vocabulary_term_jsonld_array',
			array(
				'jsonld'     => $jsonld,
				'references' => $references,
			),
			$term_id
		);

	}

	private function relative_to_schema_context( $predicate ) {
		return str_replace( 'http://schema.org/', '', $predicate );
	}

	private function process_value( $value, &$references ) {

		if ( is_array( $value )
			 && count( $value ) > 0
			 && $value[0] instanceof \Wordlift_Property_Entity_Reference ) {

			// All of the references from the custom fields are post references.
			$references = array_merge(
				$references,
				array_map(
					function ( $property_entity_reference ) {
						/**
						 * @var $property_entity_reference \Wordlift_Property_Entity_Reference
						 */
						return $property_entity_reference->to_reference();
					},
					$value
				)
			);

			return array_map(
				function ( $reference ) {
					/**
					 * @var $reference \Wordlift_Property_Entity_Reference
					 */
					return array( '@id' => $reference->get_url() );
				},
				$value
			);

		}

		return $value;
	}

	/**
	 * Remove all the shortcodes from the content. We're using our own function
	 * because WordPress' own `strip_shortcodes` only takes into consideration
	 * shortcodes for installed plugins/themes.
	 *
	 * @since 3.12.0
	 *
	 * @param string $content The content with shortcodes.
	 *
	 * @return string The content without shortcodes.
	 */
	private static function strip_all_shortcodes( $content ) {
		return preg_replace( '/\[[^]]+\]/', '', $content );
	}
}
