<?php

namespace Wordlift\Block_Editor;

class Classification_Block_Type {

	public function __construct() {

		add_action( 'init', function () {
			register_block_type( 'wordlift/classification', array(
//				'title'           => __( 'WordLift Classification', 'wordlift' ),
//				'description'     => __( 'A block holding the classification data for the current post.', 'wordlift' ),
				'attributes'      => array(
					'entities' => array( 'type' => 'array' ),
				),
				'render_callback' => function ( $attributes ) {
					return 'Hello World!';
				},
			) );
		} );

		/**
		 *
		 * // Registering my block with a unique name
		 * registerBlockType("wordlift/classification", {
		 * title: __("WordLift Classification", "wordlift"),
		 * description: __("A block holding the classification data for the current post.", "wordlift"),
		 * category: "wordlift",
		 * attributes: {
		 * entities: {
		 * type: "array"
		 * }
		 * },
		 * supports: {
		 * // Do not support HTML editing.
		 * html: false,
		 * // Only support being inserted programmatically.
		 * inserter: false,
		 * // Only allow one block.
		 * multiple: false,
		 * // Do not allow reusability.
		 * reusable: false
		 * },
		 * edit: () => <div>WordLift Classification (edit)</div>,
		 * save: () => null
		 * });
		 */

	}

}
