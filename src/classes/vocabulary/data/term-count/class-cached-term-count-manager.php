<?php

namespace Wordlift\Vocabulary\Data\Term_Count;

use Wordlift\Vocabulary\Terms_Compat;

/**
 * This class removes all the cache for the term count if the analysis is done for new tags
 *
 * @since 3.30.0
 */
class Cached_Term_Count_Manager {

	public function connect_hook() {

		add_action(
			'wordlift_vocabulary_analysis_complete_for_terms_batch',
			function () {
				delete_transient( Cached_Term_Count::TRANSIENT_KEY );
			}
		);

		$taxonomies = Terms_Compat::get_public_taxonomies();
		foreach ( $taxonomies as $taxonomy ) {
			add_action(
				"created_${taxonomy}",
				function () {
					delete_transient( Cached_Term_Count::TRANSIENT_KEY );
				}
			);
		}

	}

}
