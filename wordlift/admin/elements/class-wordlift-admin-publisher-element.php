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
class Wordlift_Admin_Publisher_Element extends Wordlift_Admin_Author_Element {

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
	 * Create a {@link Wordlift_Admin_Publisher_Element} instance.
	 *
	 * @param \Wordlift_Publisher_Service     $publisher_service The {@link Wordlift_Publisher_Service} instance.
	 * @param \Wordlift_Admin_Tabs_Element    $tabs_element The {@link Wordlift_Admin_Tabs_Element} instance.
	 * @param \Wordlift_Admin_Select2_Element $select_element The {@link Wordlift_Admin_Select_Element} instance.
	 *
	 * @since 3.11.0
	 */
	public function __construct( $publisher_service, $tabs_element, $select_element ) {
		parent::__construct( $publisher_service, $select_element );

		$this->publisher_service = $publisher_service;

		// Child elements.
		$this->tabs_element = $tabs_element;

	}

	/**
	 * @inheritdoc
	 */
	public function render( $args ) {

		// Parse the arguments and merge with default values.
		$params = wp_parse_args(
			$args,
			array(
				'id'   => uniqid( 'wl-input-' ),
				'name' => uniqid( 'wl-input-' ),
			)
		);

		// Get the number of potential candidates as publishers.
		$count = $this->publisher_service->count();

		$this->tabs_element->render(
			array(
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
			)
		);

		// Finally return the element instance.
		return $this;
	}

	/**
	 * Render the publisher's select.
	 *
	 * @param array $params An array of parameters.
	 *
	 * @since 3.11.0
	 */
	public function select( $params ) {

		// Get the configured publisher id. In case a publisher id is already configured
		// this must be pre-loaded in the options.
		$publisher_id = Wordlift_Configuration_Service::get_instance()->get_publisher_id();

		// Get the publisher data.
		$data = $this->publisher_service->query();
		array_unshift(
			$data,
			array(
				'id'            => '',
				'text'          => _x( '(none)', 'Publisher Select in Settings Screen.', 'wordlift' ),
				'type'          => '',
				'thumbnail_url' => false,
			)
		);

		// Call the select internal render.
		$this->do_render( $params, $publisher_id, $data );

	}

	/**
	 * Render the 'create publisher' form.
	 *
	 * @param array $params An array of parameters.
	 *
	 * @since 3.11.0
	 */
    // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function create( $params ) {
		?>
		<p>
			<strong><?php esc_html_e( 'Are you publishing as an individual or as a company?', 'wordlift' ); ?></strong>
		</p>

		<p id="wl-publisher-type">
			<span>
				<input
						id="wl-publisher-person"
						type="radio"
						name="wl_publisher[type]"
						value="person"
						checked="checked"
				>

				<label for="wl-publisher-person">
					<?php esc_html_e( 'Person', 'wordlift' ); ?>
				</label>
			</span>

			<span>
				<input
						id="wl-publisher-company"
						type="radio"
						name="wl_publisher[type]"
						value="organization"
				>

				<label for="wl-publisher-company">
					<?php esc_html_e( 'Company', 'wordlift' ); ?>
				</label>
			</span>
		</p>

		<p id="wl-publisher-name">
			<input
					type="text"
					name="wl_publisher[name]"
					placeholder="<?php echo esc_attr__( "What's your name?", 'wordlift' ); ?>"
			>
		</p>

		<div id="wl-publisher-logo">
			<input
					type="hidden"
					id="wl-publisher-media-uploader-id"
					name="wl_publisher[thumbnail_id]"
			/>

			<p>
				<b><?php esc_html_e( "Choose the publisher's Logo", 'wordlift' ); ?></b>
			</p>

			<p>
				<img id="wl-publisher-media-uploader-preview"/>

				<button
						type="button"
						class="button"
						id="wl-publisher-media-uploader">
					<?php esc_html_e( 'Select an existing image or upload a new one', 'wordlift' ); ?>
				</button>
			</p>
		</div>
		<?php
	}

}
