<?php
namespace Wordlift\Vocabulary\Data\Entity;
/**
 * @since 3.30.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

abstract class Entity {
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
	 * @return array
	 */
	abstract  public function get_jsonld_data();

	/**
	 * @param $request \WP_REST_Request
	 *
	 * @return bool
	 */
	abstract  public function save_jsonld_data( $request );

	/**
	 * Clear the data on the meta.
	 * @return bool
	 */
	abstract public function clear_data();

}