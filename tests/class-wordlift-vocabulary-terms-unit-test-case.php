<?php
/**
 * @since 3.31.7
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
use Wordlift\Features\Features_Registry;
use Wordlift\Term\Uri_Service;

abstract class Wordlift_Vocabulary_Terms_Unit_Test_Case extends Wordlift_Unit_Test_Case {

	public function setUp() {
		parent::setUp();

		// taxonomy to be used on the no_vocabulary_terms test.
		if ( ! taxonomy_exists( 'no_vocabulary_terms' ) ) {
			register_taxonomy( 'no_vocabulary_terms', 'post' );
		}

		// Reset all global filters.
		global $wp_filter, $wp_scripts, $wp_styles;
		$wp_filter  = array();
		$wp_scripts = null;
		$wp_styles  = null;
		add_filter( 'wl_feature__enable__no-vocabulary-terms', '__return_true' );
		// vocabulary terms feature should now be enabled.
		run_wordlift();
		$features_registry = Features_Registry::get_instance();
		$features_registry->initialize_all_features();

	}

	const NO_VOCABULARY_TERM_TAXONOMY = 'no_vocabulary_terms';

	/**
	 * @return int|WP_Error
	 */
	protected function create_post_with_term_reference( $term_name) {

		$term_data        = wp_insert_term($term_name,  self::NO_VOCABULARY_TERM_TAXONOMY );
		$term             = get_term( $term_data['term_id'] );
		$term_uri_service = Uri_Service::get_instance();
		$term_uri         = $term_uri_service->get_uri_by_term( $term->term_id );
		$post_content     = <<<EOF
		<span itemid="$term_uri">test</span>
EOF;

		$post_id = wp_insert_post( array(
			'post_content' => $post_content
		) );

		wl_linked_data_save_post_and_related_entities( $post_id );

		return $post_id;
	}



}