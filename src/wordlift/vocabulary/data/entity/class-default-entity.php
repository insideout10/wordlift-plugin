<?php

namespace Wordlift\Vocabulary\Data\Entity;

use Wordlift\Vocabulary\Api\Entity_Rest_Endpoint;

/**
 * This class is created to support new multiple entity matches in db.
 * @since 3.30.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Default_Entity extends Entity {

	const META_KEY = '_wl_vocabulary_entity_match_for_term';

	/**
	 * @var Legacy_Entity
	 */
	private $legacy_entity;

	/**
	 * Default_Entity constructor.
	 *
	 * @param $term_id int
	 * @param $legacy_entity Legacy_Entity
	 */
	public function __construct( $term_id, $legacy_entity ) {
		parent::__construct( $term_id );
		$this->legacy_entity = $legacy_entity;
	}

	public function get_jsonld_data() {
		$default_data = get_term_meta( $this->term_id, self::META_KEY, true );
		if ( is_array( $default_data ) ) {
			return $default_data;
		}

		// Use legacy entity if the data doesnt exist on that key.
		return $this->legacy_entity->get_jsonld_data();
	}

	public function save_jsonld_data( $entity_data ) {

		$same_as_list = array_merge( $entity_data['sameAs'], array( $entity_data['@id'] ) );

		$alt_labels = array( (string) $entity_data['name'] );

		$data = array(
			'@type'         => $entity_data['@type'],
			'description'   => $entity_data['description'],
			'sameAs'        => $same_as_list,
			'alternateName' => $alt_labels
		);

		update_term_meta( $this->term_id, self::META_KEY, $data );

	}

	public function clear_data() {
		delete_term_meta( $this->term_id, self::META_KEY );

	}
}