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

		// Only use mb_* functions when mbstring is available.
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/693.
		if ( extension_loaded( 'mbstring' ) ) {
			mb_regex_encoding( 'UTF-8' );

			$merged = array_reduce( (array) $values, function ( $carry, $item ) {
				return array_merge( $carry, mb_split( "\x{2063}", wp_unslash( $item ) ) );
			}, array() );
		} else {
			$merged = array_reduce( (array) $values, function ( $carry, $item ) {
				return array_merge( $carry, preg_split( "/\x{2063}/u", wp_unslash( $item ) ) );
			}, array() );
		}

		// Convert all escaped special characters to their original.
		$merged = array_map( 'urldecode', $merged );

		$merged = $this->filter_urls( $merged );

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
	 * @return string The html fragment.
	 * @since 3.15.0
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

		$placeholder = esc_attr_x( 'Type here the URL of an equivalent entity from another dataset.', 'sameAs metabox input', 'wordlift' );

		return
  		'<button type="button" class="wl-add-input wl-add-input--link">'.esc_html__( 'Click here to manually add URLs', 'wordlift' ).'</button>'
        .'<div style="display: none;"><div class="wl-input-wrapper">'
        ."<input type='text' id='$this->meta_name' name='wl_metaboxes[$this->meta_name][]' placeholder='$placeholder' />"
        .'<button type="button" class="wl-remove-input wl-remove-input--sameas"></button>'
        .'</div></div>'
        . '<fieldset id="wl-input-container">'.$this->get_stored_values_html( $count ).'</fieldset>'
        .parent::get_add_custom_button_html( $count, 'Add Another URL', 'hide' );
	}

	/**
	 * @inheritdoc
	 */
	protected function get_stored_values_html( &$count ) {

		return  parent::get_stored_values_html( $count );
	}

	/**
	 * @inheritdoc
	 */
	public function html() {

		/**
		 * Filter: wl_feature__enable__metabox__sameas.
		 *
		 * @param bool whether the sameAs metabox should be shown, defaults to true.
		 *
		 * @return bool
		 * @since 3.29.1
		 */
		if ( apply_filters( 'wl_feature__enable__metabox__sameas', true ) ) {

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

			// Close the HTML wrapper.
			$html .= $this->html_wrapper_close();

			return $html;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function html_input( $value ) {
		@ob_start();
		?>
        <div class="wl-input-wrapper wl-input-wrapper-readonly">
            <input
                    type="text"
                    readonly="readonly"
                    id="<?php echo $this->meta_name ?>"
                    name="wl_metaboxes[<?php echo $this->meta_name ?>][]"
                    value="<?php echo $value ?>"
            />

            <button class="wl-remove-input wl-remove-input--sameas"></button>
        </div>
		<?php

		$html = ob_get_clean();

		return $html;
	}

	/**
	 * @param array $urls
	 *
	 * @return array
	 */
	private function filter_urls( $urls ) {
		$configuration_service = Wordlift_Configuration_Service::get_instance();
		$dataset_uri           = $configuration_service->get_dataset_uri();

		return array_filter( $urls, function ( $url ) use ( $dataset_uri ) {
			$url_validation = filter_var( $url, FILTER_VALIDATE_URL );
			if ( null === $dataset_uri ) {
				return $url_validation;
			}

			// URLs should not start with local dataset uri.
			return $url_validation && ( strpos( $url, $dataset_uri, 0 ) === false );
		} );
	}

}
