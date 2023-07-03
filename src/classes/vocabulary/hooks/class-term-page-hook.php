<?php

namespace Wordlift\Vocabulary\Hooks;

use Wordlift\Scripts\Scripts_Helper;
use Wordlift\Vocabulary\Api\Api_Config;
use Wordlift\Vocabulary\Data\Entity_List\Entity_List_Utils;
use Wordlift\Vocabulary\Data\Term_Data\Term_Data_Factory;
use Wordlift\Vocabulary\Terms_Compat;

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
		$taxonomies = Terms_Compat::get_public_taxonomies();
		foreach ( $taxonomies as $taxonomy ) {
			add_action( "${taxonomy}_edit_form_fields", array( $this, 'load_scripts' ), 2, PHP_INT_MAX );
		}
	}

	/**
	 * @param $term \WP_Term
	 */
	public function load_scripts( $term ) {

		$term_data = $this->term_data_factory->get_term_data( $term );

		Scripts_Helper::enqueue_based_on_wordpress_version(
			self::HANDLE,
			plugin_dir_url( dirname( dirname( __DIR__ ) ) ) . 'js/dist/vocabulary-term-page',
			array( 'react', 'react-dom', 'wp-polyfill', 'wp-i18n' ),
			true
		);

		wp_enqueue_style( self::HANDLE, plugin_dir_url( dirname( dirname( __DIR__ ) ) ) . 'js/dist/vocabulary-term-page.full.css', array(), WORDLIFT_VERSION );

		$term_data_arr = $term_data->get_data();

		$term_data_arr['entities'] = Entity_List_Utils::mark_is_active_for_entities( $term->term_id, $term_data_arr['entities'] );

		wp_localize_script(
			self::HANDLE,
			self::LOCALIZED_KEY,
			array(
				'termData'  => $term_data_arr,
				'apiConfig' => Api_Config::get_api_config(),
				'restUrl'   => get_rest_url( null, Api_Config::REST_NAMESPACE . '/search-entity/' ),
				'nonce'     => wp_create_nonce( 'wp_rest' ),
			)
		);

		echo "<tr class=\"form-field\">
				<th>Match</th>
				<td style='width: 100%;' id='wl_vocabulary_terms_widget'></td>
			</tr>";

		echo "<tr class=\"form-field\">
		     <th></th>
		     <td id='wl_vocabulary_terms_autocomplete_select'></td></tr>";

	}

}
