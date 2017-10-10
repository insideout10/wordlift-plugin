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
class Wordlift_Post_To_Jsonld_Converter extends Wordlift_Abstract_Post_To_Jsonld_Converter {

	/**
	 * A {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.10.0
	 * @access private
	 * @var \Wordlift_Configuration_Service $configuration_service A {@link Wordlift_Configuration_Service} instance.
	 */
	private $configuration_service;

	/**
	 * @var Wordlift_Entity_Post_To_Jsonld_Converter
	 */
	private $entity_post_to_jsonld_converter;

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
	 * @param \Wordlift_Entity_Type_Service              $entity_type_service   A {@link Wordlift_Entity_Type_Service} instance.
	 * @param \Wordlift_Entity_Service                   $entity_service        A {@link Wordlift_Entity_Service} instance.
	 * @param \Wordlift_User_Service                     $user_service          A {@link Wordlift_User_Service} instance.
	 * @param \Wordlift_Attachment_Service               $attachment_service    A {@link Wordlift_Attachment_Service} instance.
	 * @param \Wordlift_Configuration_Service            $configuration_service A {@link Wordlift_Configuration_Service} instance.
	 * @param  \Wordlift_Entity_Post_To_Jsonld_Converter $entity_post_to_jsonld_converter
	 */
	public function __construct( $entity_type_service, $entity_service, $user_service, $attachment_service, $configuration_service, $entity_post_to_jsonld_converter ) {
		parent::__construct( $entity_type_service, $entity_service, $user_service, $attachment_service );

		$this->configuration_service           = $configuration_service;
		$this->entity_post_to_jsonld_converter = $entity_post_to_jsonld_converter;

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

		// Get the base JSON-LD and the list of entities referenced by this entity.
		$jsonld = parent::convert( $post_id, $references );

		// Get the entity name.
		$jsonld['headline'] = $post->post_title;

		// Get the author.
		$jsonld['author'] = $this->get_author( $post->post_author );

		// Set the published and modified dates.
		$jsonld['datePublished'] = get_post_time( 'Y-m-d\TH:i', true, $post, false );
		$jsonld['dateModified']  = get_post_modified_time( 'Y-m-d\TH:i', true, $post, false );

		// Get the word count for the post.
		$post_adapter        = new Wordlift_Post_Adapter( $post_id );
		$jsonld['wordCount'] = $post_adapter->word_count();

		// Set the publisher.
		$this->set_publisher( $jsonld );

		// Process the references if any.
		if ( 0 < sizeof( $references ) ) {

			// Prepare the `about` and `mentions` array.
			$about = $mentions = array();

			// If the entity is in the title, then it should be an `about`.
			foreach ( $references as $reference ) {

				// Get the entity labels.
				$labels = $this->entity_service->get_labels( $reference );

				// Get the entity URI.
				$item = array( '@id' => $this->entity_service->get_uri( $reference ) );

				// Check if the labels match any part of the title.
				$matches = 1 === preg_match( '/' . implode( '|', $labels ) . '/', $post->post_title );

				// If the title matches, assign the entity to the about, otherwise to the mentions.
				if ( $matches ) {
					$about[] = $item;
				} else {
					$mentions[] = $item;
				}
			}

			// If we have abouts, assign them to the JSON-LD.
			if ( 0 < sizeof( $about ) ) {
				$jsonld['about'] = $about;
			}

			// If we have mentions, assign them to the JSON-LD.
			if ( 0 < sizeof( $mentions ) ) {
				$jsonld['mentions'] = $mentions;
			}

		}

		/**
		 * Call the `wl_post_jsonld` filter.
		 *
		 * @api
		 *
		 * @since 3.14.0
		 *
		 * @param array $jsonld     The JSON-LD structure.
		 * @param int   $post_id    The {@link WP_Post} `id`.
		 * @param array $references The array of referenced entities.
		 */
		return apply_filters( 'wl_post_jsonld', $jsonld, $post_id, $references );
	}

	/**
	 * Get the author's JSON-LD fragment.
	 *
	 * The JSON-LD fragment is generated using the {@link WP_User}'s data or
	 * the referenced entity if configured for the {@link WP_User}.
	 *
	 * @since 3.14.0
	 *
	 * @param int $author_id The author {@link WP_User}'s `id`.
	 *
	 * @return array A JSON-LD structure.
	 */
	private function get_author( $author_id ) {

		// Get the entity bound to this user.
		$entity_id = $this->user_service->get_entity( $author_id );

		// If there's no entity bound return a simple author structure.
		if ( empty( $entity_id ) ) {

			$author     = get_the_author_meta( 'display_name', $author_id );
			$author_uri = $this->user_service->get_uri( $author_id );

			return array(
				'@type' => 'Person',
				'@id'   => $author_uri,
				'name'  => $author,
			);
		}

		// Return the JSON-LD for the referenced entity.
		return $this->entity_post_to_jsonld_converter->convert( $entity_id );
	}

	/**
	 * Enrich the provided params array with publisher data, if available.
	 *
	 * @since 3.10.0
	 *
	 * @param array $params The parameters array.
	 */
	protected function set_publisher( &$params ) {

		// If the publisher id isn't set don't do anything.
		if ( null === $publisher_id = $this->configuration_service->get_publisher_id() ) {
			return;
		}

		// Get the post instance.
		if ( null === $post = get_post( $publisher_id ) ) {
			// Publisher not found.
			return;
		}

		// Get the item id
		$id = $this->entity_service->get_uri( $publisher_id );

		// Get the type.
		$type = $this->entity_type_service->get( $publisher_id );

		// Get the name.
		$name = $post->post_title;

		// Set the publisher data.
		$params['publisher'] = array(
			'@type' => $this->relative_to_context( $type['uri'] ),
			'@id'   => $id,
			'name'  => $name,
		);

		// Add the sameAs values associated with the publisher.
		$storage_factory = Wordlift_Storage_Factory::get_instance();
		$sameas = $storage_factory->post_meta( Wordlift_Schema_Service::FIELD_SAME_AS )->get( $publisher_id );
		if ( ! empty( $sameas ) ) {
			$params['publisher']['sameAs'] = $sameas;
		}

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
		$params['publisher']['logo']['@type'] = 'ImageObject';
		$params['publisher']['logo']['url']   = $attachment[0];
		// If you specify a "width" or "height" value you should leave out
		// 'px'. For example: "width":"4608px" should be "width":"4608".
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/451
		$params['publisher']['logo']['width']  = $attachment[1];
		$params['publisher']['logo']['height'] = $attachment[2];

	}

}
