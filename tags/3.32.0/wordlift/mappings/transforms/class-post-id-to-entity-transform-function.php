<?php
/**
 * This transform function will take the provided post ID and expand it to an entity if it exists.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.27.1
 * @package Wordlift\Mappings\Transforms
 */

namespace Wordlift\Mappings\Transforms;

use Wordlift\Mappings\Mappings_Transform_Function;

/**
 * Class Url_To_Entity_Transform_Function.
 *
 * @package Wordlift\Mappings\Transforms
 */
class Post_Id_To_Entity_Transform_Function implements Mappings_Transform_Function {

	/**
	 */
	public function __construct() {

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

		return 'post_id_to_entity';
	}

	/**
	 * @inheritDoc
	 */
	public function get_label() {

		return __( 'Post ID to Entity', 'wordlift' );
	}

	/**
	 * @inheritDoc
	 */
	public function transform_data( $data, $jsonld, &$references, $post_id ) {

		$ret_val = array();
		foreach ( (array) $data as $target_post_id ) {

			// We need a numeric post ID.
			if ( ! is_numeric( $target_post_id ) ) {

				@header( "X-Post-Id-To-Entity-Transform-Function: $target_post_id not numeric." );

				continue;
			}

			// Get the entity by URI.
			$entity_url = get_post_meta( $target_post_id, 'entity_url', true );

			// No entity URL.
			if ( empty( $entity_url ) ) {
				@header( "X-Post-Id-To-Entity-Transform-Function: entity url for $data not found." );

				continue;
			}

			// Add the entity among the references using the post ID.
			$references[] = (int) $target_post_id;

			$ret_val[] = array( "@id" => $entity_url, );
		}

		return $ret_val;
	}

}
