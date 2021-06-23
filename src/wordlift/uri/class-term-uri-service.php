<?php
/**
 * @since 3.31.7
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class returns the term by URI.
 */

namespace Wordlift\Uri;

use Wordlift\Vocabulary\Terms_Compat;

class Term_Uri_Service {

	/**
	 * @param $uri string
	 *
	 * @return \WP_Term | bool
	 */
	public static function get_term( $uri ) {

		$selected_terms = Terms_Compat::get_terms( '', array(
			'fields'     => 'all',
			'get'        => 'all',
			'number'     => 1,
			'meta_query' => array(
				array(
					'key'   => 'entity_url',
					'value' => $uri,
				),
			),
			'orderby'    => 'term_id',
			'order'      => 'ASC',
		) );

		if ( count( $selected_terms ) === 0 ) {
			return false;
		}

		return $selected_terms[0];
	}

}
