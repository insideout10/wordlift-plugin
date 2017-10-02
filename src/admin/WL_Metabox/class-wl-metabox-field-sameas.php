<?php
/**
 * Metaboxes: sameAs Field Metabox.
 *
 * @since      3.0.0
 * @package    Wordlift
 * @subpackage Wordlift/admin/WL_Metabox
 */

/**
 * Define the {@link WL_Metabox_Field_sameas} class.
 *
 * @since      3.0.0
 * @package    Wordlift
 * @subpackage Wordlift/admin/WL_Metabox
 */
class WL_Metabox_Field_sameas extends WL_Metabox_Field {

	/**
	 * @inheritdoc
	 */
	public function __construct( $args ) {
		parent::__construct( $args['sameas'] );
	}

	/**
	 * @inheritdoc
	 */
	public function save_data( $values ) {
		// The autocomplete select may send JSON arrays in input values.
		mb_regex_encoding( 'UTF-8' );
		$merged = array_reduce( (array) $values, function ( $carry, $item ) {
			return array_merge( $carry, mb_split( "\x{2063}", wp_unslash( $item ) ) );
		}, array() );

		parent::save_data( $merged );
	}

	/**
	 * @inheritdoc
	 */
	public function sanitize_data_filter( $value ) {

		// Call our sanitizer helper.
		return Wordlift_Sanitizer::sanitize_url( $value );
	}

	/**
	 * @inheritdoc
	 */
	protected function get_heading_html() {

		// Add the select html fragment after the heading.
		return parent::get_heading_html()
			   . $this->get_select_html();
	}

	/**
	 * Get the select html fragment.
	 *
	 * @since 3.15.0
	 * @return string The html fragment.
	 */
	private function get_select_html() {
		// Return an element where the new Autocomplete Select will attach to.
		return '<p>'
			   . esc_html__( 'Use the search below to link this entity with equivalent entities in the linked data cloud.', 'wordlift' )
			   . '<div id="wl-metabox-field-sameas"></div></p>';
	}

	/**
	 * @inheritdoc
	 */
	protected function get_add_button_html( $count ) {

		return
			"<label for='$this->meta_name'>"
			. esc_html__( 'If you already know the URL of the entity that you would like to link, add it in the field below.', 'wordlift' )
			. '</label>'
			. '<div class="wl-input-wrapper">'
			. "<input type='text' readonly='readonly' id='$this->meta_name' name='wl_metaboxes[$this->meta_name][]'  style='width:88%' />"
			. '<button class="button wl-remove-input wl-button" type="button">Remove</button>'
			. '</div>'
			. parent::get_add_button_html( $count );
	}

	/**
	 * @inheritdoc
	 */
	protected function get_stored_values_html( &$count ) {

		return '<p>'
			   . parent::get_stored_values_html( $count )
			   . '</p>';
	}

	/**
	 * @inheritdoc
	 */
	public function html() {

		// Open main <div> for the Field.
		$html = $this->html_wrapper_open();

		// Label.
		$html .= $this->get_heading_html();

		// print nonce.
		$html .= $this->html_nonce();

		// print data loaded from DB.
		$count = 0;

		// If cardinality allows it, print button to add new values.
		$html .= $this->get_add_button_html( $count );

		$html .= $this->get_stored_values_html( $count );

		// Close the HTML wrapper.
		$html .= $this->html_wrapper_close();

		return $html;
	}

}
