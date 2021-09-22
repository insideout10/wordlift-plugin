<?php
/**
 * This class adds the terms associated with the posts to the posts.
 * @since 3.32.3
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Vocabulary_Terms\Jsonld;

use Wordlift\Jsonld\Term_Reference;

class Post_Jsonld {

	public function init() {
		add_filter( 'wl_post_jsonld_array', array( $this, 'wl_post_jsonld_array' ), 10, 2 );
	}

	public function wl_post_jsonld_array( $data, $post_id ) {

		$term_references = $this->get_term_references( $post_id );

		if ( ! $term_references ) {
			return $data;
		}

		$references         = $data['references'];
		$jsonld             = $data['jsonld'];

		$jsonld['mentions'] = $this->append_term_mentions( $jsonld, $term_references );

		return array(
			'jsonld'     => $jsonld,
			'references' => array_merge( $references, $term_references )
		);
	}

	/**
	 * @param $post_id
	 *
	 * @return array Returns a list of term references, Returns empty array if none found.
	 */
	private function get_term_references( $post_id ) {

		$taxonomies_for_post = get_object_taxonomies( get_post_type( $post_id ) );

		// now we need to collect all terms attached to this post.
		$terms = array();

		foreach ( $taxonomies_for_post as $taxonomy ) {
			$taxonomy_terms = get_the_terms( $post_id, $taxonomy );
			if ( is_array( $taxonomy_terms ) ) {
				$terms = array_merge( $terms, $taxonomy_terms );
			}
		}

		// Convert everything to the Term Reference.
		return array_filter( array_map( function ( $term ) {
			/**
			 * @var \WP_Term $term
			 */
			if ( wl_get_term_entity_uri( $term->term_id ) ) {
				return new Term_Reference( $term->term_id );
			}

			return false;
		}, $terms ) );

	}

	/**
	 * @param $jsonld array
	 * @param $term_references array<Term_Reference>
	 */
	private function append_term_mentions( $jsonld, $term_references ) {

		$existing_mentions = array_key_exists( 'mentions', $jsonld ) ? $jsonld['mentions'] : array();


		$term_mentions = array_map( function ( $term_reference ) {
			return array(
				'@id' => wl_get_term_entity_uri( $term_reference->get_id() )
			);
		}, $term_references );

		return array_merge( $existing_mentions, $term_mentions );
	}

}