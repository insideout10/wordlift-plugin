<?php

namespace Wordlift\Vocabulary\Data\Entity_List;

/**
 * This class is created to support new multiple entity matches in db.
 *
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

	/**
	 * @param $values
	 *
	 * @return array
	 */
	private static function extract_jsonld_values( $values ) {
		return array_map(
			function ( $value ) {
				if ( ! is_array( $value ) || ! array_key_exists( '@value', $value ) ) {
					  return $value;
				}

				return $value['@value'];
			},
			$values
		);
	}

	/**
	 * Check if the key exists and value is array.
	 *
	 * @param $entity_data
	 *
	 * @return bool
	 */
	private static function is_value_array( $key, $entity_data ) {
		return array_key_exists( $key, $entity_data ) && is_array( $entity_data[ $key ] );
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

		$entity_id = $entity_data['@id'];

		if ( $entity_id ) {
			$entity_data['sameAs'] = array_merge( $entity_data['sameAs'], array( $entity_id ) );
		}

		$entity_list = get_term_meta( $this->term_id, self::META_KEY );

		$entity_list[] = $this->compact_jsonld( $this->filter_entity_data( $entity_data ) );

		$this->clear_and_save_list( $entity_list );

	}

	public function clear_data() {
		delete_term_meta( $this->term_id, self::META_KEY );
	}

	public function remove_entity_by_id( $entity_id ) {
		$entity_list = get_term_meta( $this->term_id, self::META_KEY );
		foreach ( $entity_list as $key => $entity ) {
			$same_as = $entity['sameAs'];
			if ( in_array( $entity_id, $same_as, true ) ) {
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

	/**
	 * For now support only these properties.
	 *
	 * @param $entity_data
	 *
	 * @return array
	 */
	private function filter_entity_data( $entity_data ) {
		$allowed_keys = array( '@id', 'description', 'sameAs', '@type', 'name' );
		$data         = array();
		foreach ( $entity_data as $key => $value ) {
			if ( in_array( $key, $allowed_keys, true ) ) {
				$data[ $key ] = $value;
			}
		}

		return $data;
	}

	public static function compact_jsonld( $entity_data ) {

		if ( self::is_value_array( '@type', $entity_data ) ) {
			$entity_data['@type'] = array_map(
				function ( $type ) {
					return str_replace( 'http://schema.org/', '', $type );
				},
				$entity_data['@type']
			);
		}

		if ( self::is_value_array( 'description', $entity_data ) ) {
			$entity_data['description'] = self::extract_jsonld_values( $entity_data['description'] );
		}

		if ( self::is_value_array( 'name', $entity_data ) ) {
			$entity_data['name'] = self::extract_jsonld_values( $entity_data['name'] );
		}

		if ( self::is_value_array( 'sameAs', $entity_data ) ) {
			$entity_data['sameAs'] = array_map(
				function ( $sameas ) {
					if ( ! is_array( $sameas ) || ! array_key_exists( '@id', $sameas ) ) {
						  return $sameas;
					}
					return $sameas['@id'];
				},
				$entity_data['sameAs']
			);
		}

		return $entity_data;
	}
}
