<?php

namespace Wordlift\Vocabulary\Data\Term_Count;

use Wordlift\Vocabulary\Analysis_Background_Service;
use Wordlift\Vocabulary\Api\Entity_Rest_Endpoint;
use Wordlift\Vocabulary\Terms_Compat;

/**
 * This class is used for getting default term count without cache.
 *
 * @since 3.30.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Default_Term_Count implements Term_Count {

	/**
	 * Return count of terms which have entities and also not matched by editor, currently
	 * it is used for the menu icon badge.
	 *
	 * @return int
	 */
	public function get_term_count() {
		return count(
			Terms_Compat::get_terms(
				Terms_Compat::get_public_taxonomies(),
				array(
					'hide_empty' => false,
					'fields'     => 'ids',
					'meta_query' => array(
						array(
							'key'     => Entity_Rest_Endpoint::IGNORE_TAG_FROM_LISTING,
							'compare' => 'NOT EXISTS',
						),
						array(
							'key'     => Analysis_Background_Service::ENTITIES_PRESENT_FOR_TERM,
							'compare' => 'EXISTS',
						),
					),
				)
			)
		);

	}

}
