<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Vocabulary_Terms;

use Wordlift\Metabox\Wl_Abstract_Metabox;
use Wordlift\Object_Type_Enum;
use Wordlift\Vocabulary\Terms_Compat;

class Term_Metabox extends Wl_Abstract_Metabox {

	public function __construct() {
		parent::__construct();
		if ( ! apply_filters( 'wl_feature__enable__pods-integration', false ) ) { //phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
			add_action( 'init', array( $this, 'init_all_custom_fields' ) );
		}

	}

	/**
	 * @param $term \WP_Term
	 */
	public function render_ui( $term ) {

		$this->instantiate_fields( $term->term_id, Object_Type_Enum::TERM );
		$this->html();
		$this->enqueue_scripts_and_styles();
		$plugin = \Wordlift::get_instance();

		// Enqueue this scripts for sameas fields.
		wp_enqueue_script(
			'wl-autocomplete-select',
			plugin_dir_url( dirname( __DIR__ ) ) . 'js/dist/autocomplete-select.js',
			array(),
			$plugin->get_version(),
			true
		);

		wp_enqueue_style(
			'wl-autocomplete-select',
			plugin_dir_url( dirname( __DIR__ ) ) . 'js/dist/autocomplete-select.css',
			array(),
			$plugin->get_version()
		);
	}

	public function save_field( $term_id ) {
		$this->save_form_data( $term_id, Object_Type_Enum::TERM );
	}

	public function init_all_custom_fields() {
		$taxonomies = Terms_Compat::get_public_taxonomies();
		foreach ( $taxonomies as $taxonomy ) {
			add_action( "{$taxonomy}_edit_form", array( $this, 'render_ui' ), 1 );
			add_action( "edited_{$taxonomy}", array( $this, 'save_field' ) );
		}
	}

}
