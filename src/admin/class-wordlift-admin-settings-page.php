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
	 * The maximal number of entities to be displayed in a "simple"
	 * publisher select without a search box.
	 *
	 * @since 3.11
	 */
	const MAX_ENTITIES_WITHOUT_SEARCH = 10;

	/**
	 * The maximal number of entities to be displayed in a "simple"
	 * publisher select. If there are more entities than this, AJAX
	 * should be used.
	 *
	 * @since 3.11
	 */
	const MAX_ENTITIES_WITHOUT_AJAX = 200;

	/**
	 * Enqueue the scripts needed for the settings page.
	 *
	 * @since 3.11
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
	 * @since 3.0.0
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

		// Set a hook to enqueue scripts only when the settings page is displayed
		add_action( 'admin_print_scripts-' . $page, array(
			$this,
			'enqueue_scripts',
		) );
	}

	/**
	 * Displays the settings page content.
	 *
	 * @since 3.0.0
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
	 * @since 3.0.0
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
			'value'       => wl_configuration_get_key(),
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
			'value'       => Wordlift_Configuration_Service::get_instance()
			                                               ->get_entity_base_path(),
			'description' => __( 'All new pages created with WordLift, will be stored inside your internal vocabulary. You can customize the url pattern of these pages in the field above. Check our <a href="https://wordlift.io/wordlift-user-faqs/#10-why-and-how-should-i-customize-the-url-of-the-entity-pages-created-in-my-vocabulary">FAQs</a> if you need more info.', 'wordlift' ),
		);

		if ( Wordlift_Entity_Service::get_instance()->count() ) {
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
				'value'       => wl_configuration_get_site_language(),
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
	 * @since 3.0.0
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
			$publisher_post_id = Wordlift_Entity_Service::get_instance()->create( $_POST['wl-publisher-name'], $type_uri, $logo, 'publish' );

			$input[ Wordlift_Configuration_Service::PUBLISHER_ID ] = $publisher_post_id;
		}

		return $input;

	}

	/**
	 * Draw an input text with the provided parameters.
	 *
	 * @since 3.0.0
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
	 * @since      3.0.0
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

		// get all the organizations and persons that might be used as publishers.

		$entities_query = new WP_Query( array(
			'post_type'      => Wordlift_Entity_Service::TYPE_NAME,
			'posts_per_page' => self::MAX_ENTITIES_WITHOUT_AJAX,
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

		// Variable indicating should the select tab (and panel) be displayed.
		// If the wizard is skipped during the install there might not be entities
		// to select from and not point in shoing the tab and panel

		$select_panel_displayed = $entities_query->have_posts();

		?>
		<style>
			#wl-key {
				padding-right: 34px;
				background-repeat: no-repeat;
				background-position: calc(100% - 8px) 7px;
			}

			#wl-key.invalid {
				background-image: url("<?php echo dirname( plugin_dir_url( __FILE__ ) )?>/images/invalid.png");
			}

			#wl-key.valid {
				background-image: url("<?php echo dirname( plugin_dir_url( __FILE__ ) )?>/images/valid.png");
			}

			#wl-key.untouched {
				background-image: initial;
			}

			.wl-tab-panel {
				display: none;
				padding-top: 10px;
			}

			.wl-select-entity-active #wl-select-entity-panel {
				display: block;
			}

			.wl-create-entity-active #wl-create-entity-panel {
				display: block;
			}

			.wl-select-entity-active select {
				width: 400px;
			}

			.wl-select2 {
				font-size: 12px;
				color: #1980c0;
			}

			.wl-select2 img, .wl-select2 .img-filler {
				height: 24px;
				width: 24px;
				margin-right: 10px;
				margin-left: 10px;
				vertical-align: middle;
				display: inline-block;
			}

			.wl-select2-type {
				float: right;
				margin-right: 10px;
				text-align: right;
			}

			#wl-publisher-type span {
				margin-right: 20px;
			}

			#wl-publisher-logo {
				display: none;
			}

			#wl-publisher-logo input {
				color: #555;
				border-color: #ccc;
				background: #f7f7f7;
				box-shadow: 0 1px 0 #ccc;
			}

			#wl-publisher-name input {
				width: 400px;
			}

			#wl-publisher-logo-preview {
				width: 24px;
				height: 24px;
				vertical-align: middle;
				margin-right: 10px;
				display: none;
			}

		</style>

		<input type="hidden"
		       id="wl-setting-panel"
		       autocomplete="off"
		       name="wl-setting-panel"
		       value="<?php echo $select_panel_displayed ? 'wl-select-entity' : 'wl-create-entity' ?>">
		<input type="hidden" id="wl-publisher-logo-id"
		       name="wl-publisher-logo-id" autocomplete="off">

		<div id="wl-publisher-section"
		     class="<?php echo $select_panel_displayed ? 'wl-select-entity-active' : 'wl-create-entity-active' ?>"
		     data-tabing-enabled="<?php echo $select_panel_displayed ? 'yes' : 'no' ?>">
			<div class="nav-tab-wrapper">
				<a class="nav-tab <?php echo $select_panel_displayed ? 'nav-tab-active' : '' ?>"
				   data-panel="wl-select-entity"
				   href="#"><?php _e( 'Select existing publisher', 'wordlift' ) ?></a>
				<a class="nav-tab <?php echo $select_panel_displayed ? '' : 'nav-tab-active' ?>"
				   data-panel="wl-create-entity"
				   href="#"><?php _e( 'Create new publisher', 'wordlift' ) ?></a>
			</div>
			<div id="wl-select-entity-panel" class="wl-tab-panel">
				<?php
				// populate the select only if there are less than WL_MAX_ENTITIES_WITHOUT_AJAX possible entities
				// Otherwise use AJAX..

				$ajax_params = ( $entities_query->found_posts <= self::MAX_ENTITIES_WITHOUT_AJAX ) ? '' : ' data-ajax--url="' . parse_url( self_admin_url( 'admin-ajax.php' ), PHP_URL_PATH ) . '/action=wl_possible_publisher" data-ajax--cache="true" ';

				// show the search box only if there are more entiyies than WL_MAX_ENTITIES_WITHOUT_SEARCH.
				$disable_search_params = ( $entities_query->found_posts > self::MAX_ENTITIES_WITHOUT_SEARCH ) ? '' : ' data-nosearch="true" ';
				?>
				<select id="wl-select-entity"
				        name="wl_general_settings[<?php echo Wordlift_Configuration_Service::PUBLISHER_ID ?>]"
					<?php echo $ajax_params ?>
					<?php echo $disable_search_params ?>
					    autocomplete="off">
					<?php

					if ( $entities_query->post_count < self::MAX_ENTITIES_WITHOUT_AJAX ) {
						while ( $entities_query->have_posts() ) {
							$entities_query->the_post();

							// get the thumbnail, the long way around instead of get_the_thumbnail_url
							// because it is supported only from version 4.4

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

							echo '<option value="' . get_the_ID() . '" ' . selected( Wordlift_Configuration_Service::get_instance()->get_publisher_id(), get_the_ID(), false ) . ' data-thumb="' . esc_attr( $thumb ) . '" data-type="' . esc_attr( $entity_type ) . '">' . get_the_title() . '</option>';
						}
					} else {
						// display only the currently selected publisher

						$post_id = Wordlift_Configuration_Service::get_instance()->get_publisher_id();
						$post    = get_post( $post_id );

						$thumb             = '';
						$post_thumbnail_id = get_post_thumbnail_id( $post_id );
						if ( $post_thumbnail_id ) {
							$thumb = wp_get_attachment_image_url( $post_thumbnail_id, 'thumbnail' );
						}

						// get the type of entity.

						$terms = get_the_terms( $post_id, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );

						$entity_type = __( 'Person', 'wordlift' );
						if ( 'Organization' == $terms[0]->name ) {
							$entity_type = __( 'Company', 'wordlift' );
						}

						echo '<option value="' . $post_id . '" selected="selected"' . ' data-thumb="' . esc_attr( $thumb ) . '" data-type="' . esc_attr( $entity_type ) . '">' . get_the_title( $post_id ) . '</option>';
					}
					?>
				</select>
			</div>
			<div id="wl-create-entity-panel" class="wl-tab-panel">
				<p>
					<b><?php esc_html_e( 'Are you publishing as an individual or as a company?', 'wordlift' ) ?></b>
				</p>
				<p id="wl-publisher-type">
					<span>
						<input id="wl-publisher-person" type="radio"
						       name="wl-publisher-type" value="person"
						       checked="checcked" autocomplete="off">
						<label
							for="wl-publisher-person"><?php esc_html_e( 'Person', 'wordlift' ) ?></label>
					</span>
					<span>
						<input id="wl-publisher-company" type="radio"
						       name="wl-publisher-type" value="company"
						       autocomplete="off">
						<label
							for="wl-publisher-company"><?php esc_html_e( 'Company', 'wordlift' ) ?></label>
					</span>
				</p>
				<p id="wl-publisher-name">
					<input type="text"
					       placeholder="<?php esc_attr_e( "Publisher's Name", 'wordlift' ) ?>"
					       name="wl-publisher-name">
				</p>
				<div id="wl-publisher-logo">
					<p>
						<b><?php esc_html_e( "Choose the publisher's Logo", 'wordlift' ) ?></b>
					</p>
					<p>
						<img id="wl-publisher-logo-preview"><input type="button"
						                                           class="button"
						                                           value="<?php esc_attr_e( 'Select an existing image or upload a new one', 'wordlift' ); ?>">
					</p>
				</div>
			</div>
		</div>

		<?php
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

		// wl_write_log( "Going to request set redlink dataset uri if needed" );

		// Check the old key value and the new one. We're going to ask for the dataset URI only if the key has changed.
		$old_key = isset( $old_value['key'] ) ? $old_value['key'] : '';
		$new_key = isset( $new_value['key'] ) ? $new_value['key'] : '';

		// wl_write_log( "[ old value :: $old_key ][ new value :: $new_key ]" );

		// If the key hasn't changed, don't do anything.
		// WARN The 'update_option' hook is fired only if the new and old value are not equal
		if ( $old_key === $new_key ) {
			return;
		}

		// If the key is empty, empty the dataset URI.
		if ( '' === $new_key ) {
			wl_configuration_set_redlink_dataset_uri( '' );
		}

		// Request the dataset URI.
		$response = wp_remote_get( wl_configuration_get_accounts_by_key_dataset_uri( $new_key ), unserialize( WL_REDLINK_API_HTTP_OPTIONS ) );

		// If the response is valid, then set the value.
		if ( ! is_wp_error( $response ) && 200 === (int) $response['response']['code'] ) {

			// wl_write_log( "[ Retrieved dataset :: " . $response['body'] . " ]" );
			wl_configuration_set_redlink_dataset_uri( $response['body'] );

		} else {
			wl_write_log( 'Error on dataset uri remote retrieving [ ' . var_export( $response, true ) . ' ]' );
		}

	}

	/**
	 * Search SQL filter for matching against post title only.
	 *
	 * Adapted from
	 *
	 * @link    http://wordpress.stackexchange.com/a/11826/1685
	 *
	 * @since   3.11
	 *
	 * @param   string   $search
	 * @param   WP_Query $wp_query
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
	 * as labels, and there "post id" as values
	 *
	 */
	function possible_publisher() {

		// no actual search parameter was passed, bail out
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

			// get the thumbnail, the long way around instead of get_the_thumbnail_url
			// because it is supported only from version 4.4

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
