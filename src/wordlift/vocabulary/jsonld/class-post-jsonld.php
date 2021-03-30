<?php
/**
 * @since 1.0.0
 * @author Akshay Raje <akshay@wordlift.io>
 */

namespace Wordlift\Vocabulary\Jsonld;

use Wordlift\Vocabulary\Api\Entity_Rest_Endpoint;
use Wordlift\Vocabulary\Data\Entity_List\Entity_Factory;

class Post_Jsonld {

	public function enhance_post_jsonld() {
		add_filter( 'wl_post_jsonld_array', array( $this, 'wl_post_jsonld_array' ), 11, 2 );
		add_filter( 'wl_after_get_jsonld', array( $this, 'wl_after_get_jsonld' ), 11, 2 );
	}

	public function wl_post_jsonld_array( $arr, $post_id ) {

		$jsonld     = $arr['jsonld'];
		$references = $arr['references'];

		$this->add_mentions( $post_id, $jsonld, $references );

		return array(
			'jsonld'     => $jsonld,
			'references' => $references
		);

	}

	public function add_mentions( $post_id, &$jsonld, &$references ) {

		$tags = get_the_tags( $post_id );

		if ( $tags && ! is_wp_error( $tags ) ) {

			if ( ! array_key_exists('mentions', $jsonld) ) {
				$jsonld['mentions'] = array();
			}

			// Loop through the tags and push it to references.
			foreach ( $tags as $tag ) {

				$is_matched = intval( get_term_meta( $tag->term_id, Entity_Rest_Endpoint::IGNORE_TAG_FROM_LISTING, true ) ) === 1;

				if ( $is_matched ) {

					$entity = Entity_Factory::get_instance( $tag->term_id );

					$jsonld['mentions'] = array_merge( $jsonld['mentions'], self::add_additional_attrs( $tag, $entity->get_jsonld_data() ) );
				}

			}
		}

	}

	/**
	 * @param $term \WP_Term
	 * @param $entities
	 *
	 * @return array
	 */
	public static function add_additional_attrs( $term, $entities ) {

		return array_map( function ( $entity ) use ( $term ) {
			var_dump($entity);
			$entity['@id'] = get_term_link( $term->term_id ) . '#id';
			if ( ! empty( $term->description ) ) {
				$entity['description'] = $term->description;
			}

			return $entity;

		}, $entities );

	}

	public function wl_after_get_jsonld( $jsonld, $post_id ) {

		if ( ! is_array( $jsonld ) || count( $jsonld ) === 0 ) {
			return $jsonld;
		}

		foreach ( $jsonld as $key => $value ) {
			if ( $value['@type'] === 'Article' && isset( $value['image'] ) ) {
				$image = $value['image'];
			}
			if ( $value['@type'] === 'Recipe' && ! isset( $value['image'] ) ) {
				$index = $key;
			}
		}

		if ( isset( $index ) && ! empty( $image ) ) {
			$jsonld[ $index ]['image'] = $image;
		}

		return $jsonld;

	}

}
