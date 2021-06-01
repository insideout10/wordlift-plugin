<?php
/**
 * Metaboxes: Field Metabox.
 *
 * @since      3.0.0
 * @package    Wordlift
 * @subpackage Wordlift/admin/WL_Metabox
 */

/**
 * All custom WL_Metabox_Field(s) must extend this class.
 *
 * This class deals with saving the most basic data type, strings. Use the
 * methods that are useful or overwrite them if you need custom behaviour.
 *
 * @since      3.0.0
 * @package    Wordlift
 * @subpackage Wordlift/admin/WL_Metabox
 */
class WL_Metabox_Field {

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.15.0
	 * @access protected
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	protected $log;

	/**
	 * The meta name for this field's value.
	 *
	 * @var string $meta_name The meta name for this field's value.
	 */
	public $meta_name;

	/**
	 * The custom field settings.
	 *
	 * @var null|array $raw_custom_field The custom field settings.
	 */
	public $raw_custom_field;

	/**
	 * The schema.org predicate.
	 *
	 * @var string $predicate The schema.org predicate.
	 */
	public $predicate;

	/**
	 * The field's label.
	 *
	 * @var string $label The field's label.
	 */
	public $label;

	/**
	 * The WordLift data type.
	 *
	 * @var string $expected_wl_type The WordLift data type.
	 */
	public $expected_wl_type;

	/**
	 * The RDF data type.
	 *
	 * @var string $expected_uri_type The RDF data type.
	 */
	public $expected_uri_type;

	/**
	 * The cardinality.
	 *
	 * @var int $cardinality The cardinality.
	 */
	public $cardinality;

	/**
	 * The current value.
	 *
	 * @var array $data The current value.
	 */
	public $data;

	/**
	 * The current {@link WP_Post} id.
	 *
	 * @since 3.15.3
	 *
	 * @var int The current {@link WP_Post} id.
	 */
	private $post_id;

	/**
	 * Create a {@link WL_Metabox_Field} instance.
	 *
	 * @param array $args An array of parameters.
	 */
	public function __construct( $args ) {

		$this->log = Wordlift_Log_Service::get_logger( 'WL_Metabox_Field' );

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
			if ( Wordlift_Schema_Service::DATA_TYPE_URI === $this->expected_wl_type && isset( $constraints['uri_type'] ) ) {
				$this->expected_uri_type = is_array( $constraints['uri_type'] )
					? $constraints['uri_type']
					: array( $constraints['uri_type'] );
			}
		}

		// Save early the post id to avoid other plugins messing up with it.
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/665.
		$this->post_id = get_the_ID();

	}

	/**
	 * Return nonce HTML.
	 *
	 * Overwrite this method in a child class to obtain custom behaviour.
	 */
	public function html_nonce() {

		return wp_nonce_field( 'wordlift_' . $this->meta_name . '_entity_box', 'wordlift_' . $this->meta_name . '_entity_box_nonce', true, false );
	}

	/**
	 * Verify nonce.
	 *
	 * Overwrite this method in a child class to obtain custom behaviour.
	 *
	 * @return bool Nonce verification.
	 */
	public function verify_nonce() {

		$nonce_name   = 'wordlift_' . $this->meta_name . '_entity_box_nonce';
		$nonce_verify = 'wordlift_' . $this->meta_name . '_entity_box';

		if ( ! isset( $_POST[ $nonce_name ] ) ) {
			return false;
		}

		// Verify that the nonce is valid.
		return wp_verify_nonce( $_POST[ $nonce_name ], $nonce_verify );
	}

	/**
	 * Load data from DB and store the resulting array in $this->data.
	 *
	 * Overwrite this method in a child class to obtain custom behaviour.
	 */
	public function get_data() {

		// Get the post id and load the data.
		$post_id    = $this->post_id;
		$this->data = get_post_meta( $post_id, $this->meta_name );

	}

	/**
	 * Sanitizes data before saving to DB. Default sanitization trashes empty
	 * values.
	 *
	 * Stores the sanitized values into $this->data so they can be later processed.
	 * Overwrite this method in a child class to obtain custom behaviour.
	 *
	 * @param array $values Array of values to be sanitized and then stored into
	 *                      $this->data.
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
	 * Sanitize a single value. Called from $this->sanitize_data. Default
	 * sanitization excludes empty values.
	 *
	 * Overwrite this method in a child class to obtain custom behaviour.
	 *
	 * @param string $value The value to sanitize.
	 *
	 * @return mixed Returns sanitized value, or null.
	 */
	public function sanitize_data_filter( $value ) {

		// TODO: all fields should provide their own sanitize which shouldn't
		// be part of a UI class.

		// If the field provides its own validation, use it.
		if ( isset( $this->raw_custom_field['sanitize'] ) ) {
			return call_user_func( $this->raw_custom_field['sanitize'], $value );
		}

		if ( ! is_null( $value ) && '' !== $value ) {         // do not use 'empty()' -> https://www.virendrachandak.com/techtalk/php-isset-vs-empty-vs-is_null/ .
			return $value;
		}

		return null;
	}

	/**
	 * Save data to DB.
	 *
	 * Overwrite this method in a child class to obtain custom behaviour.
	 *
	 * @param array $values Array of values to be sanitized and then stored into $this->data.
	 */
	public function save_data( $values ) {

		// Will sanitize data and store them in $field->data.
		$this->sanitize_data( $values );

		// Bail out, if the post id isn't set in the request or isn't numeric.
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/665.
		if ( ! isset( $_POST['post_ID'] ) || ! is_numeric( $_POST['post_ID'] ) ) {
			return;
		}

		$entity_id = intval( $_POST['post_ID'] );

		// Take away old values.
		delete_post_meta( $entity_id, $this->meta_name );

		// insert new values, respecting cardinality.
		$single = ( 1 === $this->cardinality );
		foreach ( $this->data as $value ) {
			$this->log->trace( "Saving $value to $this->meta_name for entity $entity_id..." );
			// To avoid duplicate values
			delete_post_meta( $entity_id, $this->meta_name, $value );
			$meta_id = add_post_meta( $entity_id, $this->meta_name, $value, $single );
			$this->log->debug( "$value to $this->meta_name for entity $entity_id saved with id $meta_id." );
		}
	}

	/**
	 * Returns the HTML tag that will contain the Field. By default the we
	 * return a <div> with data- attributes on cardinality and expected types.
	 *
	 * It is useful to provide data- attributes for the JS scripts.
	 *
	 * Overwrite this method in a child class to obtain custom behaviour.
	 */
	public function html_wrapper_open() {

		return "<div class='wl-field' data-cardinality='$this->cardinality'>";
	}

	/**
	 * Returns Field HTML (nonce included).
	 *
	 * Overwrite this method (or methods called from this method) in a child
	 * class to obtain custom behaviour.
	 *
	 * The HTML fragment includes the following parts:
	 * * html wrapper open.
	 * * heading.
	 * * nonce.
	 * * stored values.
	 * * an empty input when there are no stored values.
	 * * an add button to add more values.
	 * * html wrapper close.
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
		$html  .= $this->get_stored_values_html( $count );

		// Print the empty <input> to add new values.
		if ( 0 === $count ) { // } || $count < $this->cardinality ) { DO NOT print empty inputs unless requested by the editor since fields might support empty strings.
			$this->log->debug( 'Going to print an empty HTML input...' );
			$html .= $this->html_input( '' );    // Will print an empty <input>.
			$count ++;
		}

		// If cardinality allows it, print button to add new values.
		$html .= $this->get_add_button_html( $count );

		// Close the HTML wrapper.
		$html .= $this->html_wrapper_close();

		return $html;
	}

	/**
	 * Print the heading with the label for the metabox.
	 *
	 * @since 3.15.0
	 * @return string The heading html fragment.
	 */
	protected function get_heading_html() {

		return "<h3>$this->label</h3>";
	}

	/**
	 * Print the stored values.
	 *
	 * @since 3.15.0
	 *
	 * @param int $count An output value: the number of printed values.
	 *
	 * @return string The html fragment.
	 */
	protected function get_stored_values_html( &$count ) {

		$html = '';

		// print data loaded from DB.
		$count = 0;
		if ( $this->data ) {
			foreach ( $this->data as $value ) {
				if ( $count < $this->cardinality ) {
					$this->log->debug( "Going to print an HTML input #$count with $value..." );
					$fragment = $this->html_input( $value );

					// If the fragment is empty, continue to the next one. This is necessary because the
					// metabox may reference an invalid value which would cause the metabox not to print,
					// returning an empty html fragment.
					//
					// See https://github.com/insideout10/wordlift-plugin/issues/818
					if ( '' === $fragment ) {
						continue;
					}

					$html .= $fragment;
					$count ++;
				}
			}
		}

		return $html;
	}

	/**
	 * Get the add button html.
	 *
	 * This function is protected, allowing extending class to further customize
	 * the add button html code.
	 *
	 * @since 3.15.0
	 *
	 * @param int $count The current number of values.
	 *
	 * @return string The add button html code.
	 */
	protected function get_add_button_html( $count ) {

		// If cardinality allows it, print button to add new values.
		if ( $count < $this->cardinality ) {
			return '<button class="button wl-add-input wl-button" type="button">' . esc_html__( 'Add' ) . '</button>';
		}

		// Return an empty string.
		return '';
	}

	/**
	 * Get the add custom button html.
	 *
	 * This function is protected, allowing extending class to further customize
	 * the add button html code.
	 *
	 * @since 3.15.0
	 *
	 * @param int $count The current number of values.
	 *
	 * @return string The add button html code.
	 */
	protected function get_add_custom_button_html( $count, $label, $class = '' ) {

		// If cardinality allows it, print button to add new values.
		if ( $count < $this->cardinality ) {
			return '<button class="button wl-add-input wl-button wl-add-input--sameas '.$class.'" type="button">' . esc_html__( $label ) . '</button>';
		}

		// Return an empty string.
		return '';
	}

	/**
	 * Return a single <input> tag for the Field.
	 *
	 * @param mixed $value Input value.
	 *
	 * @return string The html code fragment.
	 */
	public function html_input( $value ) {
		@ob_start();
		?>
        <div class="wl-input-wrapper">
            <input
                    type="text"
                    id="<?php echo esc_attr( $this->meta_name ); ?>"
                    name="wl_metaboxes[<?php echo $this->meta_name ?>][]"
                    value="<?php echo esc_attr( $value ); ?>"
                    style="width:88%"
            />

            <button class="button wl-remove-input wl-button" type="button">
				<?php esc_html_e( 'Remove', 'wordlift' ); ?>
            </button>
        </div>
		<?php
		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Returns closing for the wrapper HTML tag.
	 */
	public function html_wrapper_close() {

		return '</div>';
	}

}
