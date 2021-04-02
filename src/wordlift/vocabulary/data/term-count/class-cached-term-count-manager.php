<?php

namespace Wordlift\Vocabulary\Data\Term_Count;
/**
 * This class removes all the cache for the term count if the analysis is done for new tags
 * @since 3.30.0
 */
class Cached_Term_count_Manager {

	public function connect_hook() {

		add_action( 'wordlift_vocabulary_analysis_complete_for_terms_batch', function () {
			delete_transient( Cached_Term_Count::TRANSIENT_KEY );
		} );

		add_action('created_post_tag', function () {
			delete_transient( Cached_Term_Count::TRANSIENT_KEY );
		});

	}

}