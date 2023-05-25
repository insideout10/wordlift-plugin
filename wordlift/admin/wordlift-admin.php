<?php
/**
 * This file contains miscellaneous admin-functions.
 *
 * @package Wordlift
 */

// Add the Admin menu.
require_once 'wordlift-admin-menu.php';

/**
 * Serialize an entity post.
 *
 * @param int|array $entity The entity post or the entity post id.
 *
 * @return array mixed The entity data array.
 */
function wl_serialize_entity( $entity ) {

	$entity = ( is_numeric( $entity ) ) ? get_post( $entity ) : $entity;

	// Bail if the entity doesn't exists.
	// In some cases we have `wl_topic` meta
	// pointing to an entity that has been deleted.
	if ( empty( $entity ) ) {
		return;
	}

	$type   = Wordlift_Entity_Type_Service::get_instance()->get( $entity->ID );
	$images = wl_get_image_urls( $entity->ID );

	return array(
		'id'          => wl_get_entity_uri( $entity->ID ),
		'label'       => $entity->post_title,
		'description' => $entity->post_content,
		'sameAs'      => wl_schema_get_value( $entity->ID, 'sameAs' ),
		'mainType'    => str_replace( 'wl-', '', $type['css_class'] ),
		'types'       => wl_get_entity_rdf_types( $entity->ID ),
		'images'      => $images,

	);
}

/**
 * Removes empty text annotations from the post content.
 *
 * @param array $data The post data.
 *
 * @return array mixed The post data array.
 * @since 1.0.0
 */
function wl_remove_text_annotations( $data ) {

	// Remove blank elements that can interfere with annotations removing
	// See https://github.com/insideout10/wordlift-plugin/issues/234
	// Just blank span tags without any attributes are cleaned up.
	// Restrict removal to empty spans only as we may impact valid empty elements like: <th>
	$pattern = '/<span><\/span>/im';
	// Remove the pattern while it is found (match nested annotations).
	while ( 1 === preg_match( $pattern, $data['post_content'] ) ) {
		$data['post_content'] = preg_replace( $pattern, '$2', $data['post_content'], - 1, $count );
	}
	// Remove text annotations
	// <span class="textannotation" id="urn:enhancement-777cbed4-b131-00fb-54a4-ed9b26ae57ea">.

	// @see https://github.com/insideout10/wordlift-plugin/issues/771
	// Changing this:
	// $pattern = '/<(\w+)[^>]*\sclass=\\\"textannotation(?![^\\"]*\sdisambiguated)[^\\"]*\\\"[^>]*>([^<]+)<\/\1>/im';
	// to:
	$pattern = '/<(\w+)[^>]*\sclass=\\\"textannotation(?![^\\"]*\sdisambiguated)[^\\"]*\\\"[^>]*>(.*?)<\/\1>/im';
	// Remove the pattern while it is found (match nested annotations).
	while ( 1 === preg_match( $pattern, $data['post_content'] ) ) {
		$data['post_content'] = preg_replace( $pattern, '$2', $data['post_content'], - 1, $count );
	}

	return $data;
}

add_filter( 'wp_insert_post_data', 'wl_remove_text_annotations', '98', 1 );

/**
 * Adds wl-metabox CSS class to a metabox.
 *
 * @param array $classes List of CSS classes already assigned to the metabox.
 *
 * @return array The updated list of CSS classes.
 * @since 3.2.0
 */
function wl_admin_metaboxes_add_css_class( $classes = array() ) {

	return array_merge( $classes, array( 'wl-metabox' ) );
}
