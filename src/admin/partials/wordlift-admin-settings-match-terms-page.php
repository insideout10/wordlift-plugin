<?php

use Wordlift\Scripts\Scripts_Helper;

Scripts_Helper::enqueue_based_on_wordpress_version(
	'wl-vocabulary-match-terms-settings',
	plugin_dir_url( dirname( __DIR__ ) ) . 'js/dist/vocabulary-settings-page',
	array( 'react', 'react-dom', 'wp-polyfill' )
);

wp_enqueue_style(
	'wl-vocabulary-match-terms-settings',
	plugin_dir_url( dirname( __DIR__ ) ) . 'js/dist/vocabulary-settings-page.full.css',
	array(),
	WORDLIFT_VERSION
);
echo "<br/><div id='wl_vocabulary_analysis_progress_bar'></div>";
