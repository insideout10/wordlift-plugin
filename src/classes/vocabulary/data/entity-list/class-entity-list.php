<?php
namespace Wordlift\Vocabulary\Data\Entity_List;

/**
 * @since 3.30.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

abstract class Entity_List {
	/**
	 * @var int
	 */
	protected $term_id;

	/**
	 * Entity constructor.
	 *
	 * @param $term_id int
	 */
	public function __construct( $term_id ) {
		$this->term_id = $term_id;
	}

	/**
	 * Return a structure of jsonld data.
	 *
	 * @return array
	 */
	abstract  public function get_jsonld_data();

	/**
	 * @param $entity_data array
	 *
	 * @return bool
	 */
	abstract  public function save_jsonld_data( $entity_data );

	/**
	 * Clear the data on the meta.
	 *
	 * @return bool
	 */
	abstract public function clear_data();

	abstract public function remove_entity_by_id( $entity_id );
}
