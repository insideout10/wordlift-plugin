<?php
namespace Wordlift\Vocabulary;

class Terms_Compat {

	public static function get_terms( $taxonomy, $args ) {
		global $wp_version;

		if ( version_compare( $wp_version, '4.5', '<' ) ) {
			return get_terms( $taxonomy, $args );
		} else {
			$args['taxonomy'] = $taxonomy;
			return get_terms( $args );
		}
	}

	public static function get_public_taxonomies() {
		return get_taxonomies( array( 'public' => true ) );
	}

}
