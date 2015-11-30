<?php

/**
 * Field to manage the addresses.
 * 
 * @since 3.3.0
 */
class WL_Metabox_Field_address extends WL_Metabox_Field {

	/**
	 * The Log service.
	 *
	 * @since 3.3.0
	 * @access private
	 * @var $subfields Array of WL_Metabox_Field objects, each dealing with a part of the http://schema.org/PostalAddress structure.
	 */
	private $subfields;
	
	/**
	 * Constructor.
	 */
	public function __construct( $args ) {
		
		// leverage the WL_Metabox_Field class to build the subfields
		$this->subfields = array();
		foreach ( reset($args) as $key => $subfield ) {
			$this->subfields[] = new WL_Metabox_Field( array( $key => $subfield ) );
		}
		
		$this->label = 'address';
		
		// $_POST array key in which we will pass the values
		$this->meta_name = 'wl_grouped_field_address';
	}

	/**
	 * Load data from DB and store the resulting array in $this->data.
	 */
	public function get_data() {
		
		foreach ( $this->subfields as $subfield ) {
			$subfield->get_data();
		}
	}

	/**
	 * Save data to DB.
	 */
	public function save_data( $values ) {
		
		foreach ( $this->subfields as $subfield ) {
			$subfield_value = isset( $values[ $subfield->meta_name ] )? $values[ $subfield->meta_name ] : null;
			$subfield->save_data( $subfield_value );
		}
	}

	/**
	 * Returns Field HTML (nonce included).
	 * Overwrite this method (or methods called from this method) in a child class to obtain custom behaviour.
	 */
	public function html() {

		// Open main <div> for the Field
		$html = "<div class='wl-field'>";

		// Label
		$html .= "<h3>$this->label</h3>";

		// print nonce
		$html .= $this->html_nonce();

		// print data loaded from DB
		foreach ( $this->subfields as $subfield ) {
			
			$value = isset( $subfield->data[0] )? $subfield->data[0] : '';
			
			$html .= <<<EOF
			<div class="wl-input-wrapper">
				<label for="wl_metaboxes[$this->meta_name][$subfield->meta_name]" style="display:inline-block; width:20%;">$subfield->label</label>
				<input type="text" name="wl_metaboxes[$this->meta_name][$subfield->meta_name]" value="$value" style="width:78%;" />
			</div>
EOF;
		}

		// Close the HTML wrapper
		$html .= $this->html_wrapper_close();

		return $html;
	}
}


