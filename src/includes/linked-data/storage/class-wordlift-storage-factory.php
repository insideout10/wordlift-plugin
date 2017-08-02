<?php

class Wordlift_Storage_Factory {

	public function post_title() {

		return new Wordlift_Post_Property_Storage( Wordlift_Post_Property_Storage::TITLE );
	}

	public function post_description_no_tags_no_shortcodes() {

		return new Wordlift_Post_Property_Storage( Wordlift_Post_Property_Storage::DESCRIPTION_NO_TAGS_NO_SHORTCODES );
	}

	public function post_meta( $meta_key ) {

		return new Wordlift_Post_Meta_Storage( $meta_key );
	}

	public function schema_class( $schema_service ) {

		return new Wordlift_Post_Schema_Class_Storage( Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME, $schema_service );

	}

}
