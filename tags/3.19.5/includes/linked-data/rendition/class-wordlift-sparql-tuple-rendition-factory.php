<?php
/**
 * Factories: Rendition Factory.
 *
 * A factory which creates renditions which translate source contents into output
 * statements (JSON-LD or SPARQL).
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/linked-data/rendition
 */

/**
 * Define the {@link Wordlift_Sparql_Tuple_Rendition_Factory} class.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/linked-data/rendition
 */
class Wordlift_Sparql_Tuple_Rendition_Factory {

	/**
	 * The {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * The {@link Wordlift_Sparql_Tuple_Rendition_Factory} singleton instance.
	 *
	 * @since 3.19.1
	 * @access private
	 * @var \Wordlift_Sparql_Tuple_Rendition_Factory $instance The {@link Wordlift_Sparql_Tuple_Rendition_Factory} singleton instance.
	 */
	private static $instance;

	/**
	 * Create a a {@link Wordlift_Sparql_Tuple_Rendition_Factory} instance.
	 *
	 * @since 3.15.0
	 *
	 * @param \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} instance.
	 */
	public function __construct( $entity_service ) {

		$this->entity_service = $entity_service;

		self::$instance = $this;

	}

	/**
	 * Get the {@link Wordlift_Sparql_Tuple_Rendition_Factory} singleton instance.
	 *
	 * @since 3.19.1
	 *
	 * @return \Wordlift_Sparql_Tuple_Rendition_Factory The {@link Wordlift_Sparql_Tuple_Rendition_Factory} singleton instance.
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Create a rendition.
	 *
	 * @since 3.15.0
	 *
	 * @param \Wordlift_Storage $storage A {@link Wordlift_Storage} instance.
	 * @param string $predicate A predicate (e.g. `http://schema.org/name`).
	 * @param string|null $data_type A data-type.
	 * @param string|null $language A language code (e.g. `en`).
	 * @param string|null $uri_suffix The URI suffix.
	 *
	 * @return \Wordlift_Default_Sparql_Tuple_Rendition A {@link Wordlift_Default_Sparql_Tuple_Rendition} instance.
	 */
	public function create( $storage, $predicate, $data_type = null, $language = null, $uri_suffix = null ) {

		return new Wordlift_Default_Sparql_Tuple_Rendition( $this->entity_service, $storage, $predicate, $data_type, $language, $uri_suffix );
	}

	/**
	 * Create an address rendition.
	 *
	 * @since 3.18.0
	 *
	 * @param \Wordlift_Storage $storage A {@link Wordlift_Storage} instance.
	 * @param string|null $language A language code (e.g. `en`).
	 *
	 * @return \Wordlift_Address_Sparql_Tuple_Rendition A {@link Wordlift_Address_Sparql_Tuple_Rendition} instance.
	 */
	public function create_address( $storage, $language = null ) {

		return new Wordlift_Address_Sparql_Tuple_Rendition( $this->entity_service, $this, $storage, $language );
	}
}
