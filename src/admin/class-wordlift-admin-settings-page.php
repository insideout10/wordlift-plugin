<?php
/**
 * Pages: Admin Settings.
 *
 * Handles the WordLift admin settings page.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'modules/configuration/wordlift_configuration_constants.php' );
require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'modules/configuration/wordlift_configuration_settings.php' );

/**
 * Define the {@link Wordlift_Admin_Settings_Page} class.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */
class Wordlift_Admin_Settings_Page {

	/**
	 * The maximum number of entities to be displayed in a "simple" publisher
	 * select without a search box.
	 *
	 * @since    3.11
	 * @access   private
	 * @var      integer $max_entities_without_search The maximum number of entities
	 *  to be displayed in a "simple" publisher select without a search box.
	 */
	private $max_entities_without_search;

	/**
	 * The maximum number of entities to load when called via AJAX.
	 *
	 * @since  3.11.0
	 * @access private
	 * @var int $max_entities_without_ajax The maximum number of entities to load when called via AJAX.
	 */
	private $max_entities_without_ajax;

	/**
	 * A {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.11.0
	 * @access protected
	 * @var \Wordlift_Entity_Service $entity_service A {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * A {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.11.0
	 * @access protected
	 * @var \Wordlift_Configuration_Service $configuration_service A {@link Wordlift_Configuration_Service} instance.
	 */
	private $configuration_service;

	/**
	 * Create a {@link Wordlift_Admin_Settings_Page} instance.
	 *
	 * @since 3.11.0
	 *
	 * @param int $max_entities_without_search The maximum number of entities to be displayed in a "simple" publisher select without a search box.
	 * @param int $max_entities_without_ajax
	 * @param int $configuration_service
	 * @param int $entity_service
	 */
	function __construct( $max_entities_without_search, $max_entities_without_ajax, $configuration_service, $entity_service ) {

		$this->max_entities_without_search = $max_entities_without_search;
		$this->max_entities_without_ajax   = $max_entities_without_ajax;
		$this->configuration_service       = $configuration_service;
		$this->entity_service              = $entity_service;

	}

	/**
	 * Enqueue the scripts needed for the settings page.
	 *
	 * @since 3.11.0
	 */
	function enqueue_scripts() {

		// Enqueue the media scripts to be used for the publisher's logo selection.
		wp_enqueue_media();

		// Enqueue select2 library js and css.
		wp_enqueue_script( 'wordlift-select2', plugin_dir_url( dirname( __FILE__ ) ) . '/admin/js/select2/js/select2.min.js', array( 'jquery' ), '4.0.3' );
		wp_enqueue_style( 'wordlift-select2', plugin_dir_url( dirname( __FILE__ ) ) . '/admin/js/select2/css/select2.min.css', array(), '4.0.3' );
	}

	/**
	 * This function is called by the *wl_admin_menu* hook which is raised when WordLift builds the admin_menu.
	 *
	 * @since 3.11.0
	 *
	 * @param string $parent_slug The parent slug for the menu.
	 * @param string $capability  The required capability to access the page.
	 */
	function admin_menu( $parent_slug, $capability ) {

		// see http://codex.wordpress.org/Function_Reference/add_submenu_page
		$page = add_submenu_page(
			$parent_slug, // The parent menu slug, provided by the calling hook.
			__( 'WorldLift Settings', 'wordlift' ),  // page title
			__( 'Settings', 'wordlift' ),  // menu title
			$capability,                   // The required capability, provided by the calling hook.
			'wl_configuration_admin_menu',      // the menu slug
			array(
				$this,
				'render_page',
			) // the menu callback for displaying the page content
		);

		// Set a hook to enqueue scripts only when the settings page is displayed.
		add_action( 'admin_print_scripts-' . $page, array(
			$this,
			'enqueue_scripts',
		) );
	}

	/**
	 * Displays the settings page content.
	 *
	 * @since 3.11.0
	 */
	function render_page() {

		// Include the partial.
		include( plugin_dir_path( __FILE__ ) . 'partials/wordlift-admin-settings-page.php' );
	}


	/**
	 * Configure all the configuration parameters.
	 *
	 * Called by the *admin_init* hook.
	 *
	 * @since 3.11.0
	 */
	function admin_init() {

		// Add the settings link for the plugin on the plugin admin page.
		add_filter( 'plugin_action_links_wordlift/wordlift.php', array(
			$this,
			'settings_links',
		) );

		// Hook publisher ajax
		add_action( 'wp_ajax_wl_possible_publisher', array(
			$this,
			'possible_publisher',
		) );

		register_setting(
			'wl_general_settings',
			'wl_general_settings',
			array( $this, 'sanitize_settings' )
		);

		add_settings_section(
			'wl_general_settings_section',          // ID used to identify this section and with which to register options
			'',                                // Section header
			'',                                // Callback used to render the description of the section
			'wl_general_settings'              // Page on which to add this section of options
		);

		$key_args = array(
			'id'          => 'wl-key',
			'name'        => 'wl_general_settings[key]',
			'value'       => $this->configuration_service->get_key(),
			'description' => __( 'Insert the <a href="https://www.wordlift.io/blogger">WordLift Key</a> you received via email.', 'wordlift' ),
		);

		// set the class for the key field based on the validity of the key.
		// class should be "untouched" for an empty (virgin) value, "valid"
		// if the key is valid, or "invalid" otherwise.

		$validation_service = new Wordlift_Key_Validation_Service();
		if ( empty( $key_args['value'] ) ) {
			$key_args['class'] = 'untouched';
		} elseif ( $validation_service->is_valid( $key_args['value'] ) ) {
			$key_args['class'] = 'valid';
		} else {
			$key_args['class'] = 'invalid';
		}

		add_settings_field(
			WL_CONFIG_WORDLIFT_KEY,             // ID used to identify the field throughout the theme
			__( 'WordLift Key', 'wordlift' ),   // The label to the left of the option interface element
			array(
				$this,
				'input_box',
			),       // The name of the function responsible for rendering the option interface
			'wl_general_settings',         // The page on which this option will be displayed
			'wl_general_settings_section',      // The name of the section to which this field belongs
			$key_args                             // The array of arguments to pass to the callback. In this case, just a description.
		);

		// Entity Base Path input.

		$entity_base_path_args = array(
			// The array of arguments to pass to the callback. In this case, just a description.
			'id'          => 'wl-entity-base-path',
			'name'        => 'wl_general_settings[' . Wordlift_Configuration_Service::ENTITY_BASE_PATH_KEY . ']',
			'value'       => $this->configuration_service->get_entity_base_path(),
			'description' => __( 'All new pages created with WordLift, will be stored inside your internal vocabulary. You can customize the url pattern of these pages in the field above. Check our <a href="https://wordlift.io/wordlift-user-faqs/#10-why-and-how-should-i-customize-the-url-of-the-entity-pages-created-in-my-vocabulary">FAQs</a> if you need more info.', 'wordlift' ),
		);

		if ( $this->entity_service->count() ) {
			// Mark the field readonly, the value can be anything.
			$entity_base_path_args['readonly'] = '';
		}

		add_settings_field(
			Wordlift_Configuration_Service::ENTITY_BASE_PATH_KEY,             // ID used to identify the field throughout the theme
			__( 'Entity Base Path', 'wordlift' ),   // The label to the left of the option interface element
			array(
				$this,
				'input_box',
			),       // The name of the function responsible for rendering the option interface
			'wl_general_settings',         // The page on which this option will be displayed
			'wl_general_settings_section',      // The name of the section to which this field belongs
			$entity_base_path_args
		);

		// Site Language input.

		add_settings_field(
			WL_CONFIG_SITE_LANGUAGE_NAME,
			__( 'Site Language', 'wordlift' ),
			array( $this, 'select_box' ),
			'wl_general_settings',
			'wl_general_settings_section',
			array(
				// The array of arguments to pass to the callback. In this case, just a description.
				'id'          => 'wl-site-language',
				'name'        => 'wl_general_settings[site_language]',
				'value'       => $this->configuration_service->get_language_code(),
				'description' => __( 'Each WordLift Key can be used only in one language. Pick yours.', 'wordlift' ),
			)
		);

		add_settings_field(
			'wl_publisher',
			__( 'Publisher', 'wordlift' ),
			array( $this, 'publisher_section' ),
			'wl_general_settings',
			'wl_general_settings_section'
		);

	}

	/**
	 * Sanitize the configuration settings to be stored. Configured as a hook from *wl_configuration_settings*.
	 *
	 * If a new entity is being created for the publisher, create it and set The
	 * publisher setting.
	 *
	 * @since 3.11.0
	 *
	 * @param array $input The configuration settings array.
	 *
	 * @return mixed
	 */
	function sanitize_settings( $input ) {

		$input = apply_filters( 'wl_configuration_sanitize_settings', $input, $input );

		// If the user creates a new publisher entities the information is not part of the
		// "option" itself and need to get it from other $_POST values.
		if ( isset( $_POST['wl-setting-panel'] ) && ( 'wl-create-entity' == $_POST['wl-setting-panel'] ) ) {

			// validate publisher type
			if ( ! isset( $_POST['wl-publisher-type'] ) || ! in_array( $_POST['wl-publisher-type'], array(
					'person',
					'company',
				) )
			) {
				return $input;
			}

			// Set the type URI, either http://schema.org/Person or http://schema.org/Organization.
			$type_uri = sprintf( 'http://schema.org/%s', 'company' === $_POST['wl-publisher-type'] ? 'Organization' : 'Person' );

			// validate publisher logo
			if ( 'company' === $_POST['wl-publisher-type'] ) {
				if ( ! isset( $_POST['wl-publisher-logo-id'] ) || ! is_numeric( $_POST['wl-publisher-logo-id'] ) ) {
					return $input;
				}

				$logo = intval( $_POST['wl-publisher-logo-id'] );
			} else {
				$logo = 0;
			}

			// Create an entity for the publisher.
			$publisher_post_id = $this->entity_service->create( $_POST['wl-publisher-name'], $type_uri, $logo, 'publish' );

			$input[ Wordlift_Configuration_Service::PUBLISHER_ID ] = $publisher_post_id;
		}

		return $input;

	}

	/**
	 * Draw an input text with the provided parameters.
	 *
	 * @since 3.11.0
	 *
	 * @param array $args An array of configuration parameters.
	 */
	function input_box( $args ) {
		?>
		<input type="text" id="<?php echo esc_attr( $args['id'] ); ?>"
		       name="<?php echo esc_attr( $args['name'] ); ?>"
		       value="<?php echo esc_attr( $args['value'] ); ?>"
		       <?php if ( isset( $args['readonly'] ) ) { ?>readonly<?php } ?>
			<?php if ( isset( $args['class'] ) ) {
				echo 'class="' . esc_attr( $args['class'] ) . '"';
			} ?>
		/>

		<?php
		if ( isset( $args['description'] ) ) {
			?>
			<p><?php echo $args['description']; ?></p>
			<?php
		}

	}

	/**
	 * Display a select.
	 *
	 * @deprecated only used by the languages select.
	 *
	 * @see        https://github.com/insideout10/wordlift-plugin/issues/349
	 *
	 * @since      3.11.0
	 *
	 * @param array $args The select configuration parameters.
	 */
	function select_box( $args ) {
		?>

		<select id="<?php echo esc_attr( $args['id'] ); ?>"
		        name="<?php echo esc_attr( $args['name'] ); ?>">
			<?php
			// Print all the supported language, preselecting the one configured in WP (or English if not supported).
			// We now use the `Wordlift_Languages` class which provides the list of languages supported by WordLift.
			// See https://github.com/insideout10/wordlift-plugin/issues/349

			// Get WordLift's supported languages.
			$languages = Wordlift_Languages::get_languages();

			// If we support WP's configured language, then use that, otherwise use English by default.
			$language = isset( $languages[ $args['value'] ] ) ? $args['value'] : 'en';

			foreach ( $languages as $code => $label ) { ?>
				<option
					value="<?php echo esc_attr( $code ) ?>" <?php echo selected( $code, $language, false ) ?>><?php echo esc_html( $label ) ?></option>
			<?php } ?>
		</select>

		<?php
		if ( isset( $args['description'] ) ) {
			?>
			<p><?php echo $args['description']; ?></p>
			<?php
		}
	}

	/**
	 * Display publisher selection/creation settings.
	 *
	 * @since 3.11.0
	 *
	 */
	function publisher_section() {

		// Include the partial.
		include( plugin_dir_path( __FILE__ ) . 'partials/wordlift-admin-settings-page-publisher-section.php' );

	}

	/**
	 * Create a link to WordLift settings page.
	 *
	 * @since 3.0.0
	 *
	 * @param array $links An array of links.
	 *
	 * @return array An array of links including those added by the plugin.
	 */
	function settings_links( $links ) {

		// TODO: this link is different within SEO Ultimate.
		array_push( $links, '<a href="' . get_admin_url( null, 'admin.php?page=wl_configuration_admin_menu' ) . '">Settings</a>' );

		return $links;
	}

	/**
	 * Intercept the change of the WordLift key in order to set the dataset URI.
	 *
	 * @since 3.0.0
	 *
	 * @param array $old_value The old settings.
	 * @param array $new_value The new settings.
	 */
	function update_key( $old_value, $new_value ) {

		// Check the old key value and the new one. We're going to ask for the dataset URI only if the key has changed.
		$old_key = isset( $old_value['key'] ) ? $old_value['key'] : '';
		$new_key = isset( $new_value['key'] ) ? $new_value['key'] : '';

		// If the key hasn't changed, don't do anything.
		// WARN The 'update_option' hook is fired only if the new and old value are not equal
		if ( $old_key === $new_key ) {
			return;
		}

		// If the key is empty, empty the dataset URI.
		if ( '' === $new_key ) {
			$this->configuration_service->set_dataset_uri( '' );
		}

		// Request the dataset URI.
		$response = wp_remote_get( wl_configuration_get_accounts_by_key_dataset_uri( $new_key ), unserialize( WL_REDLINK_API_HTTP_OPTIONS ) );

		// If the response is valid, then set the value.
		if ( ! is_wp_error( $response ) && 200 === (int) $response['response']['code'] ) {

			$this->configuration_service->set_dataset_uri( $response['body'] );

		} else {
			// TO DO User notification is needed here.
		}

	}

	/**
	 * Search SQL filter for matching against post title only.
	 *
	 * Adapted from
	 *
	 * @link    http://wordpress.stackexchange.com/a/11826/1685
	 *
	 * @since   3.11.0
	 *
	 * @param   string   $search   The search string.
	 * @param   WP_Query $wp_query The WP-Query in the context of which the search is done.
	 */
	function search_by_title( $search, $wp_query ) {
		if ( ! empty( $search ) && ! empty( $wp_query->query_vars['search_terms'] ) ) {
			global $wpdb;

			$q = $wp_query->query_vars;
			$n = ! empty( $q['exact'] ) ? '' : '%';

			$search = array();

			foreach ( (array) $q['search_terms'] as $term ) {
				$search[] = $wpdb->prepare( "$wpdb->posts.post_title LIKE %s", $n . $wpdb->esc_like( $term ) . $n );
			}

			$search = ' AND ' . implode( ' AND ', $search );
		}

		return $search;
	}

	/**
	 * Handle the AJAX request coming from the publisher selection AJAX
	 * on the setting screen.
	 *
	 * The parameters in the POST request are
	 *   q - The string to search for in the title of the person or organizations
	 *       entity.
	 *
	 * As a result output the HTML select element containing the titles of the entities
	 * as labels, and there "post id" as values.
	 *
	 */
	function possible_publisher() {

		// No actual search parameter was passed, bail out.
		if ( ! isset( $_POST['q'] ) ) {
			wp_die();

			return;
		}

		add_filter( 'posts_search', array( $this, 'search_by_title' ), 10, 2 );

		$entities_query = new WP_Query( array(
			'post_type'      => Wordlift_Entity_Service::TYPE_NAME,
			'posts_per_page' => - 1,
			's'              => $_POST['q'],
			'tax_query'      => array(
				'relation' => 'OR',
				array(
					'taxonomy' => Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME,
					'field'    => 'name',
					'terms'    => 'Person',
				),
				array(
					'taxonomy' => Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME,
					'field'    => 'name',
					'terms'    => 'Organization',
				),
			),
		) );

		$response = array();

		while ( $entities_query->have_posts() ) {
			$entities_query->the_post();

			/*
			 * Get the thumbnail, the long way around instead of get_the_thumbnail_url
			 * because it is supported only from version 4.4.
			 */

			$thumb             = '';
			$post_thumbnail_id = get_post_thumbnail_id();
			if ( $post_thumbnail_id ) {
				$thumb = wp_get_attachment_image_url( $post_thumbnail_id, 'thumbnail' );
			}

			// get the type of entity.

			$terms = get_the_terms( get_the_ID(), Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );

			$entity_type = __( 'Person', 'wordlift' );
			if ( 'Organization' == $terms[0]->name ) {
				$entity_type = __( 'Company', 'wordlift' );
			}

			$entity_data = array(
				'id'       => get_the_ID(),
				'text'     => get_the_title(),
				'thumburl' => $thumb,
				'type'     => $entity_type,
			);

			$response[] = $entity_data;
		}

		wp_send_json( $response );
	}

}
