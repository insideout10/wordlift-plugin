<?php
/**
 * @since 1.0.0
 * @author Akshay Raje <akshay@wordlift.io>
 */

namespace Wordlift\Vocabulary\Jsonld;

use Wordlift\Vocabulary\Api\Entity_Rest_Endpoint;
use Wordlift\Vocabulary\Data\Entity_List\Entity_List_Factory;
use Wordlift\Vocabulary\Terms_Compat;

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

		$taxonomies = Terms_Compat::get_public_taxonomies();
		$terms      = array();

		foreach ( $taxonomies as $taxonomy ) {
			$taxonomy_terms = get_the_terms( $post_id, $taxonomy );
			if ( ! $taxonomy_terms ) {
				continue;
			}
			$terms = array_merge( $taxonomy_terms, $terms );
		}

		if ( ! $terms  ) {
			return;
		}

		if ( ! array_key_exists( 'mentions', $jsonld ) ) {
			$jsonld['mentions'] = array();
		}

		foreach ( $terms as $term ) {

			$is_matched = intval( get_term_meta( $term->term_id, Entity_Rest_Endpoint::IGNORE_TAG_FROM_LISTING, true ) ) === 1;

			if ( ! $is_matched ) {
				continue;
			}

			$entity = Entity_List_Factory::get_instance( $term->term_id );

			$entities = $entity->get_jsonld_data();

			if ( count( $entities ) === 0 ) {
				continue;
			}

			$jsonld['mentions'] = array_merge( $jsonld['mentions'], self::add_additional_attrs( $term, $entities ) );
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
