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
        if( get_post_type( $post_id ) == WL_ENTITY_TYPE_NAME ) {
            $own_uri = wl_get_entity_uri( $post_id );
            $content .= '<span itemid="' . $own_uri . '"></span>';
        }
    
        // Now search in the text entity mentions        
	$regex = '/<(\\w+)[^<]* itemid=\"([^"]+)\"[^>]*>([^<]*)<\\/\\1>/i';
	$matches = array();

	// Return the content if not item IDs have been found.
	if ( false === preg_match_all( $regex, $content, $matches, PREG_SET_ORDER ) ) {
		return $content;
        }

	// TODO: Retrieve here just one time entities type structure to avoid multiple queries.
	foreach ( $matches as $match ) {
		$item_id = $match[2];

		wl_write_log( "_wl_content_embed_microdata [ item ID :: $item_id ]" );

		$content = wl_content_embed_item_microdata( $content, $item_id );
	}

	return $content;
}

/**
 * Embed the entity properties as microdata in the content.
 *
 * @param string $content A content.
 * @param string $uri An entity URI.
 * @param string $itemprop Specifies which property this entity is for another entity. Useful for recursive markup.
 *
 * @return string The content with embedded microdata.
 */
function wl_content_embed_item_microdata( $content, $uri, $itemprop = null, $recursion_level = 0 ) {

	if ( $recursion_level > wl_config_get_recursion_depth() ) {
		wl_write_log( "recursion depth limit exceeded [ level :: $recursion_level ][ max :: " . wl_config_get_recursion_depth() . " ]" );

		return '';
	}

	$post = wl_get_entity_post_by_uri( $uri );

	// Entity not found or not published. Delete <span> tags but leave their content on page.
	if ( null === $post || $post->post_status !== 'publish' ) {
		
                wl_write_log( "wl_content_embed_item_microdata : entity not found or not published [ uri :: $uri ]" );
                
                // Replace the original tagging with the new tagging.
                $regex   = wl_content_embed_build_regex_from_uri( $uri );
                $content = preg_replace( $regex, '$2', $content );
                
		return $content;
	}

	// Get the entity URI and its escaped version for the regex.
	$entity_uri = wl_get_entity_uri( $post->ID );
	// Get the main type.
	$main_type = wl_entity_type_taxonomy_get_type( $post->ID );

	if ( null === $main_type ) {
		$item_type = '';
	} else {
		$item_type = ' itemtype="' . esc_attr( $main_type['uri'] ) . '"';

		// Append the stylesheet if the enable color coding flag is set to true.
		if ( wl_configuration_get_enable_color_coding() && is_null( $itemprop ) ) {
			$item_type .= ' class="' . esc_attr( $main_type['css_class'] ) . '"';
		}
	}

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
	$url = '<link itemprop="url" href="' . $permalink . '" />';

	// Replace the original tagging with the new tagging.
	$regex   = wl_content_embed_build_regex_from_uri( $uri );
	$content = preg_replace( $regex,
		'<$1' . $itemprop . ' itemscope' . $item_type . ' itemid="' . esc_attr( $entity_uri ) . '">'
		. $same_as
		. $additional_properties
		. $url
		. '<a class="wl-entity-page-link" href="' . $permalink .'" itemprop="name" content="' . $post->post_title . '">' . ( is_null( $itemprop ) ? '$2' : '' ) . '</a></$1>',    //Only print name inside <span> for top-level entities
		$content
	);

	wl_write_log( "wl_content_embed_item_microdata [ uri :: $uri ][ regex :: $regex ]" );

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

	wl_write_log( "[ entity id :: $entity_id ][ entity type :: " . var_export( $entity_type, true ) . " ][ recursion level :: $recursion_level ]" );

	$regex   = '/{{(.*?)}}/';
	$matches = array();

	if ( null === $entity_type ) {
		return '';
	}

	$template = $entity_type['microdata_template'];
	// Return empty string if template fields have not been found.
	if ( false === preg_match_all( $regex, $template, $matches, PREG_SET_ORDER ) ) {
		return '';
	}
        
	foreach ( $matches as $match ) {

		$placeholder = $match[0];
		$field_name  = $match[1];
                
		// Get property value.
		$meta_collection = wl_schema_get_value( $entity_id, $field_name );
		// If no value is given, just remove the placeholder from the template
		if ( null == $meta_collection ) {
			$template = str_replace( $placeholder, '', $template );
			continue;
		}

		// What kind of value is it?
		// TODO: Performance issue here: meta type retrieving should be centralized
		$expected_type = wl_get_meta_type( $field_name );

		foreach ( $meta_collection as $field_value ) {

			if ( Wordlift_Schema_Service::DATA_TYPE_URI == $expected_type ) {
				// If is a numeric value we assume it is an ID referencing for an internal entity.
				if ( is_numeric( $field_value ) ) {
					// Found id, get uri.
					$field_value = wl_get_entity_uri( $field_value );
				}
				// Just if the linked entity does exist I can go further with template compiling
                                $nested_entity = wl_get_entity_post_by_uri( $field_value );
				if ( !is_null($nested_entity) ) {
					$content           = '<span itemid="' . esc_attr( $field_value ) . '">' . $nested_entity->post_title . '</span>';
					$compiled_template = wl_content_embed_item_microdata( $content, $field_value, $field_name, ++ $recursion_level );
					$template          = str_replace( $placeholder, $compiled_template, $template );
				} else {
					$template = str_replace( $placeholder, '', $template );
				}
				continue;
			}

			// Standard condition: field containing a raw value
			$value    = '<span itemprop="' . esc_attr( $field_name ) . '" content="' . esc_attr( $field_value ) . '"></span>';
			$template = str_replace( $placeholder, $value, $template );
		}
	}
        
	return $template;
}