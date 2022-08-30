<?php
/**
 * This class runs after the entity type is changed.
 *
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Entity_Type;

use Wordlift\Jsonld\Jsonld_Article_Wrapper;
use Wordlift_Entity_Service;
use Wordlift_Entity_Type_Service;
use Wordlift_Entity_Type_Taxonomy_Service;

class Entity_Type_Change_Handler {
	/**
	 * @var Wordlift_Entity_Service
	 */
	private $entity_service;
	/**
	 * @var Wordlift_Entity_Type_Service
	 */
	private $entity_type_service;

	/**
	 * Entity_Type_Change_Handler constructor.
	 *
	 * @param $entity_service Wordlift_Entity_Service
	 * @param $entity_type_service Wordlift_Entity_Type_Service
	 */
	public function __construct( $entity_service, $entity_type_service ) {

		$this->entity_service = $entity_service;

		$this->entity_type_service = $entity_type_service;

		// Takes a performance toll, do we really need it?
		// add_action( 'set_object_terms', array( $this, 'set_object_terms' ), 10, 4 );
	}

	public function set_object_terms( $object_id, $terms, $tt_ids, $taxonomy ) {

		if ( Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME !== $taxonomy ) {
			return;
		}

		if ( count( $terms ) !== 1 ) {
			// Unable to determine which entity type or multiple entity types.
			return;
		}

		// This taxonomy is registered only for post, so the object id would
		// be the post id.
		$types = $this->entity_type_service->get_names( $object_id );

		if ( count( $types ) !== 1 ) {
			// Unable to determine which entity type or multiple entity types.
			return;
		}

		// Check if set to Article or one of its descendants
		if ( ! in_array( $types[0], Jsonld_Article_Wrapper::$article_types, true ) ) {
			return;
		}

		// clear the labels.
		$this->entity_service->set_alternative_labels( $object_id, array() );

	}

}
