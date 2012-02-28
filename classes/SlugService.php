<?php

class SlugService {
	
	function get_slug( &$entity ) {

		$matches = array();
		$results = preg_match( '/.*\/(.*)/i', $entity->get_id(), $matches );

		return sanitize_title( $matches[1] );
	}

	function get_slugs_by_terms( &$terms ) {

		if (false == is_array($terms)) return;

		$slugs = array();
		foreach ($terms as $term) {
			$slugs[] = $term->slug;
		}

		return $slugs;
	}
}

?>