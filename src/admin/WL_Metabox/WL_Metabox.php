<?php

require_once( 'WL_Metabox_Field.php' );
require_once( 'WL_Metabox_Field_date.php' );
require_once( 'WL_Metabox_Field_uri.php' );
require_once( 'WL_Metabox_Field_coordinates.php' );
require_once( 'WL_Metabox_Field_sameas.php' );

class WL_Metabox {

	public $fields;

	public function __construct() {

		// Add hooks to print metaboxes and save submitted data.
		add_action( 'add_meta_boxes', array( &$this, 'add_main_metabox' ) );
		add_action( 'wl_linked_data_save_post', array( &$this, 'save_form_data' ) );

		// Enqueue js and css
		$this->enqueue_scripts_and_styles();

	}

	/**
	 * Add a callback to print the metabox in page.
	 * Wordpress will fire the $this->html() callback at the right time.
	 */
	public function add_main_metabox() {

		// Add main metabox (will print also the inner fields)
		add_meta_box( 'uniqueMetaboxId', get_the_title() . ' properties', array(
			$this,
			'html'
		), WL_ENTITY_TYPE_NAME, 'normal', 'high' );
	}

	/**
	 * Called from WP to print the metabox content in page.
	 *
	 * @param WP_Post $entity
	 */
	public function html( $entity ) {

		// Build the fields we need to print.
		$this->instantiate_fields( $entity->ID );

		$html = '';

		// Loop over the fields
		foreach ( $this->fields as $field ) {

			// load data from DB (values will be available in $field->data)
			$field->get_data();

			// print field HTML (nonce included)
			$html .= $field->html();
		}

		// Echo Fields in page.
		echo $html;
	}

	/**
	 * Read the WL <-> Schema mapping and build the Fields for the entity being edited.
	 *
	 * @param int $entity_id
	 *
	 * Note: the first function that calls this method will instantiate the fields.
	 * Why it isn't called from the constructor? Because we need to hook this process as late as possible.
	 */
	public function instantiate_fields( $entity_id ) {

		// This function must be called only once. Not called from the constructor because WP hooks have a rococo ordering
		if ( isset( $this->fields ) ) {
			return;
		}

		$entity_type = wl_entity_taxonomy_get_custom_fields( $entity_id );

		if ( isset( $entity_type ) ) {

			/**
			 * In some special case, properties must be grouped in one field (e.g. coordinates) or dealed with custom methods.
			 * We must divide fields in two groups:
			 * - simple: accept values for one property
			 * - grouped: accept values for more properties, or for one property that needs a specific metabox.
			 */
			$metaboxes         = $this->group_properties_by_input_field( $entity_type );
			$simple_metaboxes  = $metaboxes[0];
			$grouped_metaboxes = $metaboxes[1];

			// Loop over simple entity properties
			foreach ( $simple_metaboxes as $key => $property ) {

				// Info passed to the metabox
				$info         = array();
				$info[ $key ] = $property;

				// Build the requested field as WL_Metabox_Field_ object
				$this->add_field( $info );
			}

			// Loop over grouped properties
			foreach ( $grouped_metaboxes as $key => $property ) {

				// Info passed to the metabox
				$info         = array();
				$info[ $key ] = $property;

				// Build the requested field group as WL_Metabox_Field_ object
				$this->add_field( $info, true );
			}

		}
	}

	/**
	 * Separes metaboxes in simple and grouped.
	 *
	 * @param array $custom_fields Information on the entity type.
	 */
	public function group_properties_by_input_field( $custom_fields ) {

		$simple_properties  = array();
		$grouped_properties = array();

		// Loop over possible entity properties
		foreach ( $custom_fields as $key => $property ) {

			// Check presence of predicate and type
			if ( isset( $property['predicate'] ) && isset( $property['type'] ) ) {

				// Check if input_field is defined
				if ( isset( $property['input_field'] ) && $property['input_field'] !== '' ) {

					$grouped_key = $property['input_field'];

					// Update list of grouped properties
					$grouped_properties[ $grouped_key ][ $key ] = $property;

				} else {

					// input_field not defined, add simple metabox
					$simple_properties[ $key ] = $property;
				}
			}
		}

		return array( $simple_properties, $grouped_properties );
	}

	/**
	 * Add a Field to the current Metabox, based on the description of the Field.
	 * This method is a rude factory for Field objects.
	 *
	 * @param type $args
	 * @param type $grouped Flag to distinguish between simple and grouped Fields
	 */
	public function add_field( $args, $grouped = false ) {

		if ( $grouped ) {
			// Special fields (sameas, coordinates, etc.)

			// Build Field with a custom class (e.g. WL_Metabox_Field_date)
			$field_class = 'WL_Metabox_Field_' . key( $args );

		} else {
			// Simple fields (string, uri, boolean, etc.)

			// Which field? We want to use the class that is specific for the field.
			$meta = key( $args );
			if ( ! isset( $args[ $meta ]['type'] ) || ( $args[ $meta ]['type'] == WL_DATA_TYPE_STRING ) ) {
				// Use default WL_Metabox_Field (manages strings)
				$field_class = 'WL_Metabox_Field';
			} else {
				// Build Field with a custom class (e.g. WL_Metabox_Field_date)
				$field_class = 'WL_Metabox_Field_' . $args[ $meta ]['type'];
			}
		}

		// Call apropriate constructor (e.g. WL_Metabox_Field_... )
		$this->fields[] = new $field_class( $args );
	}

	public function save_form_data( $entity_id ) {

		// Build Field objects
		$this->instantiate_fields( $entity_id );

		// Check if WL metabox form was posted
		if ( ! isset( $_POST['wl_metaboxes'] ) ) {
			return;
		}

		foreach ( $this->fields as $field ) {

			// Verify nonce
			$valid_nonce = $field->verify_nonce();
			if ( $valid_nonce ) {

				$posted_data = $_POST['wl_metaboxes'];
				$field_name  = $field->meta_name;

				// Each Filed only deals with its values.
				if ( isset( $posted_data[ $field_name ] ) ) {

					$values = $posted_data[ $field_name ];
					if ( ! is_array( $values ) ) {
						$values = array( $values );
					}

					// Save data permanently
					$field->save_data( $values );
				}
			}
		}

		wl_linked_data_push_to_redlink( $entity_id );
	}

	// print on page all the js and css the fields will need
	public function enqueue_scripts_and_styles() {

		// dateTimePicker
		wp_enqueue_style( 'datetimepicker', plugin_dir_url( __FILE__ ) . 'admin/css/jquery.datetimepicker.css' );
		wp_enqueue_script( 'datetimepicker', plugin_dir_url( __FILE__ ) . 'admin/js/jquery.datetimepicker.full.min.js', array( 'jquery' ) );

		// Leaflet.
		wp_enqueue_style( 'leaflet_css', plugins_url( 'bower_components/leaflet/dist/leaflet.css', __FILE__ ) );
		wp_enqueue_script( 'leaflet_js', plugins_url( 'bower_components/leaflet/dist/leaflet.js', __FILE__ ) );

		// Add AJAX autocomplete to facilitate metabox editing
		wp_enqueue_script( 'wl-entity-metabox-utility', plugins_url( 'js-client/wl_entity_metabox_utilities.js', __FILE__ ) );
		wp_localize_script( 'wl-entity-metabox-utility', 'wlEntityMetaboxParams', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'action'   => 'entity_by_title'
			)
		);
	}
}