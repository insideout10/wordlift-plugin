<?php
/**
 * @since 1.0.0
 * @author Akshay Raje <akshay@wordlift.io>
 */

namespace Wordlift\Vocabulary\Jsonld;

use Wordlift\Api\Default_Api_Service;
use Wordlift\Jsonld\Jsonld_Context_Enum;
use Wordlift\Vocabulary\Api\Entity_Rest_Endpoint;
use Wordlift\Vocabulary\Terms_Compat;

/**
 * Class Post_Jsonld
 *
 * @package Wordlift\Vocabulary\Jsonld
 */
class Post_Jsonld {
	/**
	 * The {@link Api_Service} used to communicate with the remote APIs.
	 *
	 * @access private
	 * @var Default_Api_Service
	 */
	private $api_service;

	/**
	 * Enhance post jsonld.
	 */
	public function enhance_post_jsonld() {
		$this->api_service = Default_Api_Service::get_instance();

		add_filter( 'wl_post_jsonld_array', array( $this, 'wl_post_jsonld_array' ), 11, 2 );
		add_filter( 'wl_after_get_jsonld', array( $this, 'wl_after_get_jsonld' ), 11 );
		add_filter( 'wl_after_get_jsonld', array( $this, 'set_events_request' ), 90, 3 );
	}

	/**
	 * Wl post jsonld array.
	 *
	 * @param $arr
	 * @param $post_id
	 *
	 * @return array
	 */
	public function wl_post_jsonld_array( $arr, $post_id ) {
		$jsonld     = $arr['jsonld'];
		$references = $arr['references'];

		$this->add_mentions( $post_id, $jsonld );

		return array(
			'jsonld'     => $jsonld,
			'references' => $references,
		);
	}

	/**
	 * Add mentions.
	 *
	 * @param $post_id
	 * @param $jsonld
	 */
	public function add_mentions( $post_id, &$jsonld ) {

		$taxonomies = Terms_Compat::get_public_taxonomies();
		$terms      = array();

		foreach ( $taxonomies as $taxonomy ) {
			$taxonomy_terms = get_the_terms( $post_id, $taxonomy );
			if ( ! $taxonomy_terms ) {
				continue;
			}
			$terms = array_merge( $taxonomy_terms, $terms );
		}

		if ( ! $terms ) {
			return;
		}

		if ( ! array_key_exists( 'mentions', $jsonld ) && count( $terms ) > 0 ) {
			$jsonld['mentions'] = array();
		}

		foreach ( $terms as $term ) {

			$is_matched = intval( get_term_meta( $term->term_id, Entity_Rest_Endpoint::IGNORE_TAG_FROM_LISTING, true ) ) === 1;

			if ( ! $is_matched ) {
				continue;
			}

			$entities = Jsonld_Utils::get_matched_entities_for_term( $term->term_id );

			if ( count( $entities ) === 0 ) {
				continue;
			}

			$add_additional_attrs = self::add_additional_attrs( $term, $entities );

			$jsonld['mentions'] = array_merge( $jsonld['mentions'], $add_additional_attrs );
		}

	}

	/**
	 * @param $term \WP_Term
	 * @param $entities
	 *
	 * @return array
	 */
	public static function add_additional_attrs( $term, $entities ) {

		return array_map(
			function ( $entity ) use ( $term ) {
				$entity['@id'] = get_term_link( $term->term_id ) . '#id';
				if ( ! empty( $term->description ) ) {
					$entity['description'] = $term->description;
				}

				return $entity;

			},
			$entities
		);

	}

	/**
	 * Wl after get jsonld.
	 *
	 * @param $jsonld
	 *
	 * @return array|mixed
	 */
	public function wl_after_get_jsonld( $jsonld ) {

		if ( ! is_array( $jsonld ) || count( $jsonld ) === 0 ) {
			return $jsonld;
		}

		foreach ( $jsonld as $key => $value ) {
			if ( 'Article' === $value['@type'] && isset( $value['image'] ) ) {
				$image = $value['image'];
			}
			if ( 'Recipe' === $value['@type'] && ! isset( $value['image'] ) ) {
				$index = $key;
			}
		}

		if ( isset( $index ) && ! empty( $image ) ) {
			$jsonld[ $index ]['image'] = $image;
		}

		return $jsonld;
	}


	/**
	 * Set events request.
	 *
	 * @param $jsonld_arr array The final jsonld before outputting to page.
	 * @param $post_id int The post id for which the jsonld is generated.
	 * @param $context int A context for the JSON-LD generation, valid values in Jsonld_Context_Enum
	 */
	public function set_events_request( $jsonld_arr, $post_id, $context ) {
		// If context is not PAGE or the array is empty, return early.
		if ( Jsonld_Context_Enum::PAGE !== $context || empty( $jsonld_arr[0] ) ) {
			return;
		}

		// Flag to indicate if we should make an API request.
		$change_status = false;

		// Get data from the array.
		$data = $jsonld_arr[0];

		// Fetch the initial 'about' and 'mentions' counts from post meta.
		$counts = array(
			'about'    => get_post_meta( $post_id, 'wl_about_count', true ) ? : 0,
			'mentions' => get_post_meta( $post_id, 'wl_mentions_count', true ) ? : 0,
		);

		// Iterate over the counts array.
		foreach ( $counts as $key => $count ) {
			// Check if data has 'about' or 'mentions' and the count is different from the existing meta value.
			if ( ! empty( $data[ $key ] ) ) {
				$new_count = count( $data[ $key ] );
				if ( $count !== $new_count ) {
					// Set flag to true if counts have changed.
					$change_status = true;

					// Update the counts array with new count.
					$counts[ $key ] = $new_count;

					// Update post meta with new count.
					update_post_meta( $post_id, 'wl_' . $key . '_count', $new_count );
				}
			}
		}

		// If the count has changed, make the API request.
		if ( $change_status ) {
			$this->api_service->request(
				'POST',
				'/plugin/events',
				array( 'Content-Type' => 'application/json' ),
				wp_json_encode( array(
					'source' => 'jsonld',
					'args'   => array(
						array( 'about_count' => $counts['about'] ),
						array( 'mentions_count' => $counts['mentions'] ),
					),
					'url'    => get_permalink( $post_id ),
				) ),
				0.001,
				null,
				array( 'blocking' => false )
			);
		}
	}
}
