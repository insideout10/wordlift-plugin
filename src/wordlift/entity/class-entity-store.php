<?php
/**
 * This file defines the Entity_Store class.
 *
 * The Entity_Store class is responsible for creating and updating entities.
 *
 * The Entity_Store class may replace the {@link Wordlift_Entity_Service}.
 *
 * @see https://github.com/insideout10/wordlift-plugin/issues/944
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 *
 * @package Wordlift
 * @subpackage Wordlift\Entity
 */

namespace Wordlift\Entity;

use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;
use Wordlift_Entity_Service;

/**
 * Entity_Store class definition.
 *
 * The Entity Store class provides the method to create and update entities.
 *
 * The Entity Store class is available as a singleton instance using {@link get_instance}.
 *
 * @package Wordlift
 * @subpackage Wordlift\Entity
 */
class Entity_Store {

	protected function __construct() {
	}

	private static $instance = null;

	/**
	 * Get the Entity_Store singleton, lazily initialized.
	 *
	 * @return Entity_Store The singleton.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Entity_Store();
		}

		return self::$instance;
	}

	/**
	 * Create and persist an entity.
	 *
	 * @param array  $params {
	 *       The entity parameters.
	 *
	 * @type string|array $labels A label, or an array of labels. The first label is set as post title.
	 * @type string $description The entity description, stored in the post content.
	 * @type string|array $same_as One or more entity URIs, stored in the sameAs post meta.
	 * }
	 *
	 * @param string $post_status The post status, by default `draft`.
	 *
	 * @return int|\WP_Error
	 * @throws \Exception when an error occurs.
	 */
	public function create( $params, $post_status = 'draft' ) {

		$args = wp_parse_args(
			$params,
			array(
				'labels'      => array(),
				'description' => '',
				'same_as'     => array(),
			)
		);

		// Use the first label as `post_title`.
		$labels = (array) $args['labels'];
		$label  = array_shift( $labels );

		$post_id = wp_insert_post(
			array(
				'post_type'    => Wordlift_Entity_Service::TYPE_NAME,
				'post_status'  => $post_status,
				'post_title'   => $label,
				'post_name'    => sanitize_title( $label ),
				'post_content' => $args['description'],
			)
		);

		// Bail out if we've got an error.
		if ( empty( $post_id ) || is_wp_error( $post_id ) ) {
			throw new \Exception( 'An error occurred while creating an entity.' );
		}

		Wordlift_Entity_Service::get_instance()
							   ->set_alternative_labels( $post_id, array_diff( $labels, array( $label ) ) );
		$this->merge_post_meta(
			$post_id,
			\Wordlift_Schema_Service::FIELD_SAME_AS,
			(array) $args['same_as'],
			(array) Wordpress_Content_Service::get_instance()->get_entity_id( Wordpress_Content_Id::create_post( $post_id ) )
		);

		return $post_id;
	}

	/**
	 * Update an entity.
	 *
	 * @param array $params {
	 *
	 * @type int $ID The post ID.
	 * @type string|array One or more labels to add to the synonyms.
	 * @type string|array One or more URIs to add to the sameAs.
	 * }
	 *
	 * @return int The post id.
	 */
	public function update( $params ) {

		$args = wp_parse_args(
			$params,
			array(
				'ID'      => 0,
				'labels'  => array(),
				'same_as' => array(),
			)
		);

		$post_id = $args['ID'];

		// Save synonyms except title.
		Wordlift_Entity_Service::get_instance()
							   ->append_alternative_labels( $post_id, array_diff( (array) $args['labels'], array( get_the_title( $post_id ) ) ) );

		$this->merge_post_meta(
			$post_id,
			\Wordlift_Schema_Service::FIELD_SAME_AS,
			(array) $args['same_as'],
			(array) Wordpress_Content_Service::get_instance()->get_entity_id( Wordpress_Content_Id::create_post( $post_id ) )
		);

		return $post_id;
	}

	/**
	 * Merge the post meta.
	 *
	 * @param int          $post_id The post ID.
	 * @param string       $meta_key The post meta key.
	 * @param string|array $values One or more values to add.
	 * @param string|array $exclusions An additional list of values to exclude.
	 */
	private function merge_post_meta( $post_id, $meta_key, $values, $exclusions = array() ) {

		$existing = array_merge(
			(array) $exclusions,
			get_post_meta( $post_id, $meta_key )
		);

		foreach ( (array) $values as $value ) {
			// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			if ( ! in_array( $value, $existing ) ) {
				add_post_meta( $post_id, $meta_key, $value );
			}
		}
	}

}
