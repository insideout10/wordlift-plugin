<?php
/**
 * Pages: User Profile Edit Page.
 *
 * A 'ghost' page which loads additional scripts and style for the post edit page.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the {@link Wordlift_Admin_User_Profile_Page} page.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */
class Wordlift_Admin_User_Profile_Page {

	/**
	 * The {@link Wordlift_Admin_Person_Element} instance.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift_Admin_Author_Element $plugin The person entity
	 *                selection element rendering the possible persons.
	 */
	private $author_element;

	/**
	 * The {@link Wordlift_User_Service} instance.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift_User_Service $user_service The {@link Wordlift_User_Service} instance.
	 */
	private $user_service;

	/**
	 * Create the {@link Wordlift_Admin_User_Profile_Page} instance.
	 *
	 * @param \Wordlift_Admin_Author_Element $author_element The person entity selection
	 *                                                       element rendering the possible persons.
	 * @param \Wordlift_User_Service         $user_service The {@link Wordlift_User_Service} instance.
	 *
	 * @since 3.14.0
	 */
	public function __construct( $author_element, $user_service ) {

		$this->author_element = $author_element;
		$this->user_service   = $user_service;

		/*
		 * When an admin (or similar permissions) edits his own profile a
		 * different action than the usual is being triggered.
		 * It is too early in the WordPress boot to do user capabilities filtering
		 * here and it is deferred to the handler.
		 */
		add_action( 'show_user_profile', array( $this, 'edit_user_profile' ) );
		add_action( 'edit_user_profile', array( $this, 'edit_user_profile' ) );
		add_action(
			'edit_user_profile_update',
			array(
				$this,
				'edit_user_profile_update',
			)
		);
		add_action(
			'personal_options_update',
			array(
				$this,
				'edit_user_profile_update',
			)
		);

	}

	/**
	 * Add a WordLift section in the user profile which lets
	 * the admin to associate a WordPress user with a person entity.
	 *
	 * @param WP_User $user The current WP_User object of the user being edited.
	 *
	 * @since 3.14.0
	 */
	public function edit_user_profile( $user ) {

		// In case it is a user editing his own profile, make sure he has admin
		// like capabilities.
		if ( ! current_user_can( 'edit_users' ) ) {
			return;
		}

		?>
		<h2><?php esc_html_e( 'WordLift', 'wordlift' ); ?></h2>
		<?php wp_nonce_field( 'wordlift_user_save', 'wordlift_user_save_nonce', false ); ?>
		<table class="form-table">
			<?php
			// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
			if ( apply_filters( 'wl_feature__enable__user-author', true ) ) {
				?>
				<tr class="user-description-wrap">
					<th><label
								for="wl_person"><?php esc_html_e( 'Author from the vocabulary', 'wordlift' ); ?></label>
					</th>
					<td>
						<?php
						$this->author_element->render(
							array(
								'id'             => 'wl_person',
								'name'           => 'wl_person',
								'current_entity' => $this->user_service->get_entity( $user->ID ),
							)
						);
						?>
						<p class="description"><?php esc_html_e( 'The entity, person or organization, from the vocabulary to associate with this author.', 'wordlift' ); ?></p>
					</td>
				</tr>
			<?php } ?>
			<?php if ( $this->user_service->is_editor( $user->ID ) ) { ?>
			<tr>
				<th>
					<label
							for="wl_can_create_entities"><?php esc_html_e( 'Can create new entities', 'wordlift' ); ?></label>
				</th>
				<td>
					<input id="wl_can_create_entities"
						   name="wl_can_create_entities"
						   type="checkbox" <?php checked( $this->user_service->editor_can_create_entities( $user->ID ) ); ?>
				</td>
				<?php } ?>
				<?php
				/**
				 * Action name: wordlift_user_settings_page
				 * An action to render the wordlift user settings.
				 *
				 * @since 3.30.0
				 */
				do_action( 'wordlift_user_settings_page' );
				?>
		</table>
		<?php
	}

	/**
	 * Handle storing the person entity associated with the user.
	 *
	 * @param int $user_id The user id of the user being saved.
	 *
	 * @since 3.14.0
	 */
	public function edit_user_profile_update( $user_id ) {

		// In case it is a user editing his own profile, make sure he has admin
		// like capabilities.
		if ( ! current_user_can( 'edit_users' ) ) {
			return;
		}

		check_admin_referer( 'wordlift_user_save', 'wordlift_user_save_nonce' );

		// Link an entity to the user.
		$this->link_entity( $user_id, $_POST );

		// Deny and enable the edit entity capability
		if ( isset( $_POST['wl_can_create_entities'] ) ) {
			// User has capability so remove the deny indication if present.
			$this->user_service->allow_editor_entity_create( $user_id );
		} else {
			$this->user_service->deny_editor_entity_create( $user_id );
		}

	}

	/**
	 * Link an entity (specified in the `$_POST` array) to the {@link WP_User}
	 * with the specified `id`.
	 *
	 * @param int   $user_id The {@link WP_User} `id`.
	 * @param array $post The `$_POST` array.
	 *
	 * @since 3.14.0
	 */
	private function link_entity( $user_id, $post ) {

		// Bail out if the `wl_person` parameter isn't set.
		if ( ! isset( $post['wl_person'] ) || ! is_numeric( $post['wl_person'] ) ) {
			return;
		}

		$this->user_service->set_entity( $user_id, intval( $post['wl_person'] ) );

	}

}
