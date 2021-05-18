<?php

/**
 * Field to manage the address. The pattern followed is to simply build an array of subfields using the base WL_Metabox_field class,
 * and act as a proxy between WL_Metabox and them.
 * 
 * @since 3.2.0
 */
class WL_Metabox_Field_address extends WL_Metabox_Field {

	/**
	 * Sub-fields contained in the Field.
	 *
	 * @since 3.2.0
	 * @access private
	 * @var $subfields Array of WL_Metabox_Field objects, each dealing with a part of the http://schema.org/PostalAddress structure.
	 */
	private $subfields;
	
	/**
	 * Constructor.
	 * 
	 * @param array $args Set of fields containing info to build the subfields.
	 * The structure of $args is:
	 * array( 'address' => array( ... array of subfields ... ) )
	 */
	public function __construct( $args ) {
		
		$this->label = key( $args );
		
		// leverage the WL_Metabox_Field class to build the subfields
		$this->subfields = array();
		// Loop over subfields. Using 'reset' to take the data contained in the first element of $args
		foreach ( reset( $args ) as $key => $subfield ) {
			$this->subfields[] = new WL_Metabox_Field( array( $key => $subfield ) );
		}
		
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
	 * 
	 * @param array $values Values coming from $_POST and passed from WL_Metabox. We just send to each subfield its own value.
	 */
	public function save_data( $values ) {
		
		foreach ( $this->subfields as $subfield ) {
			$subfield_value = isset( $values[ $subfield->meta_name ] )? $values[ $subfield->meta_name ] : null;
			$subfield->save_data( $subfield_value );
		}
	}

	/**
	 * Returns Field HTML (nonce included).
	 * 
	 * @return string Field HTML
	 */
	public function html() {
		@ob_start();
		// Open main <div> for the Field, then insert label and nonce
		?>
			<div class='wl-field'>
			<h3><?php echo $this->label ?></h3>
			<?php echo $this->html_nonce() ?>

		<?php
		// print data loaded from DB
		foreach ( $this->subfields as $subfield ) :
			$value = isset( $subfield->data[0] )? $subfield->data[0] : '';
		?>
			<div class="wl-input-wrapper">
				<label
					for="wl_metaboxes[<?php echo $this->meta_name ?>][<?php echo $subfield->meta_name ?>]"
					style="display:inline-block; width:20%;"
				>
					<?php echo $subfield->label ?>
				</label>

				<input
					type="text"
					name="wl_metaboxes[<?php echo $this->meta_name ?>][<?php echo $subfield->meta_name ?>]"
					value="<?php echo $value; ?>"
					style="width:78%;"
				/>
			</div>
		<?php
		endforeach;
		$html = ob_get_clean();

		// Close the HTML wrapper
		$html .= $this->html_wrapper_close();

		return $html;
	}
}