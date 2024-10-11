<?php
/**
 * This class adds the terms associated with the posts to the posts.
 *
 * @since 3.32.3
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Vocabulary_Terms\Jsonld;

use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Term_Content_Service;
use Wordlift\Relation\Relation;
use Wordlift\Relation\Relations;
use WP_Taxonomy;
use WP_Term;

class Post_Jsonld {

	public function init() {
		add_filter( 'wl_post_jsonld_array', array( $this, 'wl_post_jsonld_array' ), 10, 2 );
	}

	public function wl_post_jsonld_array( $data, $post_id ) {

		$relations = $data['relations'] ?? null;

		if ( ! is_a( $relations, 'Wordlift\Relation\Relations' ) ) {
			return $data;
		}

		$term_relations = $this->get_term_relations( $post_id );

		if ( ! $term_relations ) {
			return $data;
		}

		$references       = $data['references'];
		$jsonld           = $data['jsonld'];
		$references_infos = $data['references_infos'];

		$relations->add( ...$term_relations );
		$term_mentions = $this->get_term_mentions( $term_relations );

		if ( count( $term_mentions ) > 0 ) {
			$existing_mentions  = array_key_exists( 'mentions', $jsonld ) ? $jsonld['mentions'] : array();
			$jsonld['mentions'] = array_merge( $existing_mentions, $term_mentions );
		}

		return array(
			'jsonld'           => $jsonld,
			'references'       => $references,
			'references_infos' => $references_infos,
			'relations'        => $relations,
		);
	}

	/**
	 * @param $post_id
	 * @param $relations Relations
	 *
	 * @return array<Relation> Returns a list of term relations.
	 */
	private function get_term_relations( $post_id ) {

		/** @var WP_Taxonomy[] $taxonomies_for_post */
		$taxonomies_for_post = get_object_taxonomies( get_post_type( $post_id ), 'objects' );

		// now we need to collect all terms attached to this post.
		$terms = array();

		foreach ( $taxonomies_for_post as $taxonomy ) {
			// Please note that `$taxonomy->publicly_queryable is only WP 4.7+
			if ( 'wl_entity_type' === $taxonomy->name || ! $taxonomy->public ) {
				continue;
			}

			$taxonomy_terms = get_the_terms( $post_id, $taxonomy->name );
			if ( is_array( $taxonomy_terms ) ) {
				$terms = array_merge( $terms, $taxonomy_terms );
			}
		}

		// Convert everything to the Term Reference.
		return array_filter(
			array_map(
				function ( $term ) use ( $post_id ) {

					if ( 1 === $term->term_id ) {
						return false;
					}

					/**
					 * @var WP_Term $term
					 */
					if ( Wordpress_Term_Content_Service::get_instance()
													  ->get_entity_id( Wordpress_Content_Id::create_term( $term->term_id ) )
					) {
						return new Relation(
							Wordpress_Content_Id::create_post( $post_id ),
							Wordpress_Content_Id::create_term( $term->term_id ),
							WL_WHAT_RELATION
						);
					}

					return false;
				},
				$terms
			)
		);

	}

	/**
	 * @param $term_relations array<Relation>
	 *
	 * @return array
	 */
	private function get_term_mentions( $term_relations ) {

		return array_map(
			function ( $term_relation ) {
				return array(
					'@id' => Wordpress_Term_Content_Service::get_instance()
															  ->get_entity_id( $term_relation->get_object() ),
				);
			},
			$term_relations
		);

	}

}
