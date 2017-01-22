<?php
/**
 * Converters: Post to JSON-LD Converter.
 *
 * This file defines a converter from an entity {@link WP_Post} to a JSON-LD array.
 *
 * @since   3.10.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Post_To_Jsonld_Converter} class.
 *
 * @since 3.10.0
 */
class Wordlift_Post_To_Jsonld_Converter implements Wordlift_Post_Converter {

	/**
	 * The JSON-LD context.
	 *
	 * @since 3.10.0
	 */
	const CONTEXT = 'http://schema.org';

	/**
	 * A {@link Wordlift_Entity_Type_Service} instance.
	 *
	 * @since  3.10.0
	 * @access protected
	 * @var \Wordlift_Entity_Type_Service $entity_type_service A {@link Wordlift_Entity_Type_Service} instance.
	 */
	protected $entity_type_service;

	/**
	 * A {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.10.0
	 * @access protected
	 * @var \Wordlift_Entity_Service $entity_type_service A {@link Wordlift_Entity_Service} instance.
	 */
	protected $entity_service;

	/**
	 * The publisher id.
	 *
	 * @since  3.10.0
	 * @access private
	 * @var int|NULL  The publisher id or NULL if not set.
	 */
	private $publisher_id;

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.10.0
	 * @access private
	 * @var Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * Wordlift_Post_To_Jsonld_Converter constructor.
	 *
	 * @since 3.10.0
	 *
	 * @param \Wordlift_Entity_Type_Service $entity_type_service
	 * @param \Wordlift_Entity_Service      $entity_service
	 * @param \Wordlift_Property_Getter     $property_getter
	 * @param int|NULL                      $publisher_id The publisher id or NULL if not configured.
	 */
	public function __construct( $entity_type_service, $entity_service, $property_getter, $publisher_id ) {

		$this->entity_type_service = $entity_type_service;
		$this->entity_service      = $entity_service;
		$this->publisher_id        = $publisher_id;

		// Set a reference to the logger.
		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Post_To_Jsonld_Converter' );
	}

	/**
	 * Convert the provided {@link WP_Post} to a JSON-LD array. Any entity reference
	 * found while processing the post is set in the $references array.
	 *
	 * @since 3.10.0
	 *
	 *
	 * @param int   $post_id    The post id.
	 * @param array $references An array of entity references.
	 *
	 * @return array A JSON-LD array.
	 */
	public function convert( $post_id, &$references = array() ) {

		// Get the post instance.
		if ( null === $post = get_post( $post_id ) ) {
			// Post not found.
			return null;
		}

		// Get the post URI @id.
		$id = $this->entity_service->get_uri( $post->ID );

		// Get the entity @type. We consider `post` BlogPostings.
		$type = $this->entity_type_service->get( $post_id );

		// Get the entity name.
		$headline = $post->post_title;

		// Get the author.
		$author = get_the_author_meta( 'display_name', $post->post_author );

		// Prepare the response.
		$jsonld = array(
			'@context'      => self::CONTEXT,
			'@id'           => $id,
			'@type'         => $this->relative_to_context( $type['uri'] ),
			'headline'      => $headline,
			'description'   => $this->get_excerpt( $post ),
			'author'        => array( '@type' => 'Person', 'name' => $author ),
			'datePublished' => get_post_time( 'Y-m-d\TH:i', true, $post, false ),
			'dateModified'  => get_post_modified_time( 'Y-m-d\TH:i', true, $post, false ),
		);

		// Set the image URLs if there are images.
		$images = wl_get_image_urls( $post->ID );
		if ( 0 < count( $images ) ) {
			$jsonld['image'] = $images;
		}

		// Set the publisher.
		$this->set_publisher( $jsonld );

		return $jsonld;
	}

	/**
	 * If the provided value starts with the schema.org context, we remove the schema.org
	 * part since it is set with the '@context'.
	 *
	 * @since 3.10.0
	 *
	 * @param string $value The property value.
	 *
	 * @return string The property value without the context.
	 */
	public function relative_to_context( $value ) {

		return 0 === strpos( $value, self::CONTEXT . '/' ) ? substr( $value, strlen( self::CONTEXT ) + 1 ) : $value;
	}

	/**
	 * Get the excerpt for the provided {@link WP_Post}.
	 *
	 * @since 3.10.0
	 *
	 * @param WP_Post $post The {@link WP_Post}.
	 *
	 * @return string The excerpt.
	 */
	private function get_excerpt( $post ) {

		// Temporary pop the previous post.
		$original = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;

		// Setup our own post.
		setup_postdata( $GLOBALS['post'] = &$post );

		$excerpt = get_the_excerpt( $post );

		// Restore the previous post.
		if ( null !== $original ) {
			setup_postdata( $GLOBALS['post'] = $original );
		}

		// Finally return the excerpt.
		return html_entity_decode( $excerpt );
	}

	/**
	 * Enrich the provided params array with publisher data, if available.
	 *
	 * @since 3.10.0
	 *
	 * @param array $params The parameters array.
	 */
	private function set_publisher( &$params ) {

		// If the publisher id isn't set don't do anything.
		if ( ! isset( $this->publisher_id ) ) {
			return;
		}

		// Get the post instance.
		if ( null === $post = get_post( $this->publisher_id ) ) {
			// Publisher not found.
			return;
		}

		// Get the item id
		$item_id = $this->entity_service->get_uri( $this->publisher_id );

		// Get the type.
		$type = $this->entity_type_service->get( $this->publisher_id );

		// Get the name.
		$name = $post->post_title;

		// Set the publisher data.
		$params['publisher'] = array(
			'@type' => $this->relative_to_context( $type['uri'] ),
			'@id'   => $item_id,
			'name'  => $name,
		);

		// Set the logo, only for http://schema.org/Organization as Person doesn't
		// support the logo property.
		//
		// See http://schema.org/logo
		if ( 'http://schema.org/Organization' !== $type['uri'] ) {
			return;
		}

		// Get the logo, WP < 4.4 way: only post ID accepted here.
		if ( '' === $thumbnail_id = get_post_thumbnail_id( $post->ID ) ) {
			return;
		}

		// Get the image URL.
		if ( false === $attachment = wp_get_attachment_image_src( $thumbnail_id, 'full' ) ) {
			return;
		}

		// Copy over some useful properties.
		//
		// See https://developers.google.com/search/docs/data-types/articles
		$params['publisher']['logo']['url']    = $attachment[0];
		$params['publisher']['logo']['width']  = $attachment[1];
		$params['publisher']['logo']['height'] = $attachment[2];

	}

}
