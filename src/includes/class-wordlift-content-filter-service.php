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

use Wordlift\Link\Link_Builder;
use Wordlift\Link\Object_Link_Provider;

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
	 * The {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.13.0
	 * @access private
	 * @var \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	private $configuration_service;

	/**
	 * The `link by default` setting.
	 *
	 * @since  3.13.0
	 * @access private
	 * @var bool True if link by default is enabled otherwise false.
	 */
	private $is_link_by_default;

	private $linked_object_ids = array();

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
	 * The {@link Wordlift_Content_Filter_Service} singleton instance.
	 *
	 * @since  3.14.2
	 * @access private
	 * @var \Wordlift_Content_Filter_Service $instance The {@link Wordlift_Content_Filter_Service} singleton instance.
	 */
	private static $instance;
	/**
	 * @var Object_Link_Provider
	 */
	private $object_link_provider;

	/**
	 * Create a {@link Wordlift_Content_Filter_Service} instance.
	 *
	 * @param \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} instance.
	 * @param \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 * @param \Wordlift_Entity_Uri_Service $entity_uri_service The {@link Wordlift_Entity_Uri_Service} instance.
	 *
	 * @since 3.8.0
	 *
	 */
	public function __construct( $entity_service, $configuration_service, $entity_uri_service ) {

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		$this->entity_service        = $entity_service;
		$this->configuration_service = $configuration_service;
		$this->entity_uri_service    = $entity_uri_service;
		$this->object_link_provider  = Object_Link_Provider::get_instance();
		self::$instance              = $this;

	}

	/**
	 * Get the {@link Wordlift_Content_Filter_Service} singleton instance.
	 *
	 * @return \Wordlift_Content_Filter_Service The {@link Wordlift_Content_Filter_Service} singleton instance.
	 * @since 3.14.2
	 */
	public static function get_instance() {

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
	 *
	 */
	public function the_content( $content ) {

		$this->log->trace( "Filtering content [ " . ( is_singular() ? 'yes' : 'no' ) . " ]..." );

		// Links should be added only on the front end and not for RSS.
		if ( is_feed() ) {
			return $content;
		}

		// Preload the `link by default` setting.
		$this->is_link_by_default = $this->configuration_service->is_link_by_default();

		// Reset the array of of entity post ids linked from the post content.
		// This is used to avoid linking more the once the same post.
		$this->linked_object_ids = array();

		// Preload URIs.
		$matches = array();
		preg_match_all( self::PATTERN, $content, $matches );

		// Bail out if there are no URIs.
		if ( empty( $matches[3] ) ) {
			return $content;
		}

		// Preload the URIs.
		$this->entity_uri_service->preload_uris( $matches[3] );

		// Replace each match of the entity tag with the entity link. If an error
		// occurs fail silently returning the original content.
		$result = preg_replace_callback( self::PATTERN, array(
			$this,
			'link',
		), $content ) ?: $content;

		$this->entity_uri_service->reset_uris();

		return $result;
	}

	/**
	 * Get the entity match and replace it with a page link.
	 *
	 * @param array $matches An array of matches.
	 *
	 * @return string The replaced text with the link to the entity page.
	 * @since 3.8.0
	 *
	 */
	private function link( $matches ) {

		// Get the entity itemid URI and label.
		$css_class = $matches[2];
		$uri       = $matches[3];
		$label     = $matches[4];

		$object_type = $this->object_link_provider->get_object_type( $uri );

		if  ( ! $object_type ) {
			// Since we cant find the object type for the entity uri
			// it doesnt seem to exist on the local dataset, so return
			// the label without linking.
			return $label;
		}

		$object_id  = $this->object_link_provider->get_object_id_by_type( $uri, $object_type );

		$object_id_unique_identifier = $object_type . "_" . $object_id;

		$no_link = - 1 < strpos( $css_class, 'wl-no-link' )
		           // Do not link if already linked.
		           || $this->is_already_linked( $object_id_unique_identifier );
		$link    = - 1 < strpos( $css_class, 'wl-link' );

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
		$this->linked_object_ids[] = $object_id_unique_identifier;

		// Get the link.
		$href = Wordlift_Post_Adapter::get_production_permalink( $object_id );

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
	 * Get a `title` attribute with an alternative label for the link.
	 *
	 * If an alternative title isn't available an empty string is returned.
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 * @param string $label The main link label.
	 *
	 * @return string A `title` attribute with an alternative label or an empty
	 *                string if none available.
	 * @since 3.15.0
	 *
	 */
	private function get_title_attribute( $post_id, $label ) {

		// Get an alternative title.
		$title = $this->object_link_provider->get_link_title( $post_id, $label );
		if ( ! empty( $title ) ) {
			return 'title="' . esc_attr( $title ) . '"';
		}

		return '';
	}

	/**
	 * Get a string to be used as a title attribute in links to a post
	 *
	 * @param int $post_id The post id of the post being linked.
	 * @param string $ignore_label A label to ignore.
	 *
	 * @return string    The title to be used in the link. An empty string when
	 *                    there is no alternative that is not the $ignore_label.
	 * @deprecated 3.32.0 Use object link provider to get the link title for getting link
	 * title for different types.
	 * @since 3.15.0
	 *
	 */
	function get_link_title( $post_id, $ignore_label ) {
		return $this->object_link_provider->get_link_title( $post_id, $ignore_label );
	}

	/**
	 * Get the entity URIs (configured in the `itemid` attribute) contained in
	 * the provided content.
	 *
	 * @param string $content The content.
	 *
	 * @return array An array of URIs.
	 * @since 3.14.2
	 *
	 */
	public function get_entity_uris( $content ) {

		$matches = array();
		preg_match_all( Wordlift_Content_Filter_Service::PATTERN, $content, $matches );

		// We need to use `array_values` here in order to avoid further `json_encode`
		// to turn it into an object (since if the 3rd match isn't found the index
		// is not sequential.
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/646.
		return array_values( array_unique( $matches[3] ) );
	}

	/**
	 * @param $post_id
	 *
	 * @return string
	 */
	private function get_attributes_for_link( $post_id ) {
		/**
		 * Allow 3rd parties to add additional attributes to the anchor link.
		 *
		 * @since 3.26.0
		 */
		$default_attributes = array(
			'id' => implode( ';', array_merge(
				(array) $this->entity_service->get_uri( $post_id ),
				get_post_meta( $post_id, Wordlift_Schema_Service::FIELD_SAME_AS )
			) )
		);
		$attributes         = apply_filters( 'wl_anchor_data_attributes', $default_attributes, $post_id );
		$attributes_html    = '';
		foreach ( $attributes as $key => $value ) {
			$attributes_html .= ' data-' . esc_html( $key ) . '="' . esc_attr( $value ) . '" ';
		}

		return $attributes_html;
	}

	/**
	 * @param $post_id
	 *
	 * @return bool
	 */
	private function is_already_linked( $post_id ) {
		return in_array( $post_id, $this->linked_object_ids );
	}

}
