<?php

namespace Wordlift\Jsonld;

use Wordlift_Post_To_Jsonld_Converter;

class Jsonld_Article_Wrapper {

	private static $article_types = array(
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
		'APIReference'
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

	public function __construct( $post_to_jsonld_converter, $cached_postid_to_jsonld_converter ) {

		$this->post_to_jsonld_converter = $post_to_jsonld_converter;

		add_filter( 'wl_after_get_jsonld', array( $this, 'after_get_jsonld' ), 10, 3 );

		$this->cached_postid_to_jsonld_converter = $cached_postid_to_jsonld_converter;

		$this->entity_uri_service = \Wordlift_Entity_Uri_Service::get_instance();
	}

	public function after_get_jsonld( $jsonld, $post_id, $context ) {


		if ( Jsonld_Context_Enum::PAGE !== $context || ! is_array( $jsonld ) || ! isset( $jsonld[0] )
		     || ! is_array( $jsonld[0] ) ) {
			return $jsonld;
		}

		// Copy the 1st array element
		$post_jsonld = $jsonld[0];

		// Don't wrap in article if the json-ld is already about an Article (or its descendants). `@type` must be
		// in the schema.org context, i.e. `Article`, not `http://schema.org/Article`.
		if ( ! isset( $post_jsonld['@id'] ) || ! isset( $post_jsonld['@type'] ) || $this->is_article( $post_jsonld['@type'] ) ) {
			return $jsonld;
		}

		// Convert the post as Article.
		$article_jsonld = $this->post_to_jsonld_converter->convert( $post_id );

		$article_jsonld['@id'] = $post_jsonld['@id'] . '#article';
		// Reset the type, since by default the type assigned via the Entity Type taxonomy is used.
		$article_jsonld['@type'] = 'Article';
		$article_jsonld['about'] = array( '@id' => $post_jsonld['@id'] );


		// Copy over the URLs.
		if ( isset( $post_jsonld['url'] ) ) {
			$article_jsonld['url'] = $post_jsonld['url'];
		}

		array_unshift( $jsonld, $article_jsonld );

		$author_jsonld = $this->get_author_linked_entity( $article_jsonld );

		/**
		 * The author entities can be present in graph for some entity types
		 * for Person and Organization, so check before we add it to graph.
		 * reference : https://schema.org/author
		 */
		if ( $author_jsonld && ! $this->is_author_entity_present_in_graph( $jsonld, $article_jsonld['author']['@id'] )) {
			$jsonld[] = $author_jsonld;
		}

		return $jsonld;
	}

	private function is_article( $schema_types ) {

		$array_intersect = array_intersect( self::$article_types, ( array ) $schema_types );

		return ! empty( $array_intersect );
	}

	private function get_author_linked_entity( $article_jsonld ) {
		if ( ! array_key_exists( 'author', $article_jsonld ) ) {
			return false;
		}

		$author = $article_jsonld['author'];

		if ( count( array_keys( $author ) ) !== 1 || ! array_key_exists( '@id', $author ) ) {
			return false;
		}

		$author_linked_entity_id = $author['@id'];

		$author_entity_post = $this->entity_uri_service->get_entity( $author_linked_entity_id );

		if ( ! $author_entity_post instanceof \WP_Post ) {
			return false;
		}

		return $this->cached_postid_to_jsonld_converter->convert( $author_entity_post->ID );

	}

	private function is_author_entity_present_in_graph( $jsonld, $author_entity_id ) {

		foreach ( $jsonld as $item ) {
			if ( $item && array_key_exists('@id', $item ) && $item['@id'] === $author_entity_id ) {
				return true;
			}
		}
		return false;
	}

}