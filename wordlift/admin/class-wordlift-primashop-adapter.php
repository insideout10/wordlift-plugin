<?php

/**
 * A class that provides compatibility with PrimaShop, i.e. displays the Header options on Entity edit pages.
 *
 * @since 3.2.3
 */
class Wordlift_PrimaShop_Adapter {

	/**
	 * Create a Wordlift_PrimaShop_Adapter instance.
	 *
	 * @since 3.2.3
	 */
	public function __construct() {

		// Tell WP (and PrimaShop) that we support the *prima-layout-settings*. This will display the Content Settings
		// in the entity edit page.
		add_post_type_support( Wordlift_Entity_Service::TYPE_NAME, 'prima-layout-settings' );

	}

	/**
	 * Intercept the <em>prima_metabox_entity_header_args</em> filter and return what a call to the related <em>post</em>
	 * would have returned.
	 *
	 * @since 3.2.3
	 *
	 * @param array $meta The meta array.
	 *
	 * @return array A meta array.
	 */
	public function prima_metabox_entity_header_args( $meta ) {

		return apply_filters( 'prima_metabox_post_header_args', $meta, 'post' );
	}

}
