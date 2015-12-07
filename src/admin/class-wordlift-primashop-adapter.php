<?php

/**
 * A class that provides compatibility with PrimaShop, i.e. displays the Header options on Entity edit pages.
 *
 * @since 3.2.3
 */
class Wordlift_PrimaShop_Adapter {

	/**
	 * Intercept the <em>prima_metabox_entity_header_args</em> filter and return what a call to the related <em>post</em>
	 * would have returned.
	 *
	 * @since 3.2.3
	 *
	 * @param array $meta The meta array.
	 * @param string $ype The post type.
	 *
	 * @return array A meta array.
	 */
	function prima_metabox_entity_header_args( $meta, $ype ) {

		return apply_filters( "prima_metabox_post_header_args", $meta, 'post' );
	}

}
