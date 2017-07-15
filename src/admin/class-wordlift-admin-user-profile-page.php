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
	 * The {@link Wordlift_Admin_Person_Element} Wordlift_Admin_Person_Element instance.
	 *
	 * @since 3.14.0
	 *
	 * @var \Wordlift_Admin_Person_Element $plugin The person entity
	 *                selection element rendering the possible persons.
	 */
	private $person_element;

	/**
	 * Create the {@link Wordlift_Admin_User_Profile_Page} instance.
	 *
	 * @since 3.14.0
	 *
	 * @param \Wordlift_Admin_Person_Element $person_element The person entity selection
	 *                                                      element rendering the possible persons.
	 */
	function __construct( $person_element ) {

		/*
		 * When an admin (or similar permissions) edits his own profile a
		 * different action than the usual is being triggered.
		 * It is too early in the wordpress boot to do user capabilities filtering
		 * here and it is deferred to the handler.
		 */
		add_action( 'show_user_profile', array( $this, 'edit_user_profile' ) );
		add_action( 'edit_user_profile', array( $this, 'edit_user_profile' ) );
		add_action( 'edit_user_profile_update', array(
			$this,
			'edit_user_profile_update',
		) );

		$this->person_element = $person_element;
	}

	/**
	 * Add a WordLift section in the user profile which lets
	 * the admin to associate a wordpress user with a person entity.
	 *
	 * @since 3.14.0
	 *
	 * @param WP_User $user The current WP_User object of the user being edited.
	 */
	public function edit_user_profile( $user ) {

		// In case it is a user editing his own profile, make sure he has admin
		// like capabilities.
		if ( ! current_user_can( 'edit_users' ) ) {
			return;
		}
		?>
		<h2><?php esc_html_e( 'Wordlift', 'wordlift' ); ?></h2>

		<table class="form-table">
			<tr class="user-description-wrap">
				<th><label
						for="wl_person"><?php _e( 'Schema.org Publisher', 'wordlift' ); ?></label>
				</th>
				<td>
					<?php
					$this->person_element->render( array(
						'id'             => 'wl_person',
						'name'           => 'wl_person',
						'current_entity' => get_user_meta( $user->ID, 'wl_person', true ),
					) );
					?>
					<p class="description"><?php _e( 'The Publisher entity to associate with this user.', 'wordlift' ); ?></p>
				</td>
			</tr>
			<?php if ( in_array( 'editor', (array) $user->roles ) ) { ?>
				<tr>
					<th>
						<label for="wl_can_edit_entities"><?php esc_html_e( 'Can edit entities', 'wordlift' )?></label>
					</th>
					<td>
						<input id="wl_can_edit_entities" name="wl_can_edit_entities" type="checkbox" <?php checked( Wordlift_User_Service::get_instance()->editor_can_edit_entities( $user->ID ) ) ?>
					</td>
			<?php } ?>
		</table>
		<?php
	}

	/**
	 * Handle storing the person entity associated with the user.
	 *
	 * @since 3.14.0
	 *
	 * @param int $user_id The user id of the user being saved.
	 */
	public function edit_user_profile_update( $user_id ) {

		// In case it is a user editing his own profile, make sure he has admin
		// like capabilities.
		if ( ! current_user_can( 'edit_users' ) ) {
			return;
		}

		// Update the entity id in the user meta
		if ( isset( $_POST['wl_person'] ) ) {
			update_user_meta( $user_id, 'wl_person', intval( $_POST['wl_person'] ) );
		}

		// Deny and enable the edit entity capability
		if ( isset( $_POST['wl_can_edit_entities'] ) ) {
			// User has capability so remove the deny indication if present.
			Wordlift_User_Service::get_instance()->enable_editor_entity_editing( $user_id );
		} else {
			Wordlift_User_Service::get_instance()->deny_editor_entity_editing( $user_id );
		}

	}

}
