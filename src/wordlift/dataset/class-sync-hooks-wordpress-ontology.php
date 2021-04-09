<?php

namespace Wordlift\Dataset;

use Wordlift\Object_Type_Enum;

class Sync_Hooks_Wordpress_Ontology {

	const HTTP_PURL_ORG_WORDPRESS_1_0 = 'http://purl.org/wordpress/1.0/';

	public function __construct() {
		add_filter( 'wl_dataset__sync_service__sync_item__jsonld', array( $this, 'jsonld' ), 10, 3 );
	}

	public function jsonld( $jsonld, $type, $object_id ) {

		$jsonld[0][ self::HTTP_PURL_ORG_WORDPRESS_1_0 . 'id' ] = $object_id;

		switch ( $type ) {

			case Object_Type_Enum::TERM:
				$term = get_term( $object_id );

				$jsonld[0][ self::HTTP_PURL_ORG_WORDPRESS_1_0 . 'type' ]        = 'term';
				$jsonld[0][ self::HTTP_PURL_ORG_WORDPRESS_1_0 . 'name' ]        = $term->name;
				$jsonld[0][ self::HTTP_PURL_ORG_WORDPRESS_1_0 . 'description' ] = $term->description;
				break;

			case Object_Type_Enum::USER:
				$user = get_userdata( $object_id );

				$jsonld[0][ self::HTTP_PURL_ORG_WORDPRESS_1_0 . 'type' ]        = 'user';
				$jsonld[0][ self::HTTP_PURL_ORG_WORDPRESS_1_0 . 'displayName' ] = $user->display_name;
				break;

			case Object_Type_Enum::POST:
				$post = get_post( $object_id );

				$jsonld[0][ self::HTTP_PURL_ORG_WORDPRESS_1_0 . 'type' ]       = 'post';
				$jsonld[0][ self::HTTP_PURL_ORG_WORDPRESS_1_0 . 'customType' ] = $post->post_type;
				$jsonld[0][ self::HTTP_PURL_ORG_WORDPRESS_1_0 . 'title' ]      = $post->post_title;
				$jsonld[0][ self::HTTP_PURL_ORG_WORDPRESS_1_0 . 'status' ]     = $post->post_status;
				$content   = has_blocks( $post ) ? do_blocks( $post->post_content ) : do_shortcode( $post->post_content );
				$jsonld[0][ self::HTTP_PURL_ORG_WORDPRESS_1_0 . 'content' ]    = $content;
				break;

			default:

		}

		return $jsonld;
	}

}
