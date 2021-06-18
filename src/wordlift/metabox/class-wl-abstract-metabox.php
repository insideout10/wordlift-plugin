<?php
namespace Wordlift\Metabox;
/**
 * This class provides abstract metbox which can be extended for term pages.
 *
 * @since      3.31.6
 * @package    Wordlift
 * @subpackage Wordlift/admin/WL_Metabox
 */

class WL_Abstract_Meta_Box {

	const POST = 1;

	const TERM = 2;

	/**
	 * The metabox custom fields for the current {@link WP_Post}.
	 *
	 * @since  3.1.0
	 * @access public
	 * @var array $fields The metabox custom fields.
	 */
	public $fields;

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since 3.15.4
	 *
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * WL_Metabox constructor.
	 *
	 * @since 3.1.0
	 */
	public function __construct() {

		$this->log = Wordlift_Log_Service::get_logger( get_class() );
	}

	/**
	 * Add a callback to print the metabox in page.
	 * Wordpress will fire the $this->html() callback at the right time.
	 */
	public function add_main_metabox() {

		// Build the fields we need to print.
		$this->instantiate_fields( get_the_ID(), self::POST );

		// Bailout if there are no actual fields, we do not need a metabox in that case.
		if ( empty( $this->fields ) ) {
			return;
		}

		// Add main metabox (will print also the inner fields).
		$id    = uniqid( 'wl-metabox-' );
		$title = get_the_title() . ' ' . __( 'properties', 'wordlift' );

		// WordPress 4.2 do not accept an array of screens as parameter, have to do be explicit.
		foreach ( Wordlift_Entity_Service::valid_entity_post_types() as $screen ) {
			add_meta_box( $id, $title, array(
				$this,
				'html',
			), $screen, 'normal', 'high' );
		}

		// Add filter to change the metabox CSS class.
		//
		// @since 3.20.0 Since we support post types other than `entity` for entities, we need to set the `screen`
		//  dynamically according to the `get_current_screen()` function.
		$current_screen = get_current_screen();
		$screen         = $current_screen ? $current_screen->post_type : 'entity';
		add_filter( "postbox_classes_{$screen}_$id", 'wl_admin_metaboxes_add_css_class' );

	}

	/**
	 * Render the metabox html.
	 *
	 * @since 3.1.0
	 *
	 */
	public function html() {

		// Loop over the fields.
		foreach ( $this->fields as $field ) {

			// load data from DB (values will be available in $field->data).
			$field->get_data();

			// print field HTML (nonce included).
			echo $field->html();
		}

	}

	/**
	 * Read the WL <-> Schema mapping and build the Fields for the entity being edited.
	 *
	 * Note: the first function that calls this method will instantiate the fields.
	 * Why it isn't called from the constructor? Because we need to hook this process as late as possible.
	 *
	 * @param int $id | $term_id The post id or term id.
	 *
	 * @param $type int Post or Term
	 *
	 * @since 3.1.0
	 */
	public function instantiate_fields( $id, $type ) {

		$this->log->trace( "Instantiating fields for entity post $id..." );

		// This function must be called only once. Not called from the constructor because WP hooks have a rococo ordering.
		if ( isset( $this->fields ) ) {
			return;
		}
		if ( $type === self::POST ) {
			$entity_type = wl_entity_taxonomy_get_custom_fields( $id );
		} else if ( $type === self::TERM ) {
			$term_entity_types = get_term_meta( $id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
			$term_entity_types = array_map( 'get_term', $term_entity_types );
			$entity_type       = wl_get_custom_fields_by_entity_type( $term_entity_types );
		}
		if ( isset( $entity_type ) ) {

			/*
			 * Might not have any relevant meta box field, for example for articles,
			 * therefor make sure fields are at least an empty array to help the considered
			 * in other functions using it.
			 */
			$this->fields = array();

			/**
			 * In some special case, properties must be grouped in one field (e.g. coordinates) or dealed with custom methods.
			 * We must divide fields in two groups:
			 * - simple: accept values for one property
			 * - grouped: accept values for more properties, or for one property that needs a specific metabox.
			 */
			$metaboxes         = $this->group_properties_by_input_field( $entity_type );
			$simple_metaboxes  = $metaboxes[0];
			$grouped_metaboxes = $metaboxes[1];

			// Loop over simple entity properties.
			foreach ( $simple_metaboxes as $key => $property ) {

				// Info passed to the metabox.
				$info         = array();
				$info[ $key ] = $property;

				// Build the requested field as WL_Metabox_Field_ object.
				$this->add_field( $info );

			}

			// Loop over grouped properties.
			foreach ( $grouped_metaboxes as $key => $property ) {

				// Info passed to the metabox.
				$info         = array();
				$info[ $key ] = $property;

				// Build the requested field group as WL_Metabox_Field_ object.
				$this->add_field( $info, true );

			}
		}

	}

	/**
	 * Separates metaboxes in simple and grouped.
	 *
	 * @param array $custom_fields Information on the entity type.
	 *
	 * @return array
	 */
	public function group_properties_by_input_field( $custom_fields ) {

		$simple_properties  = array();
		$grouped_properties = array();

		// Loop over possible entity properties.
		foreach ( $custom_fields as $key => $property ) {

			// Check presence of predicate and type.
			if ( isset( $property['predicate'] ) && isset( $property['type'] ) ) {

				// Check if input_field is defined.
				if ( isset( $property['input_field'] ) && '' !== $property['input_field'] ) {

					$grouped_key = $property['input_field'];

					// Update list of grouped properties.
					$grouped_properties[ $grouped_key ][ $key ] = $property;

				} else {

					// input_field not defined, add simple metabox.
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
	 * @param array $args The field's information.
	 * @param bool $grouped Flag to distinguish between simple and grouped fields.
	 */
	public function add_field( $args, $grouped = false ) {

		if ( $grouped ) {

			// Special fields (sameas, coordinates, etc.).
			//
			// Build Field with a custom class (e.g. WL_Metabox_Field_date).
			$field_class = 'WL_Metabox_Field_' . key( $args );

		} else {

			// Simple fields (string, uri, boolean, etc.).
			//
			// Which field? We want to use the class that is specific for the field.
			$meta      = key( $args );
			$this_meta = $args[ $meta ];

			// If the field declares what metabox it wants, use that one.
			if ( isset( $this_meta['metabox']['class'] ) ) {

				$field_class = $this_meta['metabox']['class'];

			} elseif ( ! isset( $this_meta['type'] ) || Wordlift_Schema_Service::DATA_TYPE_STRING === $this_meta['type'] ) {

				// TODO: all fields should explicitly declare the required WL_Metabox.
				// When they will remove this.
				//
				// Use default WL_Metabox_Field (manages strings).
				$field_class = 'WL_Metabox_Field';

			} else {

				// TODO: all fields should explicitly declare the required WL_Metabox.
				// When they will remove this.
				//
				// Build Field with a custom class (e.g. WL_Metabox_Field_date).
				$field_class = 'WL_Metabox_Field_' . $this_meta['type'];

			}

		}
		// End if().

		// Call apropriate constructor (e.g. WL_Metabox_Field_... ).
		$this->fields[] = new $field_class( $args );
	}

	/**
	 * Save the form data for the specified entity {@link WP_Post}'s id.
	 *
	 * @param int $id The entity's {@link WP_Post}'s id.
	 *
	 * @param $type int Post or term
	 *
	 * @since 3.5.4
	 */
	public function save_form_data( $id, $type ) {

		$this->log->trace( "Saving form data for entity post $id..." );

		// Build Field objects.
		$this->instantiate_fields( $id, $type );

		// Check if WL metabox form was posted.
		if ( ! isset( $_POST['wl_metaboxes'] ) ) {
			$this->log->debug( "`wl_metaboxes`, skipping..." );

			return;
		}

		foreach ( $this->fields as $field ) {

			// Verify nonce.
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

		/**
		 * Filter: 'wl_save_form_pre_push_entity' - Allow to hook right
		 * before the triples are pushed to the linked dataset.
		 *
		 * @param int $id The entity id.
		 * @param int $id The post data.
		 *
		 * @since  3.18.2
		 *
		 */
		do_action( 'wl_save_form_pre_push_entity', $id, $_POST );

		do_action( 'wl_legacy_linked_data__push', $id );

	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 3.0.0
	 */
	public function enqueue_scripts_and_styles() {

		// Use the minified version if PW_DEBUG isn't set.
		$min = ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ? '.min' : '';

		// Load the jquery-ui-timepicker-addon library.
		wp_enqueue_style( 'wl-flatpickr', dirname( plugin_dir_url( __FILE__ ) ) . "/js/flatpickr/flatpickr$min.css", array(), '3.0.6' );
		wp_enqueue_script( 'wl-flatpickr', dirname( plugin_dir_url( __FILE__ ) ) . "/js/flatpickr/flatpickr$min.js", array( 'jquery' ), '3.0.6', true );

		// Leaflet.
		wp_enqueue_style( 'wl-leaflet', 'https://unpkg.com/leaflet@1.6.0/dist/leaflet.css', array(), '1.6.0' );
		wp_enqueue_script( 'wl-leaflet', 'https://unpkg.com/leaflet@1.6.0/dist/leaflet.js', array(), '1.6.0' );

		// Add AJAX autocomplete to facilitate metabox editing.
		wp_enqueue_script( 'wl-entity-metabox-utility', dirname( plugin_dir_url( __FILE__ ) ) . '/js/wl_entity_metabox_utilities.js' );
		wp_localize_script( 'wl-entity-metabox-utility', 'wlEntityMetaboxParams', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'action'   => 'entity_by_title',
			)
		);

	}
}
