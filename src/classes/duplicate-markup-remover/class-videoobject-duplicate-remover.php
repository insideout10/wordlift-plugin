<?php

namespace Wordlift\Duplicate_Markup_Remover;

/**
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.31.7
 * Class Videoobject_Duplicate_Remover
 * @package Wordlift\Duplicate_Markup_Remover
 */
class Videoobject_Duplicate_Remover {

	public function __construct() {
		add_filter( 'wl_after_get_jsonld', array( $this, 'wl_after_get_jsonld' ), 10 );
	}

	/**
	 * @param $jsonld array The final jsonld.
	 * @return array Filtered jsonld.
	 */
	public function wl_after_get_jsonld( $jsonld ) {

		if ( ! is_array( $jsonld )
			 || ! count( $jsonld ) > 1
			 || ! array_key_exists( 0, $jsonld ) ) {
			// Return early if there are no referenced entities.
			return $jsonld;
		}

		$post_jsonld = array_shift( $jsonld );

		// we need to loop through all the items and remove the faq markup.
		foreach ( $jsonld as $key => &$value ) {
			if ( ! array_key_exists( '@type', $value ) ) {
				continue;
			}
			$type = $value['@type'];

			if ( ( is_string( $type ) && 'Article' !== $type )
				 || ( is_array( $type ) && ! in_array( 'Article', $type, true ) ) ) {
				continue;
			}
			// Video doesnt exist, dont try to remove it.
			if ( ! array_key_exists( 'video', $value ) ) {
				continue;
			}
			unset( $jsonld[ $key ]['video'] );
		}

		// Add the post jsonld to front of jsonld array.
		array_unshift( $jsonld, $post_jsonld );

		return $jsonld;
	}

}
