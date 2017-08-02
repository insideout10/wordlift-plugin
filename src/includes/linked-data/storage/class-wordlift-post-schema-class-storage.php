<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 01/08/2017
 * Time: 17:10
 */

class Wordlift_Post_Schema_Class_Storage extends Wordlift_Post_Taxonomy_Storage {

	/**
	 * @var \Wordlift_Schema_Service $schema_service
	 */
	private $schema_service;

	/**
	 * Wordlift_Post_Schema_Class_Storage constructor.
	 *
	 * @param                          $taxonomy
	 * @param \Wordlift_Schema_Service $schema_service
	 */
	public function __construct( $taxonomy, $schema_service ) {
		parent::__construct( $taxonomy );

		$this->schema_service = $schema_service;

	}

	public function get( $post_id ) {
		$terms = parent::get( $post_id );

		$schema_service = $this->schema_service;

		return array_map( function ( $item ) use ( $schema_service ) {
			$schema = $schema_service->get_schema( $item->slug );

			return $schema['uri'];
		}, $terms );

	}

}
