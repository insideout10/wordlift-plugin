<?php

namespace Wordlift\Vocabulary\Pages;

use Wordlift\Scripts\Scripts_Helper;
use Wordlift\Vocabulary\Api\Api_Config;
use Wordlift\Vocabulary\Data\Term_Count\Term_Count;
use Wordlift\Vocabulary\Menu\Badge\Badge_Generator;

/**
 * @since 1.0.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Match_Terms {
	/**
	 * @var Term_Count
	 */
	private $term_count;

	/**
	 * Match_Terms constructor.
	 *
	 * @param $term_count Term_Count
	 */
	public function __construct( $term_count ) {

		$this->term_count = $term_count;
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

	}

	public function admin_menu() {
		$number = $this->term_count->get_term_count();
		add_submenu_page(
			'wl_admin_menu',
			__( 'Match Terms', 'wordlift' ),
			__( 'Match Terms', 'wordlift' ) . ' ' . Badge_Generator::generate_html( $number ),
			'manage_options',
			'wl-vocabulary-match-terms',
			array( $this, 'submenu_page_callback' )
		);
		remove_submenu_page( 'wl_admin_menu', 'wl_admin_menu' );
	}

	public function submenu_page_callback() {

		Scripts_Helper::enqueue_based_on_wordpress_version(
			'wl-vocabulary-reconcile-script',
			plugin_dir_url( dirname( dirname( __DIR__ ) ) ) . 'js/dist/vocabulary',
			array( 'react', 'react-dom', 'wp-i18n', 'wp-polyfill' ),
			true
		);

		wp_enqueue_style(
			'wl-vocabulary-reconcile-script',
			plugin_dir_url( dirname( dirname( __DIR__ ) ) ) . 'js/dist/vocabulary.full.css',
			array(),
			WORDLIFT_VERSION
		);
		wp_localize_script( 'wl-vocabulary-reconcile-script', '_wlVocabularyMatchTermsConfig', Api_Config::get_api_config() );
		echo "<div id='wl_cmkg_reconcile_progress' class='wrap'></div>";
		echo "<div id='wl_cmkg_table' class='wrap'></div>";
	}

}
