<?php
namespace Wordlift\Vocabulary;

class Terms_Compat {

	public static function get_terms( $taxonomy, $args_with_taxonomy_key ) {
		global $wp_version;

		if ( version_compare( $wp_version, '4.5', '<' ) ) {
			return get_terms( $taxonomy, $args_with_taxonomy_key );
		} else {
			return get_terms( $args_with_taxonomy_key );
		}
	}


}