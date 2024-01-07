<?php

namespace Wordlift\Jsonld;

use Wordlift\Relation\Relations;
use Wordlift_Post_To_Jsonld_Converter;

class Jsonld_Article_Wrapper {

	public static $article_types = array(
		'Article',
		'AdvertiserContentArticle',
		'NewsArticle',
		'AnalysisNewsArticle',
		'AskPublicNewsArticle',
		'BackgroundNewsArticle',
		'OpinionNewsArticle',
		'ReportageNewsArticle',
		'ReviewNewsArticle',
		'Report',
		'SatiricalArticle',
		'ScholarlyArticle',
		'MedicalScholarlyArticle',
		'SocialMediaPosting',
		'BlogPosting',
		'LiveBlogPosting',
		'DiscussionForumPosting',
		'TechArticle',
		'APIReference',
	);

	/**
	 * @var Wordlift_Post_To_Jsonld_Converter
	 */
	private $post_to_jsonld_converter;
	/**
	 * @var \Wordlift_Cached_Post_Converter
	 */
	private $cached_postid_to_jsonld_converter;
	/**
	 * @var \Wordlift_Entity_Uri_Service
	 */
	private $entity_uri_service;

	/**
	 * Jsonld_Article_Wrapper constructor.
	 *
	 * @param Wordlift_Post_To_Jsonld_Converter $post_to_jsonld_converter
	 * @param $cached_postid_to_jsonld_converter
	 */
	public function __construct( $post_to_jsonld_converter, $cached_postid_to_jsonld_converter ) {

		$this->post_to_jsonld_converter = $post_to_jsonld_converter->new_instance_with_filters_disabled();

		add_filter(
			'wl_after_get_jsonld',
			array(
				$this,
				'after_get_jsonld',
			),
			PHP_INT_MAX - 100,
			3
		);

		$this->cached_postid_to_jsonld_converter = $cached_postid_to_jsonld_converter;

		$this->entity_uri_service = \Wordlift_Entity_Uri_Service::get_instance();
	}

	public function after_get_jsonld( $jsonld, $post_id, $context ) {

		// Invalid data structure
		if ( ! is_array( $jsonld ) || ! isset( $jsonld[0] ) || ! is_array( $jsonld[0] ) ) {
			return $jsonld;
		}

		if ( Jsonld_Context_Enum::PAGE !== $context
			 // Returns true for "1", "true", "on" and "yes". Returns false otherwise.
			 && ! filter_input( INPUT_GET, 'article_wrapper', FILTER_VALIDATE_BOOLEAN ) ) {
			return $jsonld;
		}

		// Copy the 1st array element
		$post_jsonld = $jsonld[0];

		// Don't wrap in article if the json-ld is already about an Article (or its descendants). `@type` must be
		// in the schema.org context, i.e. `Article`, not `http://schema.org/Article`.
		if ( ! isset( $post_jsonld['@id'] ) || ! isset( $post_jsonld['@type'] ) || $this->is_article( $post_jsonld['@type'] ) ) {
			return $jsonld;
		}

		$references      = array();
		$reference_infos = array();

		// Convert the post as Article.
		$article_jsonld = $this->post_to_jsonld_converter->convert( $post_id, $references, $reference_infos, new Relations() );

		$article_jsonld['@id'] = $post_jsonld['@id'] . '#article';
		// Reset the type, since by default the type assigned via the Entity Type taxonomy is used.
		$article_jsonld['@type'] = 'Article';
		$article_jsonld['about'] = array( '@id' => $post_jsonld['@id'] );

		// Copy over the URLs.
		if ( isset( $post_jsonld['url'] ) ) {
			$article_jsonld['url'] = $post_jsonld['url'];
		}

		array_unshift( $jsonld, $article_jsonld );

		// Only add authors if the key is present.
		if ( isset( $article_jsonld['author'] ) ) {
			$this->add_author_data_if_missing( $jsonld, $article_jsonld['author'] );
		}

		return $jsonld;
	}

	/**
	 * @param array            $jsonld
	 * @param array|false|null $author Either a keyed or an indexed array.
	 *
	 * @return void
	 */
	private function add_author_data_if_missing( &$jsonld, $author ) {

		// For each author when if the `@id` is present and if not, add the author data.
		$entities_ids = $this->get_list_of_entities_ids( $author );
		foreach ( $entities_ids as $entity_id ) {
			if ( $this->is_author_entity_present_in_graph( $jsonld, $entity_id ) ) {
				continue;
			}

			$author_data = $this->get_author_data_by_entity_id( $entity_id );

			if ( ! is_array( $author_data ) || empty( $author_data ) ) {
				continue;
			}
			$jsonld[] = $author_data;
		}

	}

	/**
	 * This function takes any input parameter and when it's an array, it'll return the @id properties in this array as
	 * an array of `@id`s.
	 *
	 * @param mixed $items Preferably an array, either keyed or indexed.
	 *
	 * @return array An array of `@id`s. Empty if not found.
	 */
	private function get_list_of_entities_ids( $items ) {
		// If this is not an array, then what are we doing here? we return an empty array.
		if ( ! is_array( $items ) || empty( $items ) ) {
			return array();
		}

		// If this is a keyed array and the `@id` is set, then we return that single `@id`.
		if ( isset( $items['@id'] ) ) {
			return array( $items['@id'] );
		}

		// Poor way to check whether this is an indexed array.
		if ( ! isset( $items[0] ) ) {
			return array();
		}

		// If this is an indexed array then we return the `@id` for all the items in the array.
		return array_filter( array_column( $items, '@id' ) );
	}

	private function is_article( $schema_types ) {

		$array_intersect = array_intersect( self::$article_types, (array) $schema_types );

		return ! empty( $array_intersect );
	}

	private function get_author_data_by_entity_id( $author_linked_entity_id ) {

		$author_entity_post = $this->entity_uri_service->get_entity( $author_linked_entity_id );

		if ( ! $author_entity_post instanceof \WP_Post ) {
			return false;
		}

		$references      = array();
		$reference_infos = array();

		return $this->cached_postid_to_jsonld_converter->convert(
			$author_entity_post->ID,
			$references,
			$reference_infos,
			new Relations()
		);

	}

	private function is_author_entity_present_in_graph( $jsonld, $author_entity_id ) {

		foreach ( $jsonld as $item ) {
			if ( $item && array_key_exists( '@id', $item ) && $item['@id'] === $author_entity_id ) {
				return true;
			}
		}

		return false;
	}

}
