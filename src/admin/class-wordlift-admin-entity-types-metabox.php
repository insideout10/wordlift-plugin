<?php

class Wordlift_Admin_Entity_Types_Metabox {

	public function add_metaboxes() {

		/**
		 * @see https://developer.wordpress.org/reference/functions/add_meta_box/
		 */
		add_meta_box(
			'wl-entity-types',
			__( 'Entity Types', 'wordlift' ),
			array( $this, 'render' ),
			Wordlift_Entity_Service::valid_entity_post_types(),
			'side',
			'default'

		);
	}

	public function render() {
		echo '<div id="wl-schema-class-tree"></div>';
	}


}
