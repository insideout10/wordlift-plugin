<?php
/**
 * This transform function will take the provided URL and expand it to an entity if it exists.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.25.0
 * @package Wordlift\Mappings\Transforms
 */

namespace Wordlift\Mappings\Transforms;

use Wordlift\Mappings\Mappings_Transform_Function;
use Wordlift_Entity_Uri_Service;

/**
 * Class Url_To_Entity_Transform_Function.
 *
 * @package Wordlift\Mappings\Transforms
 */
class Url_To_Entity_Transform_Function implements Mappings_Transform_Function {

	/**
	 * The {@link Wordlift_Entity_Uri_Service} instance.
	 *
	 * @var Wordlift_Entity_Uri_Service $entity_uri_service The {@link Wordlift_Entity_Uri_Service} instance.
	 */
	private $entity_uri_service;

	/**
	 * Url_To_Entity_Transform_Function constructor.
	 *
	 * @param Wordlift_Entity_Uri_Service $entity_uri_service The {@link Wordlift_Entity_Uri_Service} instance.
	 */
	public function __construct( $entity_uri_service ) {

		$this->entity_uri_service = $entity_uri_service;

		add_filter( 'wl_mappings_transformation_functions', array( $this, 'wl_mappings_transformation_functions' ) );

	}

	/**
	 * Hook to add ourselves to the list of available transform functions.
	 *
	 * @param Mappings_Transform_Function[] $value An array of {@link Mappings_Transform_Function}s.
	 *
	 * @return Mappings_Transform_Function[] An updated array with ourselves too.
	 */
	public function wl_mappings_transformation_functions( $value ) {

		$value[] = $this;

		return $value;
	}

	/**
	 * @inheritDoc
	 */
	public function get_name() {

		return 'url_to_entity';
	}

	/**
	 * @inheritDoc
	 */
	public function get_label() {

		return __( 'URL to Entity', 'wordlift' );
	}

	/**
	 * @inheritDoc
	 */
	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function transform_data( $data, $jsonld, &$references, $post_id ) {

		// Get the entity by URI.
		$post = $this->entity_uri_service->get_entity( $data );

		// If found, add the reference.
		if ( is_a( $post, 'WP_Post' ) ) {
			// Add the entity among the references using the post ID.
			$references[] = $post->ID;
		}

		return array( '@id' => $data );
	}

}
