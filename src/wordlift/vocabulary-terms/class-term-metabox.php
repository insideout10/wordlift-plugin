<?php
/**
 * @since 3.31.7
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
namespace Wordlift\Vocabulary_Terms;

use Wordlift\Metabox\Wl_Abstract_Metabox;
use Wordlift\Vocabulary\Terms_Compat;

class Term_Metabox extends Wl_Abstract_Metabox {

	public function __construct() {
		parent::__construct();
		$taxonomies = Terms_Compat::get_public_taxonomies();
		foreach ( $taxonomies as $taxonomy ) {
			add_action( "${taxonomy}_edit_form", array( $this, 'render_ui' ), 1 );
			add_action( "edited_${taxonomy}", array( $this, 'save_field' ) );
		}
	}

	/**
	 * @param $term \WP_Term
	 */
	public function render_ui( $term ) {
		$this->instantiate_fields( $term->term_id, Wl_Abstract_Metabox::TERM );
		$this->html();
		$this->enqueue_scripts_and_styles();
		$plugin = \Wordlift::get_instance();

		// Enqueue this scripts for sameas fields.
		wp_enqueue_script(
			'wl-autocomplete-select',
			plugin_dir_url( dirname( dirname( __FILE__ ) ) ) . 'js/dist/autocomplete-select.js',
			array(),
			$plugin->get_version(),
			true
		);

		wp_enqueue_style(
			'wl-autocomplete-select',
			plugin_dir_url( dirname( dirname( __FILE__ ) ) ) . 'js/dist/autocomplete-select.css',
			array(),
			$plugin->get_version()
		);
	}

	public function save_field( $term_id ) {
		$this->save_form_data( $term_id, Wl_Abstract_Metabox::TERM );
	}

}