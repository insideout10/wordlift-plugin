<?php

/**
 * Define the Wordlift_Content_Filter_Service class. This file is included from
 * the main class-wordlift.php file.
 */

/**
 * Define the Wordlift_Content_Filter_Service class which intercepts the
 * 'the_content' WP filter and mangles the content accordingly.
 *
 * @since 3.8.0
 */
class Wordlift_Content_Filter_Service {

	/**
	 * The pattern to find entities in text.
	 *
	 * @since 3.8.0
	 */
	const PATTERN = '/<(\\w+)[^<]* itemid=\"([^"]+)\"[^>]*>([^<]*)<\\/\\1>/i';

	/**
	 * A {@link Wordlift_Entity_Service} instance.
	 *
	 * @since 3.8.0
	 * @access private
	 * @var \Wordlift_Entity_Service $entity_service A {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * Wordlift_Content_Filter_Service constructor.
	 *
	 * @since 3.8.0
	 *
	 * @param $entity_service
	 */
	public function __construct( $entity_service ) {

		$this->entity_service = $entity_service;

	}

	/**
	 * Mangle the content by adding links to the entity pages. This function is
	 * hooked to the 'the_content' WP's filter.
	 *
	 * @since 3.8.0
	 *
	 * @param string $content The content being filtered.
	 *
	 * @return string The filtered content.
	 */
	public function the_content( $content ) {

		// Replace each match of the entity tag with the entity link. If an error
		// occurs fail silently returning the original content.
		return preg_replace_callback( self::PATTERN, array(
			$this,
			'link'
		), $content ) ?: $content;
	}

	/**
	 * Get the entity match and replace it with a page link.
	 *
	 * @since 3.8.0
	 *
	 * @param array $matches An array of matches.
	 *
	 * @return string The replaced text with the link to the entity page.
	 */
	private function link( $matches ) {

		// Get the entity itemid URI and label.
		$uri   = $matches[2];
		$label = $matches[3];

		// Get the entity post by URI.
		if ( NULL === ( $post = $this->entity_service->get_entity_post_by_uri( $uri ) ) ) {

			// If the entity post is not found return the original text.
			return $matches[0];
		}

		// Get the link.
		$link = get_permalink( $post );

		// Return the link.
		return "<a class='wl-entity-page-link' href='$link'>$label</a>";
	}

}
