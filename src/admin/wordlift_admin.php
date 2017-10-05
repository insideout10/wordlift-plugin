<?php
/**
 * This file contains miscellaneous admin-functions.
 */

// Add the Admin menu.
require_once( 'wordlift_admin_menu.php' );

/**
 * Serialize an entity post.
 *
 * @param array $entity The entity post or the entity post id.
 *
 * @return array mixed The entity data array.
 */
function wl_serialize_entity( $entity ) {

	$entity = ( is_numeric( $entity ) ) ? get_post( $entity ) : $entity;
	
	$type   = wl_entity_type_taxonomy_get_type( $entity->ID );
	$images = wl_get_image_urls( $entity->ID );

	return array(
		'id'         	=> wl_get_entity_uri( $entity->ID ),
		'label'      	=> $entity->post_title,
		'description'	=> $entity->post_content,
		'sameAs'     	=> wl_schema_get_value( $entity->ID, 'sameAs' ),
		'mainType'      => str_replace( 'wl-', '', $type['css_class'] ),
		'types'      	=> wl_get_entity_rdf_types( $entity->ID ),
		'images' 		=> $images,

	);
}

/**
 * Removes empty text annotations from the post content.
 *
 * @since 1.0.0
 *
 * @param array $data The post data.
 *
 * @return array mixed The post data array.
 */
function wl_remove_text_annotations( $data ) {

	// Remove blank elements that can interfere with annoataions removing
	// See https://github.com/insideout10/wordlift-plugin/issues/234
	// Just blank attributes without any attribtues are cleaned up
	$pattern = '/<(\w+)><\/\1>/im';
	// Remove the pattern while it is found (match nested annotations).
	while ( 1 === preg_match( $pattern, $data['post_content'] ) ) {		
		$data['post_content'] = preg_replace( $pattern, '$2', $data['post_content'], -1, $count ); 
	}
	// Remove text annotations
	//    <span class="textannotation" id="urn:enhancement-777cbed4-b131-00fb-54a4-ed9b26ae57ea">
	$pattern = '/<(\w+)[^>]*\sclass=\\\"textannotation(?![^\\"]*\sdisambiguated)[^\\"]*\\\"[^>]*>([^<]+)<\/\1>/im';
	// Remove the pattern while it is found (match nested annotations).
	while ( 1 === preg_match( $pattern, $data['post_content'] ) ) {		
		$data['post_content'] = preg_replace( $pattern, '$2', $data['post_content'], -1, $count ); 
	}
	return $data;
}

add_filter( 'wp_insert_post_data', 'wl_remove_text_annotations', '98', 1 );

/**
 * Adds wl-metabox CSS class to a metabox.
 *
 * @since 3.2.0
 *
 * @param array $classes List of CSS classes already assigned to the metabox.
 *
 * @return array The updated list of CSS classes.
 */
function wl_admin_metaboxes_add_css_class( $classes = array() ){
	
	return array_merge( $classes, array( 'wl-metabox' ) );
}

/**
 * Adds new image size `wl_logo`. It will be used to crop & resize
 * featured images of entity type "Oraganization".
 * see: https://github.com/insideout10/wordlift-plugin/issues/597
 *
 * @since 3.16.0
 *
 * @return void
 */
function wl_after_setup_theme() {
	// Add new image size for organization featured images.
	add_image_size( 'wl_organization_logo', 600, 60, true );
}

add_action( 'after_setup_theme', 'wl_after_setup_theme' );

/**
 * Adds featured image metabox additional instructions
 * when the entity type is organization.
 * see: https://github.com/insideout10/wordlift-plugin/issues/597
 *
 * @since  3.16.0
 *
 * @param  string $content Current metabox content.
 *
 * @return string $content metabox content with additional instructions.
 */
function wl_add_featured_image_instruction( $content ) {
	// Get the current post ID.
	$post_id = get_the_ID();

	// Bail if for some reason the post id is not set.
	if ( empty( $post_id ) ) {
		return $content;
	}

	// Get entity type(s).
	$terms = wp_get_object_terms(
		$post_id, // The post ID.
		Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME, // The taxonomy slug.
		array(
			'fields' => 'slugs', // We don't need all fields, but only slugs.
		)
	);

	// Check that the entity type is "Organization".
	if ( in_array( 'organization' , $terms, true ) ) {
		// Add the featured image description when the type is "Organization".
		$content .= '<p>' . esc_html__( 'Recommended image size 600px * 60px. Bigger images will be automatically cropped and resized to fit that size.', 'wordlift' ) . '</p>';
	}

	// Finally return the content.
	return $content;
}

add_filter( 'admin_post_thumbnail_html', 'wl_add_featured_image_instruction' );
