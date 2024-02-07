<?php

/**
 * Module:  Google Organization Knowledge Panel
 * Class:   Page_Service
 *
 * @package Wordlift/modules/google-organization-kp
 *
 * @since 3.53.0
 */

namespace Wordlift\Modules\Google_Organization_Kp;

class Page_Service {
	/**
	 * The number of pages that should be returned in the paginated /page GET request.
	 *
	 * @var int
	 */
	const PAGINATION_NUM_OF_PAGES = 100;

	/**
	 * Get a list of pages.
	 *
	 * @param int    $pagination_offset        Pagination offset.
	 * @param string $title_starts_with Case-insensitive filter for page titles.
	 *
	 * @return array Array of page IDs and titles.
	 *
	 * @since 3.53.0
	 */
	public function get( $pagination_offset, $title_starts_with ) {
		// Get a number of pages starting at a given offset.
		$pagination_no_of_pages = self::PAGINATION_NUM_OF_PAGES;

		// Get the pages
		$pages = get_pages();

		// Arrange the data.
		$data = array();
		foreach ( $pages as $page ) {
			$data[] = array(
				'id'    => (string) $page->ID,
				'title' => $page->post_title,
			);
		}

		// Filter the array to only contain items whose title starts with the `title_starts_with` param.
		if ( count( $data ) > 0 && isset( $title_starts_with ) ) {
			$filter_str = strtolower( $title_starts_with );
			$data       = array_filter(
				$data,
				function ( $page ) use ( $filter_str ) {
					// Check that the start of the string matches the filter string.
					return substr( strtolower( $page['title'] ), 0, strlen( $filter_str ) ) === $filter_str;
				}
			);
		}

		return array_slice( $data, $pagination_offset * $pagination_no_of_pages, $pagination_no_of_pages );
	}
}
