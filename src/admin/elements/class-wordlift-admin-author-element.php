<?php
/**
 * Elements: Author Element.
 *
 * A complex element that displays the current person/organization entity
 * associated with a User and enables selecting a new one.
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
class Wordlift_Admin_Author_Element implements Wordlift_Admin_Element {

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
	 * @param \Wordlift_Publisher_Service     $publisher_service The {@link Wordlift_Publisher_Service} instance.
	 * @param \Wordlift_Admin_Select2_Element $select_element The {@link Wordlift_Admin_Select_Element} instance.
	 *
	 * @since 3.14.0
	 */
	public function __construct( $publisher_service, $select_element ) {

		$this->publisher_service = $publisher_service;

		// Child elements.
		$this->select_element = $select_element;

	}

	/**
	 * @inheritdoc
	 */
	public function render( $args ) {

		// Parse the arguments and merge with default values.
		$params = wp_parse_args(
			$args,
			array(
				'id'             => uniqid( 'wl-input-' ),
				'name'           => uniqid( 'wl-input-' ),
				'current_entity' => 0,
			)
		);

		$current_entity_id = $params['current_entity'];
		$data              = $this->publisher_service->query();

		// Set a default to show when no entity is associated and a way to unassign.
		array_unshift(
			$data,
			array(
				'id'            => '0',
				'text'          => '<em>' . __( '(none)', 'wordlift' ) . '</em>',
				'type'          => '',
				'thumbnail_url' => plugin_dir_url( __DIR__ ) . 'images/pixel.png',
			)
		);

		// Finally do the render, passing along also the current selected entity
		// id and the options data.
		return $this->do_render( $params, $current_entity_id, $data );
	}

	/**
	 * Render the `select` using the provided parameters.
	 *
	 * @param array $params The array of parameters from the `render` function.
	 * @param int   $current_post_id The currently selected {@link WP_Post} `id`.
	 * @param array $data An array of Select2 options.
	 *
	 * @return \Wordlift_Admin_Author_Element $this Return this element.
	 * @since 3.14.0
	 */
	protected function do_render( $params, $current_post_id, $data ) {

		// Queue the script which will initialize the select and style it.
		wp_enqueue_script( 'wl-author-element', plugin_dir_url( __DIR__ ) . 'js/1/author.js', array( 'wordlift-select2' ), WORDLIFT_VERSION, false );
		wp_enqueue_style( 'wl-author-element', plugin_dir_url( __DIR__ ) . 'js/1/author.css', array(), WORDLIFT_VERSION );

		// Prepare the URLs for entities which don't have logos.
		$person_thumbnail_url       = plugin_dir_url( __DIR__ ) . '../images/person.png';
		$organization_thumbnail_url = plugin_dir_url( __DIR__ ) . '../images/organization.png';

		// Get the current post.
		$current_post = $current_post_id ? get_post( $current_post_id ) : null;

		// Finally render the Select.
		$this->select_element->render(
			array(
				// Id.
				'id'      => $params['id'],
				// Name.
				'name'    => $params['name'],
				// Class names.
				'class'   => 'wl-select2-element',
				// The selected id.
				'value'   => $current_post_id,
				// The selected item (must be in the options for Select2 to display it).
				'options' => $current_post ? array( $current_post->ID => $current_post->post_title ) : array(),
				// Data attributes.
				'data'    => array(
					// The list of available options.
					'wl-select2-data'               => wp_json_encode( $data ),
					// The HTML template for each option.
					'wl-select2-template-result'    => "<div class='wl-select2-result'><span class='wl-select2-thumbnail' style='background-image: url( <%- obj.thumbnail_url || ( 'Organization' === obj.type ? '$organization_thumbnail_url' : '$person_thumbnail_url' ) %> );'>&nbsp;</span><span class='wl-select2'><%- obj.text %></span><span class='wl-select2-type'><%- obj.type %></span></div>",
					// The HTML template for the selected option.
					'wl-select2-template-selection' => "<div class='wl-select2-selection'><span class='wl-select2-thumbnail' style='background-image: url( <%- obj.thumbnail_url || ( 'Organization' === obj.type ? '$organization_thumbnail_url' : '$person_thumbnail_url' ) %> );'>&nbsp;</span><span class='wl-select2'><%- obj.text %></span><span class='wl-select2-type'><%- obj.type %></span></div>",
				),
			)
		);

		// Finally return the element instance.
		return $this;
	}

}
