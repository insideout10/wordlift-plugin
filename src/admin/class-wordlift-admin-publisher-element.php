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
//		$params = wp_parse_args( $args, array() );

		// Get the number of potential candidates as publishers.
		$count = $this->publisher_service->count();

		$this->tabs_element->render( array(
			'tabs'   => array(
				array(
					'label'    => 'Select an Existing Publisher',
					'callback' => array( $this, 'select' ),
				),
				array(
					'label'    => 'Create a New Publisher',
					'callback' => array( $this, 'create' ),
				),
			),
			// Set the default tab according to the number of potential publishers
			// configured in WP: 0 = select, 1 = create.
			'active' => 0 === $count ? 1 : 0,
		) );

//		include( plugin_dir_path( __FILE__ ) . 'partials/wordlift-admin-settings-page-publisher-section.php' );

		return $this;
	}

	public function select() {

		// Get the configured publisher id. In case a publisher id is already configured
		// this must be pre-loaded in the options.
		$publisher_id = $this->configuration_service->get_publisher_id();

		$post = get_post( $publisher_id );

		$this->select_element->render( array(
			'value'              => $publisher_id,
			'options'            => array( $post->ID => $post->post_title ),
			'data'               => $this->publisher_service->query(),
			'template-result'    => '<img src="<%= obj.thumbnail_url || "" %>" /><span class="wl-select2"><%= obj.text %></span><span class="wl-select2-type"><%= obj.type || "" %></span>',
			'template-selection' => '<img src="<%= obj.thumbnail_url || "" %>" /><span class="wl-select2"><%= obj.text %></span><span class="wl-select2-type"><%= obj.type || "" %></span>',
		) );

	}

	public function create() {
		?>
		Hello Create
		<?php
	}

}
