<?php
/**
 * Module Name: Yoast SEO
 * Description: Enhances Yoast SEO structured data.
 *
 * @since   1.0.0
 * @package wordlift
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds images to Article Schema data.
 *
 * @param array $data Schema.org Article data array.
 *
 * @return array Schema.org Article data array.
 */
// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
function __wl_yoast_seo__wpseo_schema_article( $data, $context ) {
	// This is the attachment ID for our image.
	// $attachment_id = 12345;
	//
	// We're going to create a graph piece for our image. Every graph piece always needs a Schema ID, so it can
	// be referenced by other graph pieces, best practice is to base that on the canonical adding an ID that's
	// always going to be unique.
	// $schema_id     = YoastSEO()->meta->for_current_page()->canonical . '#/schema/image/' . $attachment_id;
	// $data['image'] = new WPSEO_Schema_Image( $schema_id );

	return $data;
}

add_action( 'wpseo_schema_article', '__wl_yoast_seo__wpseo_schema_article', 10, 2 );
