<?php

namespace Wordlift\Vocabulary\Hooks;

use Wordlift\Scripts\Scripts_Helper;
use Wordlift\Vocabulary\Analysis_Background_Service;
use Wordlift\Vocabulary\Api\Api_Config;
use Wordlift\Vocabulary\Api\Entity_Rest_Endpoint;
use Wordlift\Vocabulary\Data\Term_Data\Term_Data_Factory;
use Wordlift\Vocabulary\Pages\Match_Terms;

/**
 * This class is used to show the entity match component on the
 * term page.
 */
class Term_Page_Hook {

	const HANDLE = 'wl-vocabulary-term-page-handle';

	const LOCALIZED_KEY = '_wlVocabularyTermPageSettings';

	private $term_data_factory;

	/**
	 * Term_Page_Hook constructor.
	 *
	 * @param $term_data_factory Term_Data_Factory
	 */
	public function __construct( $term_data_factory ) {
		$this->term_data_factory = $term_data_factory;
	}

	public function connect_hook() {

		add_action( 'post_tag_edit_form_fields', array( $this, 'load_scripts' ), PHP_INT_MAX );

	}

	/**
	 * @param $term \WP_Term
	 */
	public function load_scripts( $term ) {

		$term_data = $this->term_data_factory->get_term_data( $term );

		Scripts_Helper::enqueue_based_on_wordpress_version(
			self::HANDLE,
			plugin_dir_url( dirname( dirname( __DIR__ ) ) ) . 'js/dist/vocabulary-term-page',
			array( 'react', 'react-dom', 'wp-polyfill' ),
			true
		);

		wp_enqueue_style( self::HANDLE, plugin_dir_url( dirname( dirname( __DIR__ ) ) ) . 'js/dist/vocabulary-term-page.full.css' );

		wp_localize_script( self::HANDLE, self::LOCALIZED_KEY, array(
			'termData'  => $term_data->get_data(),
			'apiConfig' => Api_Config::get_api_config()
		) );

		echo "<tr class=\"form-field\">
				<th>Match</th>
				<td style='width: 100%;'><div id='wl_vocabulary_terms_widget'></div></td>
			</tr>";
	}

}