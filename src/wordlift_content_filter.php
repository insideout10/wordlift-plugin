<?php
/**
 * This file contains functions that filter the post content before it is showed on the frontend.
 * For "microdata compiling" we refer to the process used to insert schema.org markup into the text.
 */

/**
 * Build the regex to find a <span> tag relative to a specific uri
 *
 * @param string $uri Uri of the entity to search in the post content.
 */
function wl_content_embed_build_regex_from_uri( $uri ) {
	return '|<(\\w+)[^<]* itemid=\"' . esc_attr( $uri ) . '\"[^>]*>([^<]*)<\\/\\1>|i';
}

/**
 * Lift the post content with the microdata.
 *
 * @param string $content The post content.
 *
 * @return string The updated post content.
 */
function wl_content_embed_microdata( $content ) {

	// Apply microdata only to single pages.
	/*if ( ! is_single() ) {
		wl_write_log( "wl_content_embed_microdata : is not single" );

		return $content;
	}

	global $post;
        */
	return _wl_content_embed_microdata( get_the_ID(), $content );
}

/**
 * Lift the post content with the microdata (skipping the is_single check).
 *
 * @param int $post_id The post ID.
 * @param string $content The post content.
 *
 * @return string The updated post content.
 */
function _wl_content_embed_microdata( $post_id, $content ) {

	// If it is an entity, add its own microdata to the content.
	if ( get_post_type( $post_id ) == Wordlift_Entity_Service::TYPE_NAME ) {
		$own_uri = wl_get_entity_uri( $post_id );
		// Create a fake and randomic annotation id
		$annotation_id = uniqid( 'urn:' );		
		$content .= "<span id=\"$annotation_id\" class=\"textannotation disambiguated\" itemid=\"$own_uri\"></span>";
	}

	// Now search in the text entity mentions
	$regex = '/<(\\w+)[^<]* id=\"([^"]+)\" class=\"([^"]+)\" itemid=\"([^"]+)\"[^>]*>([^<]*)<\\/\\1>/i';
	
	$matches = array();

	// Return the content if not item IDs have been found.
	if ( FALSE === preg_match_all( $regex, $content, $matches, PREG_SET_ORDER ) ) {
		return $content;
	}
	
	// Retrieve item_ids removing deuplicates
	$item_ids = array_unique( array_map( function( $match ) {
		return $match[4];
	}, $matches ) );

	// Build annotations
	$annotations = array();
	foreach ( $matches as $match ) {
		$annotations[ $match[4] ][ $match[2] ] = preg_match( '/'. WL_BLIND_ANNOTATION_CSS_CLASS .'/', $match[3] );
	}

	// Embed microdata removing deuplicated item ids
	foreach ( array_unique( $item_ids ) as $item_id ) {
		// wl_write_log( "_wl_content_embed_microdata [ item ID :: $item_id ]" );
		$content = wl_content_embed_item_microdata( $content, $item_id, $annotations[ $item_id ] );
	}

	return $content;
}

/**
 * Embed the entity properties as microdata in the content.
 *
 * @param string $content A content.
 * @param string $uri An entity URI.
 * @param array  $annotations Mapping annotation ids => blindness
 * @param string $itemprop Specifies which property this entity is for another entity. Useful for recursive markup.
 *
 * @return string The content with embedded microdata.
 */
function wl_content_embed_item_microdata( $content, $uri, $annotations = array(), $itemprop = NULL, $recursion_level = 0 ) {

	// The recursion level is set by `wl_content_embed_compile_microdata_template`
	// which is loading referenced entities and calling again this function to print
	// additional properties. By default WordLift doesn't print more than 3 nested
	// entities.
	if ( $recursion_level > wl_config_get_recursion_depth() ) {
		wl_write_log( "recursion depth limit exceeded [ level :: $recursion_level ][ max :: " . wl_config_get_recursion_depth() . " ]" );

		return '';
	}

	$post = Wordlift_Entity_Service::get_instance()
	                               ->get_entity_post_by_uri( $uri );

	// Entity not found or not published. Delete <span> tags but leave their content on page.
	if ( NULL === $post || $post->post_status !== 'publish' ) {

		// wl_write_log( "wl_content_embed_item_microdata : entity not found or not published [ uri :: $uri ]" );

		// Replace the original tagging with the new tagging.
		$regex   = wl_content_embed_build_regex_from_uri( $uri );
		$content = preg_replace( $regex, '$2', $content );

		return $content;
	}

	// Get the entity URI and its escaped version for the regex.
	$entity_uri = wl_get_entity_uri( $post->ID );
	// Get the main type.
	$main_type = wl_entity_type_taxonomy_get_type( $post->ID );

	// Set the item type if available.
	$item_type = ( NULL === $main_type ? '' : ' itemtype="' . esc_attr( $main_type['uri'] ) . '"' );

	// Define attribute itemprop if this entity is nested.
	if ( ! is_null( $itemprop ) ) {
		$itemprop = ' itemprop="' . $itemprop . '"';
	}

	// Get additional properties (this may imply a recursion of this method on a sub-entity).
	$additional_properties = wl_content_embed_compile_microdata_template( $post->ID, $main_type, $recursion_level );

	$same_as = '';
	// Get the array of sameAs uris.
	$same_as_uris = wl_schema_get_value( $post->ID, 'sameAs' );
	// Prepare the sameAs fragment.
	foreach ( $same_as_uris as $same_as_uri ) {
		$same_as .= "<link itemprop=\"sameAs\" href=\"$same_as_uri\">";
	}

	// Get the entity URL.
	$permalink = get_permalink( $post->ID );
	$url       = '<link itemprop="url" href="' . $permalink . '" />';

	foreach ( $annotations as $annotation_id => $is_blind ) {

		// Replace the original tagging with the new tagging.
		$regex = '/<(\\w+)[^<]* id=\"' . $annotation_id . '"[^>]*>([^<]*)<\\/\\1>/i';
	
		// If entity is nested, we do not show a link, but a hidden meta.
		// See https://github.com/insideout10/wordlift-plugin/issues/348
		$name = ( ! is_null( $itemprop ) )
			? "<meta itemprop='name' content='$post->post_title' />"
			: ( $is_blind )
				? '<span class="wl-blind-annotation" itemprop="name" content="' . $post->post_title . '">$2</span>' :
				'<a class="wl-entity-page-link" href="' . $permalink . '" itemprop="name" content="' . $post->post_title . '">$2</a>';
		
		$content = preg_replace( $regex,
			'<$1' . $itemprop . ' itemscope' . $item_type . ' itemid="' . esc_attr( $entity_uri ) . '">'
			.	$same_as
			.	$additional_properties
			.	$url
			.	$name
			.	'</$1>',
			$content
		);

	}
	
	// wl_write_log( "wl_content_embed_item_microdata [ uri :: $uri ][ regex :: $regex ]" );

	return $content;
}

add_filter( 'the_content', 'wl_content_embed_microdata' );

/**
 * Fills up the microdata_template with entity's values.
 *
 * @param string $entity_id An entity ID.
 * @param string $entity_type Entity type structure.
 * @param integer $recursion_level Recursion depth level in microdata compiling. Recursion depth limit is defined by WL_MAX_NUM_RECURSIONS_WHEN_PRINTING_MICRODATA constant.
 *
 * @return string The content with embedded microdata.
 */
function wl_content_embed_compile_microdata_template( $entity_id, $entity_type, $recursion_level = 0 ) {
	global $wl_logger;

	if ( WP_DEBUG ) {
		$wl_logger->trace( "Embedding microdata [ entity id :: $entity_id ][ recursion level :: $recursion_level ]" );
	}

	$regex   = '/{{(.*?)}}/';
	$matches = array();

	if ( NULL === $entity_type ) {
		return '';
	}

	$template = $entity_type['microdata_template'];
	// Return empty string if template fields have not been found.
	if ( FALSE === preg_match_all( $regex, $template, $matches, PREG_SET_ORDER ) ) {
		return '';
	}

	foreach ( $matches as $match ) {

		$placeholder = $match[0];
		$field_name  = $match[1];

		// Get property value.
		$meta_collection = wl_schema_get_value( $entity_id, $field_name );

		// If no value is given, just remove the placeholder from the template
		if ( NULL == $meta_collection ) {
			$template = str_replace( $placeholder, '', $template );
			continue;
		}

		// What kind of value is it?
		// TODO: Performance issue here: meta type retrieving should be centralized
		$expected_type = wl_get_meta_type( $field_name );

		if ( WP_DEBUG ) {
			$wl_logger->trace( "Embedding microdata [ placeholder :: $placeholder ][ field name :: $field_name ][ meta collection :: " . ( is_array( $meta_collection ) ? var_export( $meta_collection, TRUE ) : $meta_collection ) . " ][ expected type :: $expected_type ]" );
		}

		foreach ( $meta_collection as $field_value ) {

			// Quick and dirty patch for #163:
			//  - only apply to URIs, i.e. to properties pointing to another post ( $field_value should be a post ID ),
			//  - check that $field_value is actually a number,
			//  - check that the referenced post is published.
			//  OR
			//  - if the value is empty then we don't display it.
			if ( Wordlift_Schema_Service::DATA_TYPE_URI === $expected_type && is_numeric( $field_value ) && 'publish' !== ( $post_status = get_post_status( $field_value ) )
			     || empty( $field_value )
			) {

				if ( WP_DEBUG ) {
					$wl_logger->trace( "Microdata refers to a non-published post [ field value :: $field_value ][ post status :: $post_status ]" );
				}

				// Remove the placeholder.
				$template = str_replace( $placeholder, '', $template );
				continue;
			}

			if ( Wordlift_Schema_Service::DATA_TYPE_URI == $expected_type ) {
				// If is a numeric value we assume it is an ID referencing for an internal entity.
				if ( is_numeric( $field_value ) ) {
					// Found id, get uri.
					$field_value = wl_get_entity_uri( $field_value );
				}
				// Just if the linked entity does exist I can go further with template compiling
				$nested_entity = Wordlift_Entity_Service::get_instance()
				                                        ->get_entity_post_by_uri( $field_value );
				if ( ! is_null( $nested_entity ) ) {

					// Create a fake and randomic annotation id
					$annotation_id = uniqid( 'urn:' );		
					$content           = '<span id="' . $annotation_id . '" class="textannotation disambiguated" itemid="' . esc_attr( $field_value ) . '">' . $nested_entity->post_title . '</span>';
					$compiled_template = wl_content_embed_item_microdata( $content, $field_value, array( $annotation_id => false ), $field_name, ++ $recursion_level );
					$template          = str_replace( $placeholder, $compiled_template, $template );
				} else {
					$template = str_replace( $placeholder, '', $template );
				}
				continue;
			}

			// Standard condition: field containing a raw value
			// For non visible test, schema.org dictates to use the *meta* tag.
			// see http://schema.org/docs/gs.html#advanced_missing
			$value    = '<meta itemprop="' . esc_attr( $field_name ) . '" content="' . esc_attr( $field_value ) . '" />';
			$template = str_replace( $placeholder, $value, $template );
		}
	}

	return $template;
}