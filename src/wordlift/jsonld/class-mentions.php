<?php
/**
 * This file adds the mentions property for all the entities which are descendant of creativework.
 *
 * @see https://github.com/insideout10/wordlift-plugin/issues/1557
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @package Wordlift\Jsonld
 * @since 3.37.1
 */

namespace Wordlift\Jsonld;

use Wordlift\Object_Type_Enum;
use Wordlift\Relation\Object_Relation_Service;

class Mentions {

	private static $schema_descendants = array(
		'AmpStory',
		'ArchiveComponent',
		'Article',
		'Atlas',
		'Blog',
		'Book',
		'Chapter',
		'Claim',
		'Clip',
		'Code',
		'Collection',
		'ComicStory',
		'Comment',
		'Conversation',
		'Course',
		'CreativeWorkSeason',
		'CreativeWorkSeries',
		'DataCatalog',
		'Dataset',
		'DefinedTermSet',
		'Diet',
		'DigitalDocument',
		'Drawing',
		'EducationalOccupationalCredential',
		'Episode',
		'ExercisePlan',
		'Game',
		'Guide',
		'HowTo',
		'HowToDirection',
		'HowToSection',
		'HowToStep',
		'HowToTip',
		'HyperToc',
		'HyperTocEntry',
		'LearningResource',
		'Legislation',
		'Manuscript',
		'Map',
		'MathSolver',
		'MediaObject',
		'Menu',
		'MenuSection',
		'Message',
		'Movie',
		'MusicComposition',
		'MusicPlaylist',
		'MusicRecording',
		'Painting',
		'Photograph',
		'Play',
		'Poster',
		'PublicationIssue',
		'PublicationVolume',
		'Quotation',
		'Review',
		'Sculpture',
		'Season',
		'SheetMusic',
		'ShortStory',
		'SoftwareApplication',
		'SoftwareSourceCode',
		'SpecialAnnouncement',
		'Thesis',
		'TvSeason',
		'TvSeries',
		'VisualArtwork',
		'WebContent',
		'WebPage',
		'WebPageElement',
		'WebSite',
		'AdvertiserContentArticle',
		'NewsArticle',
		'Report',
		'SatiricalArticle',
		'ScholarlyArticle',
		'SocialMediaPosting',
		'TechArticle',
		'AnalysisNewsArticle',
		'AskPublicNewsArticle',
		'BackgroundNewsArticle',
		'OpinionNewsArticle',
		'ReportageNewsArticle',
		'ReviewNewsArticle',
		'MedicalScholarlyArticle',
		'BlogPosting',
		'DiscussionForumPosting',
		'LiveBlogPosting',
		'ApiReference',
		'Audiobook',
		'MovieClip',
		'RadioClip',
		'TvClip',
		'VideoGameClip',
		'ProductCollection',
		'ComicCoverArt',
		'Answer',
		'CorrectionComment',
		'Question',
		'PodcastSeason',
		'RadioSeason',
		'TvSeason',
		'BookSeries',
		'MovieSeries',
		'Periodical',
		'PodcastSeries',
		'RadioSeries',
		'TvSeries',
		'VideoGameSeries',
		'ComicSeries',
		'Newspaper',
		'DataFeed',
		'CompleteDataFeed',
		'CategoryCodeSet',
		'NoteDigitalDocument',
		'PresentationDigitalDocument',
		'SpreadsheetDigitalDocument',
		'TextDigitalDocument',
		'PodcastEpisode',
		'RadioEpisode',
		'TvEpisode',
		'VideoGame',
		'Recipe',
		'Course',
		'Quiz',
		'LegislationObject',
		'AudioObject',
		'DModel',
		'DataDownload',
		'ImageObject',
		'LegislationObject',
		'MusicVideoObject',
		'VideoObject',
		'Audiobook',
		'Barcode',
		'EmailMessage',
		'MusicAlbum',
		'MusicRelease',
		'ComicIssue',
		'ClaimReview',
		'CriticReview',
		'EmployerReview',
		'MediaReview',
		'Recommendation',
		'UserReview',
		'ReviewNewsArticle',
		'MobileApplication',
		'VideoGame',
		'WebApplication',
		'CoverArt',
		'ComicCoverArt',
		'HealthTopicContent',
		'AboutPage',
		'CheckoutPage',
		'CollectionPage',
		'ContactPage',
		'FaqPage',
		'ItemPage',
		'MedicalWebPage',
		'ProfilePage',
		'QaPage',
		'RealEstateListing',
		'SearchResultsPage',
		'MediaGallery',
		'ImageGallery',
		'VideoGallery',
		'SiteNavigationElement',
		'Table',
		'WpAdBlock',
		'WpFooter',
		'WpHeader',
		'WpSideBar',
	);

	public function __construct() {
		add_filter( 'wl_after_get_jsonld', array( $this, 'wl_after_get_jsonld' ), 10, 2 );
	}

	public function wl_after_get_jsonld( $jsonld, $post_id ) {

		if ( count( $jsonld ) === 0 || ! is_array( $jsonld[0] ) || ! array_key_exists( '@type', $jsonld[0] ) || array_key_exists( 'mentions', $jsonld[0] ) ) {
			return $jsonld;
		}

		$type = $jsonld[0]['@type'];

		if ( ! $this->entity_is_descendant_of_creative_work( $type ) && ! $this->entity_is_creative_work( $type ) ) {
			return $jsonld;
		}

		$entity_references = Object_Relation_Service::get_instance()
													->get_references( $post_id, Object_Type_Enum::POST );

		$about_id = array();
		if ( array_key_exists( 'about', $jsonld[0] ) ) {
			foreach ( $jsonld[0]['about'] as $about ) {
				$about_id[] = $about['@id'];
			}
		}

		$jsonld[0]['mentions'] = array_values(
			array_filter(
				array_map(
					function ( $item ) use ( $about_id ) {
						$id = \Wordlift_Entity_Service::get_instance()->get_uri( $item->get_id() );
						if ( ! $id || in_array( $id, $about_id, true ) ) {
							return false;
						}

						return array( '@id' => $id );

					},
					$entity_references
				)
			)
		);

		// Remove mentions if the count is zero.
		if ( count( $jsonld[0]['mentions'] ) === 0 ) {
			unset( $jsonld[0]['mentions'] );
		}

		return $jsonld;
	}

	private function entity_is_descendant_of_creative_work( $type ) {
		if ( is_string( $type ) ) {
			$type = array( $type );
		}

		return count( array_intersect( $type, $this::$schema_descendants ) ) > 0;
	}

	private function entity_is_creative_work( $type ) {
		return ( 'CreativeWork' === $type ) || ( is_array( $type ) && in_array( 'CreativeWork', $type, true ) );
	}

}
