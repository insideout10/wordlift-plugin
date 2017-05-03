<?php

/**
 * All custom WL_Metabox_Filed(s) must extend this class.
 * This class deals with saving the most basic data type, strings.
 * Use the methods that are useful or overwrite them if you need custom behaviour.
 */
class WL_Metabox_Field {

	public $meta_name;
	public $raw_custom_field;
	public $predicate;
	public $label;
	public $expected_wl_type;
	public $expected_uri_type;
	public $cardinality;
	public $data;

	/**
	 * @param array $args
	 */
	public function __construct( $args ) {

		if ( empty( $args ) ) {
			return;
		}

		// Save a copy of the custom field's params.
		$this->raw_custom_field = reset( $args );

		// Extract meta name (post_meta key for the DB).
		$this->meta_name = key( $args );

		// Extract linked data predicate.
		if ( isset( $this->raw_custom_field['predicate'] ) ) {
			$this->predicate = $this->raw_custom_field['predicate'];
		} else {
			return;
		}

		// Extract human readable label.
		$exploded_predicate = explode( '/', $this->predicate );

		// Use the label defined for the property if set, otherwise the last part of the schema.org/xyz predicate.
		$this->label = isset( $this->raw_custom_field['metabox']['label'] ) ? $this->raw_custom_field['metabox']['label'] : end( $exploded_predicate );

		// Extract field constraints (numerosity, expected type).
		// Default constaints: accept one string..
		if ( isset( $this->raw_custom_field['type'] ) ) {
			$this->expected_wl_type = $this->raw_custom_field['type'];
		} else {
			$this->expected_wl_type = Wordlift_Schema_Service::DATA_TYPE_STRING;
		}

		$this->cardinality = 1;
		if ( isset( $this->raw_custom_field['constraints'] ) ) {

			$constraints = $this->raw_custom_field['constraints'];

			// Extract cardinality.
			if ( isset( $constraints['cardinality'] ) ) {
				$this->cardinality = $constraints['cardinality'];
			}

			// Which type of entity can we accept (e.g. Place, Event, ecc.)? .
			if ( $this->expected_wl_type === Wordlift_Schema_Service::DATA_TYPE_URI && isset( $constraints['uri_type'] ) ) {
				$this->expected_uri_type = is_array( $constraints['uri_type'] ) ? $constraints['uri_type'] : array( $constraints['uri_type'] );
			}

		}
	}

	/**
	 * Return nonce HTML.
	 * Overwrite this method in a child class to obtain custom behaviour.
	 */
	public function html_nonce() {

		return wp_nonce_field( 'wordlift_' . $this->meta_name . '_entity_box', 'wordlift_' . $this->meta_name . '_entity_box_nonce', TRUE, FALSE );
	}

	/**
	 * Verify nonce.
	 * Overwrite this method in a child class to obtain custom behaviour.
	 *
	 * @return boolean Nonce verification
	 */
	public function verify_nonce() {

		$nonce_name   = 'wordlift_' . $this->meta_name . '_entity_box_nonce';
		$nonce_verify = 'wordlift_' . $this->meta_name . '_entity_box';

		if ( ! isset( $_POST[ $nonce_name ] ) ) {
			return FALSE;
		}

		// Verify that the nonce is valid.
		return wp_verify_nonce( $_POST[ $nonce_name ], $nonce_verify );
	}

	/**
	 * Load data from DB and store the resulting array in $this->data.
	 * Overwrite this method in a child class to obtain custom behaviour.
	 */
	public function get_data() {

		$data = get_post_meta( get_the_ID(), $this->meta_name );

		// Values are always contained in an array (makes it easier to manage cardinality).
		if ( ! is_array( $data ) ) {
			$data = array( $data );
		}

		$this->data = $data;
	}

	/**
	 * Sanitizes data before saving to DB. Default sanitization trashes empty values.
	 * Stores the sanitized values into $this->data so they can be later processed.
	 * Overwrite this method in a child class to obtain custom behaviour.
	 *
	 * @param array $values Array of values to be sanitized and then stored into $this->data
	 *
	 */
	public function sanitize_data( $values ) {

		$sanitized_data = array();

		if ( ! is_array( $values ) ) {
			$values = array( $values );
		}

		foreach ( $values as $value ) {
			$sanitized_value = $this->sanitize_data_filter( $value );
			if ( ! is_null( $sanitized_value ) ) {
				$sanitized_data[] = $sanitized_value;
			}
		}

		$this->data = $sanitized_data;
	}

	/**
	 * Sanitize a single value. Called from $this->sanitize_data. Default sanitization excludes empty values.
	 * Overwrite this method in a child class to obtain custom behaviour.
	 *
	 * @return mixed Returns sanitized value, or null.
	 */
	public function sanitize_data_filter( $value ) {

		// TODO: all fields should provide their own sanitize which shouldn't be part of a UI class.
		// If the field provides its own validation, use it.
		if ( isset( $this->raw_custom_field['sanitize'] ) ) {
			return call_user_func( $this->raw_custom_field['sanitize'], $value );
		}

		if ( ! is_null( $value ) && $value !== '' ) {         // do not use 'empty()' -> https://www.virendrachandak.com/techtalk/php-isset-vs-empty-vs-is_null/ .
			return $value;
		}

		return NULL;
	}

	/**
	 * Save data to DB.
	 * Overwrite this method in a child class to obtain custom behaviour.
	 */
	public function save_data( $values ) {

		// Will sanitize data and store them in $field->data.
		$this->sanitize_data( $values );

		$entity_id = get_the_ID();

		// Take away old values.
		delete_post_meta( $entity_id, $this->meta_name );

		// insert new values, respecting cardinality.
		$single = ( $this->cardinality == 1 );
		foreach ( $this->data as $value ) {
			add_post_meta( $entity_id, $this->meta_name, $value, $single );
		}
	}

	/**
	 * Returns the HTML tag that will contain the Field. By default the we return a <div> with data- attributes on cardinality and expected types.
	 * It is useful to provide data- attributes for the JS scripts.
	 * Overwrite this method in a child class to obtain custom behaviour.
	 */
	public function html_wrapper_open() {

		return "<div class='wl-field' data-cardinality='$this->cardinality'>";
	}

	/**
	 * Returns Field HTML (nonce included).
	 * Overwrite this method (or methods called from this method) in a child class to obtain custom behaviour.
	 */
	public function html() {

		// Open main <div> for the Field.
		$html = $this->html_wrapper_open();

		// Label.
		$html .= "<h3>$this->label</h3>";

		// print nonce.
		$html .= $this->html_nonce();

		// print data loaded from DB.
		$count = 0;
		if ( $this->data ) {
			foreach ( $this->data as $value ) {
				if ( $count < $this->cardinality ) {
					$html .= $this->html_input( $value );
				}
				$count ++;
			}
		}

		// Print the empty <input> to add new values.
		if ( $count === 0 ) { // } || $count < $this->cardinality ) { DO NOT print empty inputs unless requested by the editor since fields might support empty strings.
			$html .= $this->html_input( '' );    // Will print an empty <input>
			$count ++;
		}

		// If cardiality allows it, print button to add new values.
		if ( $count < $this->cardinality ) {
			$html .= '<button class="button wl-add-input wl-button" type="button">Add</button>';
		}

		// Close the HTML wrapper.
		$html .= $this->html_wrapper_close();

		return $html;
	}

	/**
	 * Return a single <input> tag for the Field.
	 *
	 * @param mixed $value Input value
	 *
	 * @return string
	 */
	public function html_input( $value ) {
		$html = <<<EOF
			<div class="wl-input-wrapper">
				<input type="text" id="$this->meta_name" name="wl_metaboxes[$this->meta_name][]" value="$value" style="width:88%" />
				<button class="button wl-remove-input wl-button" type="button" style="width:10 % ">Remove</button>
			</div>
EOF;

		return $html;
	}

	/**
	 * Returns closing for the wrapper HTML tag.
	 */
	public function html_wrapper_close() {

		return '</div><hr>';
	}

}
