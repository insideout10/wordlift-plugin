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

	public function __construct( $post_to_jsonld_converter ) {

		$this->post_to_jsonld_converter = $post_to_jsonld_converter;

		add_filter( 'wl_after_get_jsonld', array( $this, 'after_get_jsonld' ), 10, 3 );

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
		$article_jsonld        = $this->post_to_jsonld_converter->convert( $post_id );
		$article_jsonld['@id'] = $post_jsonld['@id'] . '/wrapper';
		// Reset the type, since by default the type assigned via the Entity Type taxonomy is used.
		$article_jsonld['@type'] = 'Article';
		$article_jsonld['about'] = array( '@id' => $post_jsonld['@id'] );

		// Copy over the URLs.
		if ( isset( $post_jsonld['url'] ) ) {
			$article_jsonld['url'] = $post_jsonld['url'];
		}

		array_unshift( $jsonld, $article_jsonld );

		return $jsonld;
	}

	private function is_article( $schema_types ) {

		$array_intersect = array_intersect( self::$article_types, ( array ) $schema_types );

		return ! empty( $array_intersect );
	}

}