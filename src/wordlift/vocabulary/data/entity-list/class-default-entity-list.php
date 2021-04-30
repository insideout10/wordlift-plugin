<?php

namespace Wordlift\Vocabulary\Data\Entity_List;

/**
 * This class is created to support new multiple entity matches in db.
 * @since 3.30.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Default_Entity_List extends Entity_List {

	const META_KEY = '_wl_vocabulary_entity_match_for_term';

	/**
	 * @var Legacy_Entity_List
	 */
	private $legacy_entity;

	/**
	 * Default_Entity constructor.
	 *
	 * @param $term_id int
	 * @param $legacy_entity Legacy_Entity_List
	 */
	public function __construct( $term_id, $legacy_entity ) {
		parent::__construct( $term_id );
		$this->legacy_entity = $legacy_entity;
	}

	public function get_jsonld_data() {
		$default_data = get_term_meta( $this->term_id, self::META_KEY );
		if ( is_array( $default_data ) && $default_data ) {
			return $default_data;
		}

		// Use legacy entity if the data doesnt exist on that key.
		return $this->legacy_entity->get_jsonld_data();
	}

	public function save_jsonld_data( $entity_data ) {
		$entity_id    = $entity_data['@id'];
		$same_as_list = $entity_data['sameAs'];

		if ( $entity_id ) {
			$same_as_list = array_merge( $entity_data['sameAs'], array( $entity_id ) );
		}
		else {
			$same_as_list = array_merge( $entity_data['sameAs'], array( $entity_data['entityId'] ) );
		}

		$alt_labels = array( (string) $entity_data['name'] );

		$entity_list = get_term_meta( $this->term_id, self::META_KEY );


		$type = null;
		/**
		 * For entities from wiki we wont have @type
		 * in the meta.
		 */
		if ( array_key_exists( '@type', $entity_data ) ) {
			$type = $entity_data['@type'];
		} else if ( array_key_exists( 'mainType', $entity_data ) ) {
			$type = array_reduce( explode( "-", $entity_data['mainType'] ), function ($carry, $item) {
				return $carry . ucfirst($item);
			} );
		}

		$entity = array(
			'@type'         => $type,
			'description'   => $entity_data['description'],
			'sameAs'        => $same_as_list,
			'alternateName' => $alt_labels
		);

		$entity_list[] = $entity;

		$this->clear_and_save_list( $entity_list );

	}

	public function clear_data() {
		delete_term_meta( $this->term_id, self::META_KEY );
	}


	public function remove_entity_by_id( $entity_id ) {
		$entity_list = get_term_meta( $this->term_id, self::META_KEY );
		foreach ( $entity_list as $key => $entity ) {
			$same_as = $entity['sameAs'];
			if ( in_array( $entity_id, $same_as ) ) {
				// since entity ids are unique, we break after finding the first instance.
				unset( $entity_list[ $key ] );
				break;
			}
		}
		$this->clear_and_save_list( $entity_list );

	}

	/**
	 * @param $entity_list
	 */
	private function save_entity_list( $entity_list ) {
		foreach ( $entity_list as $single_entity ) {
			add_term_meta( $this->term_id, self::META_KEY, $single_entity );
		}
	}

	/**
	 * @param $entity_list
	 */
	private function clear_and_save_list( $entity_list ) {
		// Clear all data and add the new one.
		$this->clear_data();
		$this->save_entity_list( $entity_list );
	}
}