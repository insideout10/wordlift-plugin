<?php
/**
 * This transform function will take the provided post ID and expand it to an entity if it exists.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.27.1
 * @package Wordlift\Mappings\Transforms
 */

namespace Wordlift\Mappings\Transforms;

use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;
use Wordlift\Mappings\Mappings_Transform_Function;

/**
 * Class Url_To_Entity_Transform_Function.
 *
 * @package Wordlift\Mappings\Transforms
 */
class Post_Id_To_Entity_Transform_Function implements Mappings_Transform_Function {

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
	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function transform_data( $data, $jsonld, &$references, $post_id ) {

		$ret_val = array();
		foreach ( (array) $data as $target_post_id ) {

			// We may receive a `WP_Post` here, in which case we want the ID.
			if ( is_a( $target_post_id, '\WP_Post' ) ) {
				$target_post_id = $target_post_id->ID;
			}

			// We need a numeric post ID.
			if ( ! is_numeric( $target_post_id ) ) {

				$class_name = is_object( $target_post_id ) ? get_class( $target_post_id ) : 'unknown, maybe a string';

				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				@header( "X-Post-Id-To-Entity-Transform-Function: $class_name is not numeric." );

				continue;
			}

			// Get the entity by URI.
			$entity_url = Wordpress_Content_Service::get_instance()
												   ->get_entity_id( Wordpress_Content_Id::create_post( $target_post_id ) );

			// No entity URL.
			if ( empty( $entity_url ) ) {
				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				@header( "X-Post-Id-To-Entity-Transform-Function: entity url for $data not found." );

				continue;
			}

			// Add the entity among the references using the post ID.
			$references[] = (int) $target_post_id;

			$ret_val[] = array( '@id' => $entity_url );
		}

		return $ret_val;
	}

}
