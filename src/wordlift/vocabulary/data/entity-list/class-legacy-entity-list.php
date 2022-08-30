<?php

namespace Wordlift\Vocabulary\Data\Entity_List;

use Wordlift\Vocabulary\Api\Entity_Rest_Endpoint;

/**
 * This class is created to provide compatibility for the legacy matches stored on the db,
 * for saving the data from match entity endpoint, this wont be used.
 *
 * @since 3.30.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Legacy_Entity_List extends Entity_List {

	/**
	 * This function is called when default entity doesnt find any data.
	 *
	 * @return array
	 */
	public function get_jsonld_data() {
		$tag  = get_term( $this->term_id );
		$type = get_term_meta( $tag->term_id, Entity_Rest_Endpoint::TYPE_META_KEY, true );
		if ( '' === $type ) {
			return array();
		}

		return array(
			array(
				'@id'           => get_term_link( $tag->term_id ) . '#id',
				'@type'         => $type,
				'name'          => $tag->name,
				'description'   => ! empty( $tag->description ) ? $tag->description : get_term_meta( $tag->term_id, Entity_Rest_Endpoint::DESCRIPTION_META_KEY, true ),
				'sameAs'        => get_term_meta( $tag->term_id, Entity_Rest_Endpoint::SAME_AS_META_KEY ),
				'alternateName' => get_term_meta( $tag->term_id, Entity_Rest_Endpoint::ALTERNATIVE_LABEL_META_KEY ),
			),
		);
	}

	/**
	 * This function is never used.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return bool
	 */
	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function save_jsonld_data( $request ) {
		return false;
	}

	public function clear_data() {
		delete_term_meta( $this->term_id, Entity_Rest_Endpoint::SAME_AS_META_KEY );
		delete_term_meta( $this->term_id, Entity_Rest_Endpoint::ALTERNATIVE_LABEL_META_KEY );
		delete_term_meta( $this->term_id, Entity_Rest_Endpoint::DESCRIPTION_META_KEY );
		delete_term_meta( $this->term_id, Entity_Rest_Endpoint::TYPE_META_KEY );
		delete_term_meta( $this->term_id, Entity_Rest_Endpoint::EXTERNAL_ENTITY_META_KEY );
	}

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function remove_entity_by_id( $entity_id ) {
		$this->clear_data();
	}
}
