<?php
/**
 * Elements: Person element.
 *
 * A complex element that displays the current person entity associated with a User
 * and enables selecting a new one.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the {@link Wordlift_Admin_Person_Element} class.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */
class Wordlift_Admin_Person_Element implements Wordlift_Admin_Element {

	/**
	 * The {@link Wordlift_Publisher_Service} instance.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift_Publisher_Service $publisher_service The {@link Wordlift_Publisher_Service} instance.
	 */
	private $publisher_service;

	/**
	 * A {@link Wordlift_Admin_Select2_Element} instance.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift_Admin_Select2_Element $select_element A {@link Wordlift_Admin_Select2_Element} instance.
	 */
	private $select_element;

	/**
	 * Create a {@link Wordlift_Admin_Person_Element} instance.
	 *
	 * @since 3.14.0
	 *
	 * @param \Wordlift_Publisher_Service     $publisher_service The {@link Wordlift_Publisher_Service} instance.
	 * @param \Wordlift_Admin_Select2_Element $select_element    The {@link Wordlift_Admin_Select_Element} instance.
	 */
	function __construct( $publisher_service, $select_element ) {

		$this->publisher_service = $publisher_service;

		// Child elements.
		$this->select_element = $select_element;

	}

	/**
	 * @inheritdoc
	 */
	public function render( $args ) {

		// Parse the arguments and merge with default values.
		$params = wp_parse_args( $args, array(
			'id'             => uniqid( 'wl-input-' ),
			'name'           => uniqid( 'wl-input-' ),
			'current_entity' => 0,
		) );

		$current_entity_id = $params['current_entity'];
		$current_entity    = $current_entity_id ? get_post( $current_entity_id ) : null;

		// Prepare the URLs for entities which don't have logos.
		$person_thumbnail_url       = plugin_dir_url( dirname( __FILE__ ) ) . 'images/person.png';
		$organization_thumbnail_url = plugin_dir_url( dirname( __FILE__ ) ) . 'images/organization.png';

		$data = $this->publisher_service->query();

		// Set a default to show when no entity is associated and a way to unassign.
		array_unshift( $data, '' );

		// Finally render the Select.
		$this->select_element->render( array(
			// Id.
			'id'                 => $params['id'],
			// Name.
			'name'               => $params['name'],
			// The selected id.
			'value'              => $current_entity_id,
			// The selected item (must be in the options for Select2 to display it).
			'options'            => $current_entity ? array( $current_entity->ID => $current_entity->post_title ) : array(),
			// The list of available options.
			'data'               => $data,
			// The HTML template for each option.
			'template-result'    => "<div class='wl-select2-result'><span class='wl-select2-thumbnail' style='background-image: url( <%= obj.thumbnail_url || ( 'Organization' === obj.type ? '$organization_thumbnail_url' : '$person_thumbnail_url' ) %> );'>&nbsp;</span><span class='wl-select2'><%= obj.text %></span><span class='wl-select2-type'><%= obj.type %></span></div>",
			// The HTML template for the selected option.
			'template-selection' => "<div class='wl-select2-selection'><span class='wl-select2-thumbnail' style='background-image: url( <%= obj.thumbnail_url || ( 'Organization' === obj.type ? '$organization_thumbnail_url' : '$person_thumbnail_url' ) %> );'>&nbsp;</span><span class='wl-select2'><%= obj.text %></span><span class='wl-select2-type'><%= obj.type %></span></div>",
		) );

		/*
		 * Since we need to support wp version before 4.5, adding the select2
		 * initialization needs to be done in the old ugly way.
		 * Not using closure to prevent multiple hook registry in the unlikely Event
		 * of this class being used more then once on a page.
		 */

		add_action( 'admin_footer', array( $this, 'initialize_select2' ), 999 );

		// Finally return the element instance.
		return $this;
	}

	/**
	 * Add the JS code to initialize select2.
	 *
	 * @since 3.14.0
	 */
	public function initialize_select2() {
		?>
		<script type="text/javascript">
			jQuery( document ).ready( function( $ ) {
				$( '.wl-select2-element' ).each( function( index, element ) {
					const $e = $( element );
					$e.select2(
						{
							width: '100%',
							data: $e.data( 'wl-select2-data' ),
							escapeMarkup: function( markup ) {
								return markup;
							},
							templateResult: _.template( $e.data( 'wl-select2-template-result' ) ),
							templateSelection: _.template( $e.data( 'wl-select2-template-selection' ) ),
							containerCssClass: 'wl-admin-settings-page-select2',
							dropdownCssClass: 'wl-admin-settings-page-select2',
						}
					);
				} );
			} );
		</script>
		<?php
	}

}
