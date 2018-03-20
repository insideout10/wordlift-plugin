<?php
/**
 * Elements: Publisher element.
 *
 * A complex element that displays the current publisher with a select to select
 * another one from existing Organizations/Persons or a form to create a new one.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the {@link Wordlift_Admin_Publisher_Element} class.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */
class Wordlift_Admin_Publisher_Element implements Wordlift_Admin_Element {

	/**
	 * The {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.11.0
	 * @access private
	 * @var \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	private $configuration_service;

	/**
	 * The {@link Wordlift_Publisher_Service} instance.
	 *
	 * @since  3.11.0
	 * @access private
	 * @var \Wordlift_Publisher_Service $publisher_service The {@link Wordlift_Publisher_Service} instance.
	 */
	private $publisher_service;
	/**
	 * @var Wordlift_Admin_Tabs_Element
	 */
	private $tabs_element;
	/**
	 * @var Wordlift_Admin_Select2_Element
	 */
	private $select_element;

	/**
	 * Create a {@link Wordlift_Admin_Publisher_Element} instance.
	 *
	 * @since 3.11.0
	 *
	 * @param \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 * @param \Wordlift_Publisher_Service     $publisher_service     The {@link Wordlift_Publisher_Service} instance.
	 * @param \Wordlift_Admin_Tabs_Element    $tabs_element          The {@link Wordlift_Admin_Tabs_Element} instance.
	 * @param \Wordlift_Admin_Select2_Element $select_element        The {@link Wordlift_Admin_Select_Element} instance.
	 */
	function __construct( $configuration_service, $publisher_service, $tabs_element, $select_element ) {

		$this->configuration_service = $configuration_service;
		$this->publisher_service     = $publisher_service;

		// Child elements.
		$this->tabs_element   = $tabs_element;
		$this->select_element = $select_element;
	}

	/**
	 * @inheritdoc
	 */
	public function render( $args ) {

		// Parse the arguments and merge with default values.
		$params = wp_parse_args( $args, array(
			'id'   => uniqid( 'wl-input-' ),
			'name' => uniqid( 'wl-input-' ),
		) );

		// Get the number of potential candidates as publishers.
		$count = $this->publisher_service->count();

		$this->tabs_element->render( array(
			'tabs'   => array(
				array(
					'label'    => __( 'Select an Existing Publisher', 'wordlift' ),
					'callback' => array( $this, 'select' ),
					'args'     => $params,
				),
				array(
					'label'    => __( 'Create a New Publisher', 'wordlift' ),
					'callback' => array( $this, 'create' ),
					'args'     => $params,
				),
			),
			// Set the default tab according to the number of potential publishers
			// configured in WP: 0 = select, 1 = create.
			'active' => 0 === $count ? 1 : 0,
		) );

		// Finally return the element instance.
		return $this;
	}

	/**
	 * Render the publisher's select.
	 *
	 * @since 3.11.0
	 *
	 * @param array $params An array of parameters.
	 */
	public function select( $params ) {

		// Get the configured publisher id. In case a publisher id is already configured
		// this must be pre-loaded in the options.
		$publisher_id = $this->configuration_service->get_publisher_id();

		// Get the publisher post. This must be prepopulated in the `options` array
		// in order to make it preselected in Select2.
		$post = get_post( $publisher_id );

		// Prepare the URLs for entities which don't have logos.
		$person_thumbnail_url       = plugin_dir_url( dirname( __FILE__ ) ) . 'images/person.png';
		$organization_thumbnail_url = plugin_dir_url( dirname( __FILE__ ) ) . 'images/organization.png';

		// Finally render the Select.
		$this->select_element->render( array(
			// Id.
			'id'                 => $params['id'],
			// Name.
			'name'               => $params['name'],
			// The selected id.
			'value'              => $publisher_id,
			// The selected item (must be in the options for Select2 to display it).
			'options'            => $post ? array( $post->ID => $post->post_title ) : array(),
			// The list of available options.
			'data'               => $this->publisher_service->query(),
			// The HTML template for each option.
			'template-result'    => "<div class='wl-select2-result'><span class='wl-select2-thumbnail' style='background-image: url( <%= obj.thumbnail_url || ( 'Organization' === obj.type ? '$organization_thumbnail_url' : '$person_thumbnail_url' ) %> );'>&nbsp;</span><span class='wl-select2'><%= obj.text %></span><span class='wl-select2-type'><%= obj.type %></span></div>",
			// The HTML template for the selected option.
			'template-selection' => "<div class='wl-select2-selection'><span class='wl-select2-thumbnail' style='background-image: url( <%= obj.thumbnail_url || ( 'Organization' === obj.type ? '$organization_thumbnail_url' : '$person_thumbnail_url' ) %> );'>&nbsp;</span><span class='wl-select2'><%= obj.text %></span><span class='wl-select2-type'><%= obj.type %></span></div>",
		) );

	}

	/**
	 * Render the 'create publisher' form.
	 *
	 * @since 3.11.0
	 *
	 * @param array $params An array of parameters.
	 */
	public function create( $params ) {
		?>
		<p>
			<strong><?php esc_html_e( 'Are you publishing as an individual or as a company?', 'wordlift' ) ?></strong>
		</p>
		<p id="wl-publisher-type">
			<span>
				<input id="wl-publisher-person" type="radio"
				       name="wl_publisher[type]" value="person"
				       checked="checked">
				<label for="wl-publisher-person"><?php
					esc_html_e( 'Person', 'wordlift' ) ?></label>
			</span>
			<span>
				<input id="wl-publisher-company" type="radio"
				       name="wl_publisher[type]" value="organization"">
				<label for="wl-publisher-company"><?php
					esc_html_e( 'Company', 'wordlift' ) ?></label>
			</span>
		</p>
		<p id="wl-publisher-name">
			<input type="text" name="wl_publisher[name]"
			       placeholder="<?php echo esc_attr__( "What's your name?", 'wordlift' ) ?>">
		</p>
		<div id="wl-publisher-logo">
			<input type="hidden" id="wl-publisher-media-uploader-id"
			       name="wl_publisher[thumbnail_id]" />
			<p>
				<b><?php esc_html_e( "Choose the publisher's Logo", 'wordlift' ) ?></b>
			</p>
			<p>
				<img id="wl-publisher-media-uploader-preview" />
				<button type="button" class="button"
				        id="wl-publisher-media-uploader"><?php
						esc_html_e( 'Select an existing image or upload a new one', 'wordlift' ); ?></button>
			</p>
		</div>
		<?php
	}

}
