<?php

namespace Wordlift\Dataset;

use Wordlift\Object_Type_Enum;

class Sync_Hooks_Wordpress_Ontology {

	public function __construct() {

		add_filter( 'wl_dataset__sync_service__sync_item__jsonld', array( $this, 'jsonld' ), 10, 3 );
	}

	public function jsonld( $jsonld, $type, $object_id ) {

		$jsonld[0]['http://purl.org/wordpress/1.0/id'] = $object_id;

		switch ( $type ) {
			case Object_Type_Enum::TERM:
				$jsonld[0]['http://purl.org/wordpress/1.0/contentType'] = 'term';
				break;
			case Object_Type_Enum::USER:
				$jsonld[0]['http://purl.org/wordpress/1.0/contentType'] = 'user';
				break;
			case Object_Type_Enum::POST:
				$jsonld[0]['http://purl.org/wordpress/1.0/contentType'] = 'post';
				$jsonld[0]['http://purl.org/wordpress/1.0/postType']    = get_post_type( $object_id );
				break;
			default:
		}

		return $jsonld;
	}

}
