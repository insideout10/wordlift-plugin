<?php
namespace Wordlift\Vocabulary_Terms;

use Wordlift\Vocabulary\Terms_Compat;

class Term_Meta_Box extends \WL_Abstract_Meta_Box {

	public function __construct() {
		parent::__construct();
		$that = $this;
		$taxonomies = Terms_Compat::get_public_taxonomies();
		foreach ( $taxonomies as $taxonomy ) {
			add_action( "${taxonomy}_edit_form", array( $this, 'render_ui' ), 1 );
			add_action( "created_${taxonomy}", array( $this, 'save_field' ) );
			add_action( "edited_${taxonomy}", array( $this, 'save_field' ) );
		}
	}

	/**
	 * @param $term \WP_Term
	 */
	public function render_ui( $term ) {
		$this->instantiate_fields( $term->term_id );
		$this->html();
		$this->enqueue_scripts_and_styles();
	}

}