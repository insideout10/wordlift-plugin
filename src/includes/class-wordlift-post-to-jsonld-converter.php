<?php
/**
 * Converters: Post to JSON-LD Converter.
 *
 * This file defines a converter from an entity {@link WP_Post} to a JSON-LD array.
 *
 * @since   3.10.0
 * @package Wordlift
 */

use Wordlift\Jsonld\Reference;
use Wordlift\Relation\Relations;
use Wordlift_Publisher_Service;

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
	 * @var false
	 */
	private $disable_convert_filters;

	/**
	 * Wordlift_Post_To_Jsonld_Converter constructor.
	 *
	 * @param Wordlift_Entity_Type_Service $entity_type_service A {@link Wordlift_Entity_Type_Service} instance.
	 * @param Wordlift_User_Service        $user_service A {@link Wordlift_User_Service} instance.
	 * @param Wordlift_Attachment_Service  $attachment_service A {@link Wordlift_Attachment_Service} instance.
	 *
	 * @since 3.10.0
	 */
	public function __construct( $entity_type_service, $user_service, $attachment_service, $disable_convert_filters = false ) {
		parent::__construct( $entity_type_service, $user_service, $attachment_service, Wordlift_Property_Getter_Factory::create() );
		$this->disable_convert_filters = $disable_convert_filters;

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
	public function convert( $post_id, &$references = array(), &$references_infos = array(), $relations = null ) {

		// Get the post instance.
		$post = get_post( $post_id );
		if ( null === $post ) {
			// Post not found.
			return null;
		}

		// Get the base JSON-LD and the list of entities referenced by this entity.
		$jsonld = parent::convert( $post_id, $references, $references_infos, $relations );

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
		}

		// Set the publisher.
		$this->set_publisher( $jsonld );

		/**
		 * Call the `wl_post_jsonld_author` filter.
		 *
		 * This filter checks if there are co-authors or a single author and
		 * returns a JSON-LD fragment for the author(s).
		 *
		 * @param array $value {
		 *
		 * @type array $jsonld The JSON-LD structure.
		 * @type int[] $references An array of post IDs.
		 * }
		 *
		 * @param int $post_id The {@link WP_Post} `id`.
		 *
		 * @since 3.51.4
		 *
		 * @see https://www.geeklab.info/2010/04/wordpress-pass-variables-by-reference-with-apply_filter/
		 */
		$ret_val = apply_filters(
			'wl_jsonld_author',
			array(
				'author'     => $this->get_author( $post->post_author, $references ),
				'references' => $references,
			),
			$post_id
		);

		// Set the values returned by the filter.
		$jsonld['author'] = $ret_val['author'];
		$references       = $ret_val['references'];

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
		 * @type Relations $relations A set of `Relation`s.
		 * }
		 * @since 3.25.0
		 * @since 3.43.0 The filter provides a `Relations` instance.
		 *
		 * @see https://www.geeklab.info/2010/04/wordpress-pass-variables-by-reference-with-apply_filter/
		 *
		 * @api
		 */
		$ret_val = apply_filters(
			'wl_post_jsonld_array',
			array(
				'jsonld'           => $jsonld,
				'references'       => $references, // This one is only an array of post IDs.
				'references_infos' => $references_infos,
				'relations'        => $relations,
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
		// @@todo add a &$references argument and push the publisher_id in here
		// the `publisher` property should only reference the publisher e.g. { "@id": "https://data.example.org/dataset/publisher" }

		// If the publisher id isn't set don't do anything.
		$publisher_id = Wordlift_Configuration_Service::get_instance()->get_publisher_id();
		if ( empty( $publisher_id ) ) {
			return;
		}

		// Get the post instance.
		$post = get_post( $publisher_id );
		if ( ! is_a( $post, '\WP_Post' ) ) {
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
		$publisher_logo = Wordlift_Publisher_Service::get_instance()->get_publisher_logo( $post->ID );

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

}
