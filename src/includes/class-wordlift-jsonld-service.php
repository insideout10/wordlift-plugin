<?php
/**
 * Define the Wordlift_Jsonld_Service class to support JSON-LD.
 *
 * @since   3.8.0
 * @package Wordlift
 */

use Wordlift\Content\Wordpress\Wordpress_Content_Service;
use Wordlift\Jsonld\Graph;
use Wordlift\Jsonld\Jsonld_Context_Enum;
use Wordlift\Object_Type_Enum;
use Wordlift\Relation\Relation;
use Wordlift\Relation\Relations;

/**
 * This class exports an entity using JSON-LD.
 *
 * @since 3.8.0
 */
class Wordlift_Jsonld_Service {

	/**
	 * Creative work types.
	 *
	 * @var string[]
	 */
	private static $creative_work_types = array(
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
		'CreativeWork',
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

	/**
	 * The singleton instance for the JSON-LD service.
	 *
	 * @since 3.15.1
	 *
	 * @var \Wordlift_Jsonld_Service $instance The singleton instance for the JSON-LD service.
	 */
	private static $instance;

	/**
	 * A {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.8.0
	 * @access private
	 * @var Wordlift_Entity_Service $entity_service A {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * A {@link Wordlift_Term_JsonLd_Adapter} instance.
	 *
	 * @since  3.32.0
	 * @access private
	 * @var Wordlift_Term_JsonLd_Adapter $entity_service A {@link Wordlift_Term_JsonLd_Adapter} instance.
	 */
	private $term_jsonld_adapter;

	/**
	 * A {@link Wordlift_Post_Converter} instance.
	 *
	 * @since  3.8.0
	 * @access private
	 * @var \Wordlift_Post_Converter A {@link Wordlift_Post_Converter} instance.
	 */
	private $converter;

	/**
	 * A {@link Wordlift_Website_Jsonld_Converter} instance.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift_Website_Jsonld_Converter A {@link Wordlift_Website_Jsonld_Converter} instance.
	 */
	private $website_converter;

	/**
	 * Create a JSON-LD service.
	 *
	 * @param \Wordlift_Entity_Service           $entity_service A {@link Wordlift_Entity_Service} instance.
	 * @param \Wordlift_Post_Converter           $converter A {@link Wordlift_Uri_To_Jsonld_Converter} instance.
	 * @param \Wordlift_Website_Jsonld_Converter $website_converter A {@link Wordlift_Website_Jsonld_Converter} instance.
	 * @param \Wordlift_Term_JsonLd_Adapter      $term_jsonld_adapter
	 *
	 * @since 3.8.0
	 */
	public function __construct( $entity_service, $converter, $website_converter, $term_jsonld_adapter ) {
		$this->entity_service      = $entity_service;
		$this->converter           = $converter;
		$this->website_converter   = $website_converter;
		$this->term_jsonld_adapter = $term_jsonld_adapter;
		self::$instance            = $this;
	}

	/**
	 * Get the singleton instance for the JSON-LD service.
	 *
	 * @return \Wordlift_Jsonld_Service The singleton instance for the JSON-LD service.
	 * @since 3.15.1
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Process calls to the AJAX 'wl_jsonld' endpoint.
	 *
	 * @since 3.8.0
	 */
	public function get() {
		// Clear the buffer to be sure someone doesn't mess with our response.
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/406.
		// See https://codex.wordpress.org/AJAX_in_Plugins.
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		@ob_clean();

		// Get the parameter from the request.
		$is_homepage = isset( $_REQUEST['homepage'] ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$post_id     = isset( $_REQUEST['id'] ) && is_numeric( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : null; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

		// Send the generated JSON-LD.
		$this->send_jsonld( $this->get_jsonld( $is_homepage, $post_id ) );

	}

	/**
	 * A close of WP's own `wp_send_json` function which uses `application/ld+json` as content type.
	 *
	 * @param mixed $response Variable (usually an array or object) to encode as JSON,
	 *                           then print and die.
	 *
	 * @since 3.18.5
	 */
	private function send_jsonld( $response ) {
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		@header( 'Content-Type: application/ld+json; charset=' . get_option( 'blog_charset' ) );
		echo wp_json_encode( $response );
		if ( apply_filters( 'wp_doing_ajax', defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			wp_die();
		} else {
			die;
		}
	}

	/**
	 * Get the JSON-LD.
	 *
	 * @param bool     $is_homepage Whether the JSON-LD for the homepage is being requested.
	 * @param int|null $post_id The JSON-LD for the specified {@link WP_Post} id.
	 * @param int      $context A context for the JSON-LD generation, valid values in Jsonld_Context_Enum.
	 *
	 * @return array A JSON-LD structure.
	 * @since 3.15.1
	 */
	public function get_jsonld( $is_homepage = false, $post_id = null, $context = Jsonld_Context_Enum::UNKNOWN ) {

		/**
		 * Filter name: wl_before_get_jsonld
		 *
		 * @var bool $is_homepage Whether the JSON-LD for the homepage is being requested.
		 * @var int|null $post_id The JSON-LD for the specified {@link WP_Post} id.
		 * @var int $context A context for the JSON-LD generation, valid values in Jsonld_Context_Enum.
		 *
		 * @since 3.52.7
		 */
		do_action( 'wl_before_get_jsonld', $is_homepage, $post_id, $context );

		// Tell NewRelic to ignore us, otherwise NewRelic customers might receive
		// e-mails with a low apdex score.
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/521
		Wordlift_NewRelic_Adapter::ignore_apdex();

		// Switch to Website converter if is home page.
		if ( $is_homepage ) {
			/**
			 * Filter: 'wordlift_disable_website_json_ld' - Allow disabling of the json+ld output.
			 *
			 * @since  3.14.0
			 * @api    bool $display_search Whether or not to display json+ld search on the frontend.
			 */
			if ( apply_filters( 'wordlift_disable_website_json_ld', false ) ) {
				return array();
			}

			// Set a reference to the website_converter.
			$website_converter = $this->website_converter;

			// Send JSON-LD.
			return $website_converter->create_schema();
		}

		// If no id has been provided return an empty array.
		if ( ! isset( $post_id ) ) {
			return array();
		}

		// An array of references which is captured when converting an URI to a
		// json which we gather to further expand our json-ld.
		$references       = array();
		$references_infos = array();

		// Set a reference to the entity_to_jsonld_converter to use in the closures.
		$entity_to_jsonld_converter = $this->converter;

		$relations = new Relations();
		$jsonld    = $entity_to_jsonld_converter->convert( $post_id, $references, $references_infos, $relations );

		$graph = new Graph( $jsonld, $entity_to_jsonld_converter, Wordlift_Term_JsonLd_Adapter::get_instance() );

		$schema_type = is_array( $jsonld['@type'] ) ? $jsonld['@type'] : array( $jsonld['@type'] );

		// Add `about`/`mentions` only for `CreativeWork` and descendants.
		if ( array_intersect( $schema_type, self::$creative_work_types ) ) {

			foreach ( $relations->toArray() as $relation ) {

				// Setting about or mentions by label match is currently supported only for posts
				if ( Object_Type_Enum::POST !== $relation->get_object()->get_type() ) {
					continue;
				}

				// Add the `mentions`/`about` prop.
				$this->add_mention_or_about( $jsonld, $post_id, $relation );
			}
			$graph->set_main_jsonld( $jsonld );
		}

		$jsonld_arr = $graph->add_references( $references )
							->add_relations( $relations )
							->add_required_reference_infos( $references_infos )
							->render( $context );

		/**
		 * Filter name: wl_after_get_jsonld
		 *
		 * @return array
		 * @since 3.27.2
		 * @var $jsonld_arr array The final jsonld before outputting to page.
		 * @var $post_id int The post id for which the jsonld is generated.
		 */
		$jsonld_arr = apply_filters( 'wl_after_get_jsonld', $jsonld_arr, $post_id, $context );

		return $jsonld_arr;
	}

	/**
	 * Write the JSON-LD in the head.
	 *
	 * This function isn't actually used, but may be used to quickly enable writing the JSON-LD synchronously to the
	 * document head, using the `wp_head` hook.
	 *
	 * @since 3.18.5
	 */
	public function wp_head() {

		// Determine whether this is the home page or whether we're displaying a single post.
		$is_homepage = is_home() || is_front_page();
		$post_id     = is_singular() ? get_the_ID() : null;

		$jsonld = wp_json_encode( $this->get_jsonld( $is_homepage, $post_id, Jsonld_Context_Enum::PAGE ) );
		?>
		<script type="application/ld+json"><?php echo esc_html( $jsonld ); ?></script>
		<?php
	}

	/**
	 * @param array    $jsonld
	 * @param Relation $relation
	 *
	 * @return void
	 */
	private function add_mention_or_about( &$jsonld, $post_id, $relation ) {
		$content_service = Wordpress_Content_Service::get_instance();
		$entity_service  = Wordlift_Entity_Service::get_instance();

		$object     = $relation->get_object();
		$entity_uri = $content_service->get_entity_id( $object );
		$labels     = $entity_service->get_labels( $object->get_id(), $object->get_type() );

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
			$post    = get_post( $post_id );
			$matches = $this->check_title_match( $escaped_labels, $post->post_title );
		}

		if ( $entity_uri ) {
			// If the title matches, assign the entity to the about, otherwise to the mentions.
			$property_name              = $matches ? 'about' : 'mentions';
			$jsonld[ $property_name ]   = isset( $jsonld[ $property_name ] ) ? (array) $jsonld[ $property_name ] : array();
			$jsonld[ $property_name ][] = array( '@id' => $entity_uri );
		}

	}

	/**
	 * Check if the labels match any part of the title.
	 *
	 * @param $labels array The labels to check.
	 * @param $title string The title to check.
	 *
	 * @return boolean
	 */
	public function check_title_match( $labels, $title ) {

		// If the title is empty, then we shouldn't yield a match to about section.
		if ( empty( $title ) ) {
			return false;
		}

		// Check if the labels match any part of the title.
		return 1 === preg_match( '/\b(' . implode( '|', $labels ) . ')\b/iu', $title );

	}

}
