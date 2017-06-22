<?php
/**
 * Services: Content Filter Service.
 *
 * Define the Wordlift_Content_Filter_Service class. This file is included from
 * the main class-wordlift.php file.
 *
 * @since   3.8.0
 * @package Wordlift
 */

/**
 * Define the Wordlift_Content_Filter_Service class which intercepts the
 * 'the_content' WP filter and mangles the content accordingly.
 *
 * @since   3.8.0
 * @package Wordlift
 */
class Wordlift_Content_Filter_Service {

	/**
	 * The pattern to find entities in text.
	 *
	 * @since 3.8.0
	 */
	const PATTERN = '/<(\\w+)[^<]*class="([^"]*)"\\sitemid=\"([^"]+)\"[^>]*>([^<]*)<\\/\\1>/i';

	/**
	 * A {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.8.0
	 * @access private
	 * @var \Wordlift_Entity_Service $entity_service A {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * The {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.13.0
	 * @access private
	 * @var \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	private $configuration_service;

	/**
	 * The `link by default` setting.
	 *
	 * @since  3.13.0
	 * @access private
	 * @var bool True if link by default is enabled otherwise false.
	 */
	private $is_link_by_default;

	/**
	 * Wordlift_Content_Filter_Service constructor.
	 *
	 * @since 3.8.0
	 *
	 * @param \Wordlift_Entity_Service        $entity_service        The {@link Wordlift_Entity_Service} instance.
	 * @param \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	public function __construct( $entity_service, $configuration_service ) {

		$this->entity_service        = $entity_service;
		$this->configuration_service = $configuration_service;

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

		// Preload the `link by default` setting.
		$this->is_link_by_default = $this->configuration_service->is_link_by_default();

		// Replace each match of the entity tag with the entity link. If an error
		// occurs fail silently returning the original content.
		return preg_replace_callback( self::PATTERN, array(
			$this,
			'link',
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
		$css_class = $matches[2];
		$uri       = $matches[3];
		$label     = $matches[4];

		// Get the entity post by URI.
		if ( null === ( $post = $this->entity_service->get_entity_post_by_uri( $uri ) ) ) {

			// If the entity post is not found return the label w/o the markup
			// around it.
			//
			// See https://github.com/insideout10/wordlift-plugin/issues/461
			return $label;
		}

		$no_link = - 1 < strpos( $css_class, 'wl-no-link' );
		$link    = - 1 < strpos( $css_class, 'wl-link' );

		// Don't link if links are disabled and the entity is not link or the
		// entity is do not link.
		$dont_link = ( ! $this->is_link_by_default && ! $link ) || $no_link;

		// Return the label if it's don't link.
		if ( $dont_link ) {
			return $label;
		}

		// Get the link.
		$href = get_permalink( $post );

		// Return the link.
		return "<a class='wl-entity-page-link' href='$href'>$label</a>";
	}

}
