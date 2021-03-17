<?php
/**
 * @since 1.0.0
 * @author Akshay Raje <akshay@wordlift.io>
 */

namespace Wordlift\Vocabulary\Jsonld;

use Wordlift\Vocabulary\Api\Entity_Rest_Endpoint;

class Post_Jsonld {

	public function enhance_post_jsonld(){
		add_filter( 'wl_post_jsonld_array', array( $this, 'wl_post_jsonld_array' ), 11, 2 );
		add_filter( 'wl_after_get_jsonld', array( $this, 'wl_after_get_jsonld' ), 11, 2 );
	}

	public function wl_post_jsonld_array($arr, $post_id){

		$jsonld     = $arr['jsonld'];
		$references = $arr['references'];

		$this->add_mentions($post_id, $jsonld, $references);

		return array(
			'jsonld'     => $jsonld,
			'references' => $references
		);

	}

	public function add_mentions( $post_id, &$jsonld, &$references ) {

		$tags = get_the_tags( $post_id );

		if ( $tags && ! is_wp_error( $tags ) ) {
			// Loop through the tags and push it to references.
			foreach ( $tags as $tag ) {

				$tag_is_external_entity = get_term_meta( $tag->term_id, Entity_Rest_Endpoint::EXTERNAL_ENTITY_META_KEY, true );

				if($tag_is_external_entity == 1){
					$jsonld['mentions'][] = array(
						'@id'           => get_term_link( $tag->term_id ) . '#id',
						'@type'         => get_term_meta( $tag->term_id, Entity_Rest_Endpoint::TYPE_META_KEY, true ),
						'name'          => $tag->name,
						'description'   => !empty($tag->description) ?: get_term_meta( $tag->term_id, Entity_Rest_Endpoint::DESCRIPTION_META_KEY, true ),
						'sameAs'        => get_term_meta( $tag->term_id, Entity_Rest_Endpoint::SAME_AS_META_KEY ),
						'alternateName' => get_term_meta( $tag->term_id, Entity_Rest_Endpoint::ALTERNATIVE_LABEL_META_KEY )
					);
				}

			}
		}

	}

	public function wl_after_get_jsonld($jsonld, $post_id){

		if ( ! is_array( $jsonld ) || count( $jsonld ) === 0 ) {
			return $jsonld;
		}

		foreach ( $jsonld as $key => $value ) {
			if($value['@type'] === 'Article' && isset($value['image'])){
				$image = $value['image'];
			}
			if($value['@type'] === 'Recipe' && !isset($value['image'])){
				$index = $key;
			}
		}

		if(isset($index) && !empty($image)){
			$jsonld[$index]['image'] = $image;
		}

		return $jsonld;

	}

}
