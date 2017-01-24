<?php


class WL_Metabox_Field_uri extends WL_Metabox_Field {

	/**
	 * Only accept URIs or local entity IDs.
	 * Build new entity if the user inputted a name that is not present in DB.
	 */
	public function sanitize_data_filter( $value ) {

		if ( empty( $value ) ) {
			return NULL;
		}

		// Check that the inserted URI, ID or name does not point to a saved entity.
		if ( is_numeric( $value ) ) {
			$absent_from_db = is_null( get_post( $value ) );                           // search by ID
		} else {
			$absent_from_db =
				is_null( Wordlift_Entity_Service::get_instance()
				                                ->get_entity_post_by_uri( $value ) ) &&                      // search by uri
				is_null( get_page_by_title( $value, OBJECT, Wordlift_Entity_Service::TYPE_NAME ) );   // search by name
		}

		// Is it an URI?
		$name_is_uri = strpos( $value, 'http' ) === 0;

		// We create a new entity only if the entity is not present in the DB.
		// In the case of an external uri, we just save the uri.
		if ( $absent_from_db && ! $name_is_uri ) {

			// ...we create a new entity!
			$new_entity_id = wp_insert_post( array(
				'post_status' => 'publish',
				'post_type'   => Wordlift_Entity_Service::TYPE_NAME,
				'post_title'  => $value
			) );
			$new_entity    = get_post( $new_entity_id );

			$type = 'http://schema.org/' . ( isset( $this->expected_uri_type ) ? $this->expected_uri_type[0] : 'Thing' );

			wl_set_entity_main_type( $new_entity_id, $type );

			// Build uri for this entity
			$new_uri = wl_build_entity_uri( $new_entity_id );
			wl_set_entity_uri( $new_entity_id, $new_uri );

			wl_push_entity_post_to_redlink( $new_entity );

			// Update the value that will be saved as meta
			$value = $new_entity_id;
		}

		return $value;
	}

	public function html_wrapper_open() {

		// The containing <div> contains info on cardinality and expected types
		$html = "<div class='wl-field' data-cardinality='$this->cardinality'";

		if ( isset( $this->expected_uri_type ) && ! is_null( $this->expected_uri_type ) ) {

			if ( is_array( $this->expected_uri_type ) ) {
				$html .= " data-expected-types='" . implode( ',', $this->expected_uri_type ) . "'";
			} else {
				$html .= " data-expected-types='$this->expected_uri_type'";
			}
		}

		$html .= '>';

		return $html;
	}

	public function html_input( $default_entity_identifier ) {

		if ( empty( $default_entity_identifier ) ) {
			$entity = NULL;
		} elseif ( is_numeric( $default_entity_identifier ) ) {
			$entity = get_post( $default_entity_identifier );
		} else {
			// @todo: we cannot be so sure this is a URI.
			// It is an URI
			$entity = Wordlift_Entity_Service::get_instance()
			                                 ->get_entity_post_by_uri( $default_entity_identifier );
		}

		if ( ! is_null( $entity ) ) {
			$label = $entity->post_title;
			$value = $entity->ID;
		} else {
			// No ID and no internal uri. Just leave as is.
			$label = $default_entity_identifier;
			$value = $default_entity_identifier;
		}

		// Write saved value in page
		// The <input> tags host the meta value.
		// The visible <input> has the human readable value (i.e. entity name or uri)
		// and is accompained by an hidden <input> tag, passed to the server,
		// that contains the raw value (i.e. the uri or entity id).
		$html = <<<EOF
			<div class="wl-input-wrapper wl-autocomplete-wrapper">
				<input type="text" class="$this->meta_name wl-autocomplete" value="$label" style="width:88%" />
				<input type="hidden" class="$this->meta_name" name="wl_metaboxes[$this->meta_name][]" value="$value" />
				<button class="button wl-remove-input wl-button" type="button" style="width:10%">Remove</button>
				<div class="wl-input-notice"></div>
			</div>		
EOF;

		return $html;
	}
}

