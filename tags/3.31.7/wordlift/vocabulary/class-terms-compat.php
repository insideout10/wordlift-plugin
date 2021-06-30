<?php
namespace Wordlift\Vocabulary;

class Terms_Compat {

	public static function get_terms( $taxonomy, $args_with_taxonomy_key ) {
		global $wp_version;

		if ( version_compare( $wp_version, '4.5', '<' ) ) {
			return get_terms( $taxonomy, $args_with_taxonomy_key );
		} else {
			$args_with_taxonomy_key['taxonomy'] = $taxonomy;
			return get_terms( $args_with_taxonomy_key );
		}
	}

	public static function get_public_taxonomies() {
		return get_taxonomies( array( 'public' => true ) );
	}


}