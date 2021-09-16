<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class returns the term by URI.
 */

namespace Wordlift\Term;

use Wordlift\Common\Singleton;

class Uri_Service extends Singleton {
	/**
	 * @return Uri_Service
	 */
	public static function get_instance() {
		return parent::get_instance();
	}

	/**
	 * @param $term_id
	 *
	 * @return string
	 */
	public function get_uri_by_term( $term_id ) {
		return get_term_meta( $term_id, WL_ENTITY_URL_META_NAME, true );
	}

	/**
	 * @param $uri string
	 *
	 * @return \WP_Term | bool
	 */
	public function get_term( $uri ) {

		global $wpdb;
		$query_template = <<<EOF
SELECT t.term_id FROM $wpdb->terms AS t INNER JOIN $wpdb->termmeta AS tm ON t.term_id = tm.term_id
WHERE tm.meta_key='entity_url' AND tm.meta_value = %s
EOF;
		$query          = $wpdb->prepare( $query_template, $uri );

		$term_ids = $wpdb->get_col( $query );

		if ( count( $term_ids ) === 0 ) {
			return false;
		}

		return get_term( $term_ids[0] );
	}

	public function set_entity_uri( $term_id, $uri ) {
		update_term_meta( $term_id, WL_ENTITY_URL_META_NAME, $uri );
	}

}
