<?php
/**
 * @since 3.31.7
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
use Wordlift\Features\Features_Registry;

abstract class Wordlift_Vocabulary_Terms_Unit_Test_Case extends Wordlift_Unit_Test_Case {


	const NO_VOCABULARY_TERM_TAXONOMY = 'no_vocabulary_terms';


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
}