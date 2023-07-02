<?php

namespace Wordlift\Jsonld;

use Wordlift\Assertions;
use Wordlift\Content\Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Object_Type_Enum;
use Wordlift\Relation\Relations;

/**
 * This class represents a jsonld graph, This is an abstraction layer over the
 * associative array and the references, relations, reference_infos.
 */
class Graph {

	/**
	 * A single item in jsonld ( associative array )
	 *
	 * @var array
	 */
	private $main_jsonld;

	/**
	 * @var array<Content_Id>
	 */
	private $referenced_content_ids = array();
	/**
	 * @var \Wordlift_Post_Converter
	 */
	private $post_converter;
	/**
	 * @var \Wordlift_Term_JsonLd_Adapter
	 */
	private $term_converter;

	public function __construct( $main_jsonld, $post_converter, $term_converter ) {
		$this->main_jsonld    = $main_jsonld;
		$this->post_converter = $post_converter;
		$this->term_converter = $term_converter;
	}

	public function set_main_jsonld( $main_jsonld ) {
		$this->main_jsonld = $main_jsonld;
	}

	public function get_main_jsonld() {
		return $this->main_jsonld;
	}

	/**
	 * @param $references array<int>
	 *
	 * @return Graph
	 */
	public function add_references( $refs ) {
		Assertions::is_array( $refs );

		foreach ( $refs as $ref ) {
			$this->referenced_content_ids[] = Wordpress_Content_Id::create_post( $ref );
		}
		return $this;
	}

	/**
	 * @param $reference_infos array
	 *     Structure: [
	 *         [
	 *             'reference' => \Wordlift_Property_Entity_Reference,
	 *         ],
	 *         // more array items ...
	 *     ]
	 *
	 * @return Graph
	 */
	public function add_required_reference_infos( $references_infos ) {

		/**
		 * @var $required_references array<\Wordlift_Property_Entity_Reference>
		 */
		$required_references = array_filter(
			$references_infos,
			function ( $item ) {
				return isset( $item['reference'] ) &&
					   $item['reference'] instanceof \Wordlift_Property_Entity_Reference &&
					   // Check that the reference is required
					   $item['reference']->get_required();
			}
		);

		foreach ( $required_references as $data ) {
			$required_reference             = $data['reference'];
			$this->referenced_content_ids[] = new Wordpress_Content_Id(
				$required_reference->get_id(),
				$required_reference->get_type()
			);
		}
		return $this;

	}

	/**
	 * @param $relations Relations
	 *
	 * @return Graph
	 */
	public function add_relations( $relations ) {

		foreach ( $relations->toArray() as $relation ) {

			$this->referenced_content_ids[] = $relation->get_object();

		}

		return $this;
	}

	/**
	 * This method expands the content id and return only the jsonld.
	 *
	 * @param $content_id Wordpress_Content_Id
	 * @param $context int
	 * @return array|bool
	 */
	private function expand( $content_id, $context ) {
		$object_id   = $content_id->get_id();
		$object_type = $content_id->get_type();

		if ( $object_type === Object_Type_Enum::POST ) {
			return $this->expand_post( $object_id );
		} elseif ( $object_type === Object_Type_Enum::TERM ) {
			return $this->expand_term( $object_id, $context );
		}

		return false;

	}

	/**
	 * @param $context int Instance of Jsonld_Context_Enum
	 *
	 * @return array
	 */
	public function render( $context ) {

		/**
		 * This is possible because the toString() method of
		 * Wordpress_Content_Id is used to get the unique value.
		 */
		$unique_content_ids = array_unique( $this->referenced_content_ids, SORT_STRING );

		$result = array( $this->main_jsonld );

		foreach ( $unique_content_ids as $unique_content_id ) {
			$result[] = $this->expand( $unique_content_id, $context );
		}

		// Filter out the false and empty results.
		return array_filter( $result );

	}

	/**
	 * @param $object_id
	 *
	 * @return false|mixed
	 */
	public function expand_post( $object_id ) {
		if ( get_post_status( $object_id ) !== 'publish' || \Wordlift_Entity_Type_Service::get_instance()->has_entity_type(
			$object_id,
			'http://schema.org/Article'
		) ) {
			return false;
		}

		$references     = array();
		$reference_info = array();
		$relations      = new Relations();

		return $this->post_converter->convert( $object_id, $references, $reference_info, $relations );
	}

	/**
	 * @param $object_id
	 * @param $context
	 *
	 * @return false|mixed
	 */
	public function expand_term( $object_id, $context ) {
		// Skip the Uncategorized term.
		if ( 1 === $object_id ) {
			return false;
		}

		return current( $this->term_converter->get( $object_id, $context ) );
	}

}
