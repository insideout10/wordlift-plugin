<?php
/**
 * Converters: Post to JSON-LD Converter.
 *
 * This file defines a converter from an entity {@link WP_Post} to a JSON-LD array.
 *
 * @since   3.10.0
 * @package Wordlift
 */

use Wordlift\Jsonld\Post_Reference;
use Wordlift\Jsonld\Reference;
use Wordlift\Relation\Object_Relation_Service;

/**
 * Define the {@link Wordlift_Post_To_Jsonld_Converter} class.
 *
 * @since 3.10.0
 */
class Wordlift_Post_To_Jsonld_Converter extends Wordlift_Abstract_Post_To_Jsonld_Converter {

	/**
	 * @var Wordlift_Post_To_Jsonld_Converter
	 */
	private static $instance;

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.10.0
	 * @access private
	 * @var Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * @var false
	 */
	private $disable_convert_filters;
	/**
	 * @var Object_Relation_Service
	 */
	private $object_relation_service;

	/**
	 * Wordlift_Post_To_Jsonld_Converter constructor.
	 *
	 * @param \Wordlift_Entity_Type_Service $entity_type_service A {@link Wordlift_Entity_Type_Service} instance.
	 * @param \Wordlift_User_Service        $user_service A {@link Wordlift_User_Service} instance.
	 * @param \Wordlift_Attachment_Service  $attachment_service A {@link Wordlift_Attachment_Service} instance.
	 *
	 * @since 3.10.0
	 */
	public function __construct( $entity_type_service, $user_service, $attachment_service, $disable_convert_filters = false ) {
		parent::__construct( $entity_type_service, $user_service, $attachment_service, Wordlift_Property_Getter_Factory::create() );
		$this->disable_convert_filters = $disable_convert_filters;
		$this->object_relation_service = Object_Relation_Service::get_instance();
		// Set a reference to the logger.
		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Post_To_Jsonld_Converter' );

		self::$instance = $this;

	}

	public static function get_instance() {

		return self::$instance;
	}

	public function new_instance_with_filters_disabled() {
		return new static( $this->entity_type_service, $this->user_service, $this->attachment_service, true );
	}

	/**
	 * Convert the provided {@link WP_Post} to a JSON-LD array. Any entity reference
	 * found while processing the post is set in the $references array.
	 *
	 * @param int              $post_id The post id.
	 * @param array<Reference> $references An array of entity references.
	 * @param array            $references_infos
	 *
	 * @return array A JSON-LD array.
	 * @since 3.10.0
	 */
	public function convert( $post_id, &$references = array(), &$references_infos = array() ) {

		// Get the post instance.
		$post = get_post( $post_id );
		if ( null === $post ) {
			// Post not found.
			return null;
		}

		// Get the base JSON-LD and the list of entities referenced by this entity.
		$jsonld = parent::convert( $post_id, $references, $references_infos );

		// Set WebPage by default.
		if ( empty( $jsonld['@type'] ) ) {
			$jsonld['@type'] = 'WebPage';
		}

		// Get the entity name.
		$jsonld['headline'] = $post->post_title;

		$custom_fields = $this->entity_type_service->get_custom_fields_for_post( $post_id );

		if ( isset( $custom_fields ) ) {
			$this->process_type_custom_fields( $jsonld, $custom_fields, $post, $references, $references_infos );
		}

		// Set the published and modified dates.
		/*
		 * Set the `datePublished` and `dateModified` using the local timezone.
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/887
		 *
		 * @since 3.20.0
		 */
		try {
			$default_timezone = date_default_timezone_get();
			$timezone         = get_option( 'timezone_string' );
			if ( ! empty( $timezone ) ) {
				date_default_timezone_set( $timezone ); //phpcs:ignore WordPress.DateTime.RestrictedFunctions.timezone_change_date_default_timezone_set
				$jsonld['datePublished'] = get_post_time( 'Y-m-d\TH:i:sP', false, $post );
				$jsonld['dateModified']  = get_post_modified_time( 'Y-m-d\TH:i:sP', false, $post );
				date_default_timezone_set( $default_timezone ); //phpcs:ignore WordPress.DateTime.RestrictedFunctions.timezone_change_date_default_timezone_set
			} else {
				$jsonld['datePublished'] = get_post_time( 'Y-m-d\TH:i', true, $post, false );
				$jsonld['dateModified']  = get_post_modified_time( 'Y-m-d\TH:i', true, $post, false );
			}
		} catch ( Exception $e ) {
			$jsonld['datePublished'] = get_post_time( 'Y-m-d\TH:i', true, $post, false );
			$jsonld['dateModified']  = get_post_modified_time( 'Y-m-d\TH:i', true, $post, false );
		}

		// Get the word count for the post.
		/*
		 * Do not display the `wordCount` on a `WebPage`.
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/888
		 *
		 * @since 3.20.0
		 */
		if ( ! empty( $jsonld['@type'] ) && 'WebPage' !== $jsonld['@type'] ) {
			$post_adapter        = new Wordlift_Post_Adapter( $post_id );
			$jsonld['wordCount'] = $post_adapter->word_count();
		}

		/*
		 * Add keywords, articleSection, commentCount and inLanguage properties to `Article` JSON-LD
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/1140
		 *
		 * @since 3.27.2
		 */
		if ( ! empty( $jsonld['@type'] ) && 'WebPage' !== $jsonld['@type'] ) {
			$post_adapter    = new Wordlift_Post_Adapter( $post_id );
			$keywords        = $post_adapter->keywords();
			$article_section = $post_adapter->article_section();
			$comment_count   = $post_adapter->comment_count();
			$locale          = $post_adapter->locale();

			if ( isset( $keywords ) ) {
				$jsonld['keywords'] = $keywords;
			}
			if ( ! empty( $article_section ) ) {
				$jsonld['articleSection'] = $article_section;
			}
			$jsonld['commentCount'] = $comment_count;
			$jsonld['inLanguage']   = $locale;
			$post_adapter->add_references( $post_id, $references );
		}

		// Set the publisher.
		$this->set_publisher( $jsonld );

		$references = $this->convert_references( $references );

		// Process the references if any.
		$this->set_mentions_and_about( $references, $post, $jsonld );

		// Finally set the author.
		$jsonld['author'] = $this->get_author( $post->post_author, $references );

		// Return the JSON-LD if filters are disabled by the client.
		if ( $this->disable_convert_filters ) {
			return $jsonld;
		}

		/**
		 * Call the `wl_post_jsonld_array` filter. This filter allows 3rd parties to also modify the references.
		 *
		 * @param array $value {
		 *
		 * @type array $jsonld The JSON-LD structure.
		 * @type int[] $references An array of post IDs.
		 * }
		 * @since 3.25.0
		 *
		 * @see https://www.geeklab.info/2010/04/wordpress-pass-variables-by-reference-with-apply_filter/
		 *
		 * @api
		 */
		$ret_val = apply_filters(
			'wl_post_jsonld_array',
			array(
				'jsonld'     => $jsonld,
				'references' => $references,
			),
			$post_id
		);

		$jsonld     = $ret_val['jsonld'];
		$references = $ret_val['references'];

		/**
		 * Call the `wl_post_jsonld` filter.
		 *
		 * @param array $jsonld The JSON-LD structure.
		 * @param int $post_id The {@link WP_Post} `id`.
		 * @param array $references The array of referenced entities.
		 *
		 * @since 3.14.0
		 *
		 * @api
		 */
		return apply_filters( 'wl_post_jsonld', $jsonld, $post_id, $references );
	}

	/**
	 * Get the author's JSON-LD fragment.
	 *
	 * The JSON-LD fragment is generated using the {@link WP_User}'s data or
	 * the referenced entity if configured for the {@link WP_User}.
	 *
	 * @param int   $author_id The author {@link WP_User}'s `id`.
	 * @param array $references An array of referenced entities.
	 *
	 * @return string|array A JSON-LD structure.
	 * @since 3.14.0
	 */
	public function get_author( $author_id, &$references ) {

		// Get the entity bound to this user.
		$entity_id = $this->user_service->get_entity( $author_id );

		// If there's no entity bound return a simple author structure.
		if ( empty( $entity_id ) || 'publish' !== get_post_status( $entity_id ) ) {

			$author            = get_the_author_meta( 'display_name', $author_id );
			$author_first_name = get_the_author_meta( 'first_name', $author_id );
			$author_last_name  = get_the_author_meta( 'last_name', $author_id );
			$author_uri        = $this->user_service->get_uri( $author_id );

			return array(
				'@type'      => 'Person',
				'@id'        => $author_uri,
				'name'       => $author,
				'givenName'  => $author_first_name,
				'familyName' => $author_last_name,
				'url'        => get_author_posts_url( $author_id ),
			);
		}

		// Add the author to the references.
		$author_uri   = Wordlift_Entity_Service::get_instance()->get_uri( $entity_id );
		$references[] = $entity_id;

		// Return the JSON-LD for the referenced entity.
		return array(
			'@id' => $author_uri,
		);
	}

	/**
	 * Enrich the provided params array with publisher data, if available.
	 *
	 * @param array $params The parameters array.
	 *
	 * @since 3.10.0
	 */
	protected function set_publisher( &$params ) {

		// If the publisher id isn't set don't do anything.
		$publisher_id = Wordlift_Configuration_Service::get_instance()->get_publisher_id();
		if ( null === $publisher_id ) {
			return;
		}

		// Get the post instance.
		$post = get_post( $publisher_id );
		if ( null === $post ) {
			// Publisher not found.
			return;
		}

		// Get the item id.
		$id = Wordlift_Entity_Service::get_instance()->get_uri( $publisher_id );

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
		$sameas          = $storage_factory->post_meta( Wordlift_Schema_Service::FIELD_SAME_AS )->get( $publisher_id );
		if ( ! empty( $sameas ) ) {
			$params['publisher']['sameAs'] = $sameas;
		}

		// Set the logo, only for http://schema.org/Organization as Person doesn't
		// support the logo property.
		//
		// See http://schema.org/logo.
		if ( 1 !== preg_match( '~Organization$~', $type['uri'] ) ) {
			return;
		}

		// Get the publisher logo.
		$publisher_logo = $this->get_publisher_logo( $post->ID );

		// Bail out if the publisher logo isn't set.
		if ( false === $publisher_logo ) {
			return;
		}

		// Copy over some useful properties.
		//
		// See https://developers.google.com/search/docs/data-types/articles.
		$params['publisher']['logo']['@type'] = 'ImageObject';
		$params['publisher']['logo']['url']   = $publisher_logo['url'];

		// If you specify a "width" or "height" value you should leave out
		// 'px'. For example: "width":"4608px" should be "width":"4608".
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/451.
		$params['publisher']['logo']['width']  = $publisher_logo['width'];
		$params['publisher']['logo']['height'] = $publisher_logo['height'];

	}

	/**
	 * Get the publisher logo structure.
	 *
	 * The function returns false when the publisher logo cannot be determined, i.e.:
	 *  - the post has no featured image.
	 *  - the featured image has no file.
	 *  - a wp_image_editor instance cannot be instantiated on the original file or on the publisher logo file.
	 *
	 * @param int $post_id The post id.
	 *
	 * @return array|false Returns an array with the `url`, `width` and `height` for the publisher logo or false in case
	 *  of errors.
	 * @since 3.19.2
	 * @see https://github.com/insideout10/wordlift-plugin/issues/823 related issue.
	 */
	private function get_publisher_logo( $post_id ) {

		// Get the featured image for the post.
		$thumbnail_id = get_post_thumbnail_id( $post_id );

		// Bail out if thumbnail not available.
		if ( empty( $thumbnail_id ) || 0 === $thumbnail_id ) {
			$this->log->info( "Featured image not set for post $post_id." );

			return false;
		}

		// Get the uploads base URL.
		$uploads_dir = wp_upload_dir();

		// Get the attachment metadata.
		$metadata = wp_get_attachment_metadata( $thumbnail_id );

		// Bail out if the file isn't set.
		if ( ! isset( $metadata['file'] ) ) {
			$this->log->warn( "Featured image file not found for post $post_id." );

			return false;
		}

		// Retrieve the relative filename, e.g. "2018/05/logo_publisher.png"
		$path = $uploads_dir['basedir'] . DIRECTORY_SEPARATOR . $metadata['file'];

		// Use image src, if local file does not exist. @see https://github.com/insideout10/wordlift-plugin/issues/1149
		if ( ! file_exists( $path ) ) {
			$this->log->warn( "Featured image file $path doesn't exist for post $post_id." );

			$attachment_image_src = wp_get_attachment_image_src( $thumbnail_id, '' );
			if ( $attachment_image_src ) {
				return array(
					'url'    => $attachment_image_src[0],
					'width'  => $attachment_image_src[1],
					'height' => $attachment_image_src[2],
				);
			}

			// Bail out if we cant fetch wp_get_attachment_image_src
			return false;

		}

		// Try to get the image editor and bail out if the editor cannot be instantiated.
		$original_file_editor = wp_get_image_editor( $path );
		if ( is_wp_error( $original_file_editor ) ) {
			$this->log->warn( "Cannot instantiate WP Image Editor on file $path for post $post_id." );

			return false;
		}

		// Generate the publisher logo filename, we cannot use the `width` and `height` because we're scaling
		// and we don't actually know the end values.
		$publisher_logo_path = $original_file_editor->generate_filename( '-publisher-logo' );

		// If the file doesn't exist yet, create it.
		if ( ! file_exists( $publisher_logo_path ) ) {
			$original_file_editor->resize( 600, 60 );
			$original_file_editor->save( $publisher_logo_path );
		}

		// Try to get the image editor and bail out if the editor cannot be instantiated.
		$publisher_logo_editor = wp_get_image_editor( $publisher_logo_path );
		if ( is_wp_error( $publisher_logo_editor ) ) {
			$this->log->warn( "Cannot instantiate WP Image Editor on file $publisher_logo_path for post $post_id." );

			return false;
		}

		// Get the actual size.
		$size = $publisher_logo_editor->get_size();

		// Finally return the array with data.
		return array(
			'url'    => $uploads_dir['baseurl'] . substr( $publisher_logo_path, strlen( $uploads_dir['basedir'] ) ),
			'width'  => $size['width'],
			'height' => $size['height'],
		);
	}

	/**
	 * @param $references
	 * @param $post
	 * @param $jsonld
	 *
	 * @return void
	 */
	private function set_mentions_and_about( $references, $post, &$jsonld ) {

		if ( count( $references ) === 0 ) {
			return;
		}

		// Prepare the `about` and `mentions` array.
		$mentions = array();
		$about    = array();

		// If the entity is in the title, then it should be an `about`.
		foreach ( $references as $reference ) {

			if ( ! $reference instanceof Reference ) {
				// This condition should never be reached.
				continue;
			}

			// Get the entity labels.
			$labels = Wordlift_Entity_Service::get_instance()->get_labels( $reference->get_id(), $reference->get_type() );
			// Get the entity URI.
			$item = array(
				'@id' => Wordlift_Entity_Service::get_instance()->get_uri( $reference->get_id(), $reference->get_type() ),
			);

			$escaped_labels = array_map(
				function ( $value ) {
					return preg_quote( $value, '/' );
				},
				$labels
			);

			$matches = false;

			// When the title is empty, then we shouldn't yield a match to about section.
			if ( array_filter( $escaped_labels ) ) {
				// Check if the labels match any part of the title.
				$matches = 1 === preg_match( '/' . implode( '|', $escaped_labels ) . '/', $post->post_title );
			}

			// If the title matches, assign the entity to the about, otherwise to the mentions.
			if ( $matches ) {
				$about[] = $item;
			} else {
				$mentions[] = $item;
			}
		}

		// If we have abouts, assign them to the JSON-LD.
		if ( 0 < count( $about ) ) {
			$jsonld['about'] = $about;
		}

		// If we have mentions, assign them to the JSON-LD.
		if ( 0 < count( $mentions ) ) {
			$jsonld['mentions'] = $mentions;
		}

		return $jsonld;
	}

	/**
	 * Convert references to abstract data type if we find any.
	 *
	 * @param $references array<int|Reference>
	 *
	 * @return Reference[]
	 */
	private function convert_references( $references ) {
		return array_map(
			function ( $reference ) {
				// Legacy code may still push numerical references to this
				// $references variable, so convert it to post references.
				if ( is_numeric( $reference ) ) {
					  return new Post_Reference( $reference );
				}

				return $reference;

			},
			$references
		);
	}

}
