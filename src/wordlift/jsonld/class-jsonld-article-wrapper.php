<?php

namespace Wordlift\Jsonld;

use Wordlift_Jsonld_Service;
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
	 * @var Wordlift_Jsonld_Service
	 */
	private $jsonld_service;

	public function __construct( $post_to_jsonld_converter, $jsonld_service ) {

		$this->post_to_jsonld_converter = $post_to_jsonld_converter;
		$this->jsonld_service           = $jsonld_service;

		add_filter( 'wl_after_get_jsonld', array( $this, 'after_get_jsonld' ), 10, 2 );

	}

	public function after_get_jsonld( $jsonld, $post_id ) {

		if ( ! is_array( $jsonld ) || empty( $jsonld ) ) {
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
		$article_jsonld          = $this->post_to_jsonld_converter->convert( $post_id );
		$article_jsonld['@id']   = $post_jsonld['@id'] . '/wrapper';
		$article_jsonld['about'] = array( '@id' => $post_jsonld['@id'] );
		array_unshift( $jsonld, $article_jsonld );

		return $jsonld;
	}

	public function is_article( $schema_types ) {

		$array_intersect = array_intersect( self::$article_types, ( array ) $schema_types );

		return ! empty( $array_intersect );
	}

}