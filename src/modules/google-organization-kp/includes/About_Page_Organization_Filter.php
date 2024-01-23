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
	 * @var \Wordlift_Publisher_Service
	 */
	private $publisher_service;

	/**
	 * @var \Wordlift_Configuration_Service
	 */
	private $configuration_service;

	/**
	 * @var \Wordlift_Entity_Service
	 */
	private $entity_service;

	/**
	 * @var \Wordlift_Post_To_Jsonld_Converter
	 */
	private $post_jsonld_service;

	/**
	 * @param \Wordlift_Publisher_Service        $publisher_service
	 * @param \Wordlift_Configuration_Service    $configuration_service
	 * @param \Wordlift_Entity_Service           $entity_service
	 * @param \Wordlift_Post_To_Jsonld_Converter $post_jsonld_service
	 */
	public function __construct(
		$publisher_service,
		$configuration_service,
		$entity_service,
		$post_jsonld_service
	) {
		$this->publisher_service     = $publisher_service;
		$this->configuration_service = $configuration_service;
		$this->entity_service        = $entity_service;
		$this->post_jsonld_service   = $post_jsonld_service;
	}

	/**
	 * Initialize hooks.
	 *
	 * `wl_after_get_jsonld` is the main  filter hook and gets called on almost all JSON-LD conversions.
	 * However, on `WebSite` conversion this hook gets bypassed, so we also hook into `wl_website_jsonld`.
	 *
	 * @since 3.53.0
	 */
	public function register_hooks() {
		add_filter( 'wl_website_jsonld', array( $this, 'add_organization_jsonld' ), 10, 2 );
		add_filter( 'wl_after_get_jsonld', array( $this, 'add_organization_jsonld' ), 10, 2 );
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
	public function add_organization_jsonld( $jsonld, $post_id ) {
		$publisher_id = $this->configuration_service->get_publisher_id();

		// Exit if the Publisher is not set or correctly configured.
		if ( ! isset( $publisher_id ) || ! is_numeric( $publisher_id ) ) {
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

		// Add publisher to the JSON-LD if it doesn't exist in the graph.
		if ( ! $this->is_publisher_entity_in_graph( $jsonld, $publisher_id ) ) {
			$publisher_jsonld = $this->post_jsonld_service->convert( $publisher_id );

			$jsonld[] = $publisher_jsonld;
		}

		return $jsonld;
	}
}
