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

use WP_Query;

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
	 * @param int    $pagination_offset Pagination offset.
	 * @param string $title_contains Case-insensitive filter for page titles.
	 *
	 * @return array Array of page IDs and titles.
	 *
	 * @since 3.53.0
	 */
	public function get( $pagination_offset, $title_contains ) {
		// Sanitize input
		$pagination_offset = max( 1, filter_var( $pagination_offset, FILTER_VALIDATE_INT ) );
		$title_contains    = sanitize_text_field( $title_contains );

		// Get a number of pages starting at a given offset.
		$pagination_no_of_pages = self::PAGINATION_NUM_OF_PAGES;

		$args = array(
			'post_type'      => 'page',                   // Search `Page` posts
			'posts_per_page' => $pagination_no_of_pages,  // Number of posts per page
			'paged'          => $pagination_offset,       // Pagination page
			'orderby'        => 'title',                  // Sort by title
			'order'          => 'ASC',                    // Ascending
		);

		// If a title_contains filter was provided, search for pages accordingly
		if ( ! empty( $title_contains ) ) {
			$args['s']              = $title_contains;
			$args['search_columns'] = 'post_title';
		}

		$query = new WP_Query( $args );

		$data = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$data[] = array(
					'id'    => (string) get_the_ID(),
					'title' => get_the_title(),
				);
			}

			// Reset the post data
			wp_reset_postdata();
		}

		return $data;
	}
}
