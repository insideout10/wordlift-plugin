<?php

namespace Wordlift\Vocabulary\Data\Term_Count;

use Wordlift\Vocabulary\Analysis_Background_Service;
use Wordlift\Vocabulary\Api\Entity_Rest_Endpoint;

/**
 * This class is used for getting default term count without cache.
 * @since 3.30.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Default_Term_Count implements Term_Count {

	/**
	 * Return count of terms which have entities and also not matched by editor, currently
	 * it is used for the menu icon badge.
	 * @return int
	 */
	public function get_term_count() {
		/**
		 * @todo: add support for all terms, currently we add only
		 * post_tag.
		 */
		return count( $this->get_terms_compat( 'post_tag', array(
			'taxonomy'   => 'post_tag',
			'hide_empty' => false,
			'fields'     => 'ids',
			'meta_query' => array(
				array(
					'key'     => Entity_Rest_Endpoint::IGNORE_TAG_FROM_LISTING,
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => Analysis_Background_Service::ENTITIES_PRESENT_FOR_TERM,
					'compare' => 'EXISTS'
				)
			),
		) ) );

	}

	private function get_terms_compat( $taxonomy, $args_with_taxonomy_key ) {
		global $wp_version;

		if ( version_compare( $wp_version, '4.5', '<' ) ) {
			return get_terms( $taxonomy, $args_with_taxonomy_key );
		} else {
			return get_terms( $args_with_taxonomy_key );
		}
	}

}
