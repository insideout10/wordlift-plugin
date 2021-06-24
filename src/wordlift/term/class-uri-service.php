<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class returns the term by URI.
 */

namespace Wordlift\Term;

use Wordlift\Common\Singleton;
use Wordlift\Vocabulary\Terms_Compat;

class Uri_Service extends Singleton {
	/**
	 * @return Uri_Service
	 */
	public static function get_instance() {
		return parent::get_instance();
	}

	/**
	 * @param $uri string
	 *
	 * @return \WP_Term | bool
	 */
	public function get_term( $uri ) {

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
