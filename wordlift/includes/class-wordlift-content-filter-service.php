<?php
/**
 * Services: Content Filter Service.
 *
 * Define the Wordlift_Content_Filter_Service class. This file is included from
 * the main class-wordlift.php file.
 *
 * @since   3.8.0
 * @package Wordlift
 */

use Wordlift\Content\Wordpress\Wordpress_Content_Service;
use Wordlift\Link\Link_Builder;
use Wordlift\Link\Object_Link_Provider;
use Wordlift\Object_Type_Enum;

/**
 * Define the Wordlift_Content_Filter_Service class which intercepts the
 * 'the_content' WP filter and mangles the content accordingly.
 *
 * @since   3.8.0
 * @package Wordlift
 */
class Wordlift_Content_Filter_Service {

	/**
	 * The pattern to find entities in text.
	 *
	 * @since 3.8.0
	 */
	const PATTERN = '/<(\\w+)[^<]*class="([^"]*)"\\sitemid=\"([^"]+)\"[^>]*>([^<]*)<\\/\\1>/i';

	/**
	 * A {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.8.0
	 * @access private
	 * @var \Wordlift_Entity_Service $entity_service A {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * The `link by default` setting.
	 *
	 * @since  3.13.0
	 * @access private
	 * @var bool True if link by default is enabled otherwise false.
	 */
	private $is_link_by_default;

	private $linked_entity_uris = array();

	/**
	 * The {@link Wordlift_Entity_Uri_Service} instance.
	 *
	 * @since  3.16.3
	 * @access private
	 * @var \Wordlift_Entity_Uri_Service $entity_uri_service The {@link Wordlift_Entity_Uri_Service} instance.
	 */
	private $entity_uri_service;

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since 3.16.0
	 *
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;
	/**
	 * @var Object_Link_Provider
	 */
	private $object_link_provider;

	/**
	 * Create a {@link Wordlift_Content_Filter_Service} instance.
	 *
	 * @param \Wordlift_Entity_Service     $entity_service The {@link Wordlift_Entity_Service} instance.
	 * @param \Wordlift_Entity_Uri_Service $entity_uri_service The {@link Wordlift_Entity_Uri_Service} instance.
	 *
	 * @since 3.8.0
	 */
	protected function __construct( $entity_service, $entity_uri_service ) {

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		$this->entity_service       = $entity_service;
		$this->entity_uri_service   = $entity_uri_service;
		$this->object_link_provider = Object_Link_Provider::get_instance();

	}

	private static $instance = null;

	/**
	 * Get the {@link Wordlift_Content_Filter_Service} singleton instance.
	 *
	 * @return \Wordlift_Content_Filter_Service The {@link Wordlift_Content_Filter_Service} singleton instance.
	 * @since 3.14.2
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self( Wordlift_Entity_Service::get_instance(), Wordlift_Entity_Uri_Service::get_instance() );
		}

		return self::$instance;
	}

	/**
	 * Mangle the content by adding links to the entity pages. This function is
	 * hooked to the 'the_content' WP's filter.
	 *
	 * @param string $content The content being filtered.
	 *
	 * @return string The filtered content.
	 * @since 3.8.0
	 */
	public function the_content( $content ) {
		$this->log->trace( 'Filtering content [ ' . ( is_singular() ? 'yes' : 'no' ) . ' ]...' );

		// Links should be added only on the front end and not for RSS.
		if ( is_feed() || is_admin() || is_search() ) {
			return $content;
		}

		// Preload the `link by default` setting.
		$this->is_link_by_default = Wordlift_Configuration_Service::get_instance()->is_link_by_default();

		// Reset the array of of entity post ids linked from the post content.
		// This is used to avoid linking more the once the same post.
		$this->linked_entity_uris = array();

		// Preload URIs.
		$matches = array();
		preg_match_all( self::PATTERN, $content, $matches );

		// Bail out if there are no URIs.
		if ( empty( $matches[3] ) ) {
			return $content;
		}

		// Replace each match of the entity tag with the entity link. If an error
		// occurs fail silently returning the original content.
		$maybe_content = preg_replace_callback(
			self::PATTERN,
			array(
				$this,
				'link',
			),
			$content
		);

		return $maybe_content ? $maybe_content : $content;
	}

	/**
	 * Get the entity match and replace it with a page link.
	 *
	 * @param array $matches An array of matches.
	 *
	 * @return string The replaced text with the link to the entity page.
	 * @since 3.8.0
	 */
	private function link( $matches ) {

		// Get the entity itemid URI and label.
		$css_class = $matches[2];
		$uri       = $matches[3];
		$label     = $matches[4];

		/**
		 * If the entity is already linked, dont send query to the db.
		 */
		if ( $this->is_already_linked( $uri ) ) {
			return $label;
		}

		$link = - 1 < strpos( $css_class, 'wl-link' );

		// If the entity should not be linked and link by default is also disabled,
		// then don't lookup the entity on the table.
		if ( ! $this->is_link_by_default && ! $link ) {
			return $label;
		}

		$content_service = Wordpress_Content_Service::get_instance();
		$content         = $content_service->get_by_entity_id_or_same_as( $uri );

		// If no content is found, return the label, that is _remove the annotation_.
		if ( ! is_object( $content ) ) {
			return $label;
		}

		$object_id   = $content->get_id();
		$object_type = $content->get_object_type_enum();

		$no_link = - 1 < strpos( $css_class, 'wl-no-link' );

		// Don't link if links are disabled and the entity is not link or the
		// entity is do not link.
		$dont_link = ( ! $this->is_link_by_default && ! $link ) || $no_link;

		// Return the label if it's don't link.
		if ( $dont_link ) {
			return $label;
		}

		/**
		 * @since 3.32.0
		 * Object_ids are prefixed with object_type to prevent conflicts.
		 */
		$this->linked_entity_uris[] = $uri;

		// Get the link.
		$href = Wordlift_Post_Adapter::get_production_permalink( $object_id, $object_type );

		// Bail out if the `$href` has been reset.
		if ( empty( $href ) ) {
			return $label;
		}

		return Link_Builder::create( $uri, $object_id )
						   ->label( $label )
						   ->href( $href )
						   ->generate_link();
	}

	/**
	 * Get a string to be used as a title attribute in links to a post
	 *
	 * @param int    $post_id The post id of the post being linked.
	 * @param string $ignore_label A label to ignore.
	 *
	 * @return string    The title to be used in the link. An empty string when
	 *                    there is no alternative that is not the $ignore_label.
	 * @deprecated 3.32.0 Use object link provider to get the link title for getting link
	 * title for different types.
	 * @since 3.15.0
	 *
	 * As of 3.32.0 this method is not used anywhere in the core, this should be removed
	 * from tests and companion plugins.
	 */
	public function get_link_title( $post_id, $ignore_label, $object_type = Object_Type_Enum::POST ) {
		return $this->object_link_provider->get_link_title( $post_id, $ignore_label, $object_type );
	}

	/**
	 * Get the entity URIs (configured in the `itemid` attribute) contained in
	 * the provided content.
	 *
	 * @param string $content The content.
	 *
	 * @return array An array of URIs.
	 * @since 3.14.2
	 */
	public function get_entity_uris( $content ) {

		$matches = array();
		preg_match_all( self::PATTERN, $content, $matches );

		// We need to use `array_values` here in order to avoid further `json_encode`
		// to turn it into an object (since if the 3rd match isn't found the index
		// is not sequential.
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/646.
		return array_values( array_unique( $matches[3] ) );
	}

	/**
	 * @param $entity_uri
	 *
	 * @return bool
	 */
	private function is_already_linked( $entity_uri ) {
		return in_array( $entity_uri, $this->linked_entity_uris, true );
	}

}
