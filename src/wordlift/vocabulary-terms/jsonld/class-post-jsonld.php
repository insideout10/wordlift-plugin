<?php
/**
 * This class adds the terms associated with the posts to the posts.
 *
 * @since 3.32.3
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Vocabulary_Terms\Jsonld;

use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Term_Content_Legacy_Service;
use Wordlift\Relation\Relation;
use Wordlift\Relation\Relations;
use WP_Taxonomy;
use WP_Term;

class Post_Jsonld {

	public function init() {
		add_filter( 'wl_post_jsonld_array', array( $this, 'wl_post_jsonld_array' ), 10, 2 );
	}

	public function wl_post_jsonld_array( $data, $post_id ) {

		$term_relations = $this->get_term_relations( $post_id );

		if ( ! $term_relations ) {
			return $data;
		}

		$references       = $data['references'];
		$jsonld           = $data['jsonld'];
		$references_infos = $data['references_infos'];
		/**
		 * @var $relations Relations
		 */
		$relations = $data['relations'];
		$relations->add( ...$term_relations );

		$jsonld['mentions'] = $this->append_term_mentions( $jsonld, $term_relations );

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
					/**
					 * @var WP_Term $term
					 */
					if ( Wordpress_Term_Content_Legacy_Service::get_instance()
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
	 * @param $jsonld array
	 * @param $term_references array<Relation>
	 */
	private function append_term_mentions( $jsonld, $term_references ) {

		$existing_mentions = array_key_exists( 'mentions', $jsonld ) ? $jsonld['mentions'] : array();

		$term_mentions = array_map(
			function ( $term_reference ) {
				return array(
					'@id' => Wordpress_Term_Content_Legacy_Service::get_instance()
															  ->get_entity_id( Wordpress_Content_Id::create_term( $term_reference->get_object()->get_id() ) ),
				);
			},
			$term_references
		);

		return array_merge( $existing_mentions, $term_mentions );
	}

}
