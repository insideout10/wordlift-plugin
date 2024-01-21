<?php

/**
 * Module:  Google Organization Knowledge Panel
 * Class:   About_Page_Organization_Filter
 *
 * @package Wordlift/modules/google-organization-kp
 *
 * @since 3.53.0
 */

namespace Wordlift\Modules\Google_Organization_Kp;

class About_Page_Organization_Filter {

	/**
	 * @var Organization_Extra_Fields_Service
	 */
	private $extra_fields_service;

	/**
	 * @var Wordlift_Publisher_Service
	 */
	private $publisher_service;

	/**
	 * @var Wordlift_Configuration_Service
	 */
	private $configuration_service;

	/**
	 * @var Wordlift_Entity_Service
	 */
	private $entity_service;

	/**
	 * @var Wordlift_Post_To_Jsonld_Converter
	 */
	private $post_jsonld_service;

	/**
	 * @var Wordlift_Schema_Service
	 */
	private $schema_service;

	/**
	 * @var Relations
	 */
	private $relations;

	/**
	 * @param Organization_Extra_Fields_Service $extra_fields_service
	 * @param Wordlift_Publisher_Service        $publisher_service
	 * @param Wordlift_Configuration_Service    $configuration_service
	 * @param Wordlift_Entity_Service           $entity_service
	 * @param Wordlift_Post_To_Jsonld_Converter $post_jsonld_service
	 * @param Wordlift_Schema_Service           $schema_service
	 * @param Relations                         $relations
	 */
	public function __construct(
		$extra_fields_service,
		$publisher_service,
		$configuration_service,
		$entity_service,
		$post_jsonld_service,
		$schema_service,
		$relations
	) {
		$this->extra_fields_service  = $extra_fields_service;
		$this->publisher_service     = $publisher_service;
		$this->configuration_service = $configuration_service;
		$this->entity_service        = $entity_service;
		$this->post_jsonld_service   = $post_jsonld_service;
		$this->schema_service        = $schema_service;
		$this->relations             = $relations;
	}

	/**
	 * Initialize hooks.
	 *
	 * `wl_after_get_jsonld` is the main  filter hook and gets called on almost all JSON-LD conversions.
	 * However, on `WebSite` conversion this hook gets bypassed, so we also hook into `wl_website_jsonld`.
	 *
	 * @since 3.53.0
	 */
	public function init() {
		add_filter( 'wl_website_jsonld', array( $this, '_add_organization_jsonld' ), 10, 2 );
		add_filter( 'wl_after_get_jsonld', array( $this, '_add_organization_jsonld' ), 10, 2 );
	}

	/**
	 * Utility function to check if the provided $post_id is the `About Us` page specific in the options.
	 *
	 * @param $post_id int The post ID.
	 *
	 * @return bool
	 *
	 * @since 3.53.0
	 */
	public function is_about_page( $post_id ) {
		$about_page_id = get_option( 'wl_about_page_id' );

		if ( ! $about_page_id || empty( $about_page_id ) ) {
			return false;
		}

		return $about_page_id === $post_id;
	}

	/**
	 * Utility function to check if the Publisher exists in the JSON-LD structure already.
	 *
	 * @param $jsonld       array JSON-LD structure.
	 * @param $publisher_id int   Publisher Post ID.
	 *
	 * @return bool
	 *
	 * @since 3.53.0
	 */
	public function is_publisher_entity_in_graph( $jsonld, $publisher_id ) {
		$publisher_uri = $this->entity_service->get_uri( $publisher_id );

		foreach ( $jsonld as $item ) {
			if ( $item && array_key_exists( '@id', $item ) && $item['@id'] === $publisher_uri ) {
				return true;
			}
		}

		return false;
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
	public function get_publisher_logo( $post_id ) {

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
	 * Add the extra fields to the Publisher JSON-LD structure.
	 *
	 * @param &$publisher_jsonld array Reference to the Publisher JSON-LD array within the main JSON-LD array.
	 * @param $publisher_id      int   The Publisher Post ID.
	 *
	 * @since 3.53.0
	 */
	public function expand_publisher_jsonld( &$publisher_jsonld, $publisher_id ) {

		// Get custom fields.
		$entity_service   = $this->entity_service;
		$schema_service   = $this->schema_service;
		$alternative_name = get_post_meta( $publisher_id, $entity_service::ALTERNATIVE_LABEL_META_KEY, true );
		$legal_name       = get_post_meta( $publisher_id, $schema_service::FIELD_LEGAL_NAME, true );
		$street_address   = get_post_meta( $publisher_id, $schema_service::FIELD_ADDRESS, true );
		$locality         = get_post_meta( $publisher_id, $schema_service::FIELD_ADDRESS_LOCALITY, true );
		$region           = get_post_meta( $publisher_id, $schema_service::FIELD_ADDRESS_REGION, true );
		$country          = get_post_meta( $publisher_id, $schema_service::FIELD_ADDRESS_COUNTRY, true );
		$postal_code      = get_post_meta( $publisher_id, $schema_service::FIELD_ADDRESS_POSTAL_CODE, true );
		$telephone        = get_post_meta( $publisher_id, $schema_service::FIELD_TELEPHONE, true );
		$email            = get_post_meta( $publisher_id, $schema_service::FIELD_EMAIL, true );

		// Set custom fields.

		// Add alternativeName if set.
		if ( ! empty( $alternative_name ) ) {
			$publisher_jsonld['alternateName'] = $alternative_name;
		}

		// Add legalName if set.
		if ( ! empty( $legal_name ) ) {
			$publisher_jsonld['legalName'] = $legal_name;
		}

		// If all address fields are available, build the `address` property with its sub properties.
		if (
			! empty( $street_address )
			&& ! empty( $locality )
			&& ! empty( $region )
			&& ! empty( $country )
			&& ! empty( $postal_code )
		) {
			$publisher_jsonld['address'] = array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => $street_address,
				'addressLocality' => $locality,
				'addressCountry'  => $region,
				'addressRegion'   => $country,
				'postalCode'      => $postal_code,
			);
		}

		// Add telephone if set.
		if ( ! empty( $telephone ) ) {
			$publisher_jsonld['telephone'] = $telephone;
		}

		// Add email if set.
		if ( ! empty( $email ) ) {
			$publisher_jsonld['email'] = $email;
		}

		// Set extra fields.

		$extra_fields = $this->extra_fields_service->get_all_field_data();

		foreach ( $extra_fields as $field_slug => $field_value ) {
			$field_label                      = $this->extra_fields_service->get_field_label( $field_slug );
			$publisher_jsonld[ $field_label ] = $field_value;
		}

		/**
		 * Set the logo, only for http://schema.org/ + Organization, LocalBusiness, or OnlineBusiness
		 * as Person doesn't support the logo property.
		 *
		 * @see http://schema.org/logo.
		 */
		$organization_types = array(
			'Organization',
			'LocalBusiness',
			'OnlineBusiness',
		);

		if ( ! in_array( $publisher_jsonld['@type'], $organization_types, true ) ) {
			return;
		}

		// Get the publisher logo.
		$publisher_id   = $this->configuration_service->get_publisher_id();
		$publisher_logo = $this->get_publisher_logo( $publisher_id );

		// Bail out if the publisher logo isn't set.
		if ( false === $publisher_logo ) {
			return;
		}

		/**
		 * Copy over some useful properties.
		 *
		 * @see https://developers.google.com/search/docs/data-types/articles.
		 */
		$jsonld['logo']['@type'] = 'ImageObject';
		$jsonld['logo']['url']   = $publisher_logo['url'];

		/**
		 * If you specify a "width" or "height" value you should leave out 'px'.
		 * For example: "width":"4608px" should be "width":"4608".
		 *
		 * @see: https://github.com/insideout10/wordlift-plugin/issues/451.
		 */
		$jsonld['logo']['width']  = $publisher_logo['width'];
		$jsonld['logo']['height'] = $publisher_logo['height'];
	}

	/**
	 * Main callback for the filter hooks.
	 *
	 * Conditionally add the Organization data if we are on the `About Us` page, or if
	 * no `About Us` page is set, and we are on the home page.
	 *
	 * @param $jsonld  array JSON-LD structure.
	 * @param $post_id int   Post ID.
	 *
	 * @return mixed
	 *
	 * @since 3.53.0
	 */
	public function _add_organization_jsonld( $jsonld, $post_id ) {
		// Exit if the Publisher is not set or correctly configured.
		if ( ! $this->publisher_service->is_publisher_set() ) {
			return $jsonld;
		}

		// @@todo what if $post_id is false? i.e. Latest posts as Home Page.
		// I think it's safe. $this->is_about_page() does a comparison, and if $post_id is null then it would return false.

		$is_about_us = $this->is_about_page( $post_id );
		$is_homepage = is_home() || is_front_page();

		// Return when we are not looking at `About Us` page, or the `Home Page` when `About Us` is not set.
		if ( ! ( $is_about_us || ! $is_about_us && $is_homepage ) ) {
			return $jsonld;
		}

		$publisher_id = $this->configuration_service->get_publisher_id();

		// Add publisher to the JSON-LD if it doesn't exist in the graph.
		if ( ! $this->is_publisher_entity_in_graph( $jsonld, $publisher_id ) ) {
			// Get the Publisher data
			$references     = array();
			$reference_info = array();

			$publisher_jsonld = $this->post_jsonld_service->convert(
				$publisher_id,
				$references,
				$reference_info,
				new $this->relations()
			);

			// Add a reference to the publisher in the main Entity of the JSON-LD.
			$jsonld[0]['publisher'] = array(
				'@id' => $publisher_jsonld['@id'],
			);

			$jsonld[] = $publisher_jsonld;
		}

		// Add the Organization data to the Publisher JSON-LD.
		$publisher_uri = $this->entity_service->get_uri( $publisher_id );

		foreach ( $jsonld as &$jsonld_item ) {
			if (
				$jsonld_item
				&& array_key_exists( '@id', $jsonld_item )
				&& $jsonld_item['@id'] === $publisher_uri
			) {
				$this->expand_publisher_jsonld( $jsonld_item, $publisher_id );
			}
		}

		return $jsonld;
	}
}
