<?php
/**
 * Pages: User Profile Edit Page.
 *
 * A 'ghost' page which loads additional scripts and style for the post edit page.
 *
 * @since      3.12.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the {@link Wordlift_Admin_User_Profile_Page} page.
 *
 * @since      3.12.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */
class Wordlift_Admin_User_Profile_Page {

	/**
	 * The {@link Wordlift} plugin instance.
	 *
	 * @since 3.11.0
	 *
	 * @var \Wordlift $plugin The {@link Wordlift} plugin instance.
	 */
	private $plugin;

	/**
	 * The {@link Wordlift} plugin instance.
	 *
	 * @since 3.11.0
	 *
	 * @var \Wordlift_Admin_Person_Element $plugin The person entity
	 * 				selecttion element rendering the possible persons.
	 */
	private $person_element;

	/**
	 * Create the {@link Wordlift_Admin_User_Profile_Page} instance.
	 *
	 * @since 3.12.0
	 *
	 * @param \Wordlift $plugin The {@link Wordlift} plugin instance.
	 * @param Wordlift_Admin_Person_Element	$person_element The person entity
	 * 				selecttion element rendering the possible persons.
	 */
	function __construct( $plugin, $person_element ) {

		/*
		 * When an admin (or similar permissions) edits his own profile a
		 * different action than the usual is being triggered.
		 * It is too early in the wordpress boot to do user capabilities filtering
		 * here and it is defered to the handler.
		 */
		add_action( 'show_user_profile', array( $this, 'edit_user_profile' ) );
		add_action( 'edit_user_profile', array( $this, 'edit_user_profile' ) );
		$this->plugin = $plugin;
		$this->person_element = $person_element;
	}

	/**
	 * Enqueue scripts and styles for the edit page.
	 *
	 * @since 3.12.0
	 */
	public function enqueue_scripts() {

		// Enqueue the edit screen JavaScript. The `wordlift-admin.bundle.js` file
		// is scheduled to replace the older `wordlift-admin.min.js` once client-side
		// code is properly refactored.
		wp_enqueue_script(
			'wordlift-admin-edit-page', plugin_dir_url( __FILE__ ) . 'js/wordlift-admin-edit-page.bundle.js',
			array( $this->plugin->get_plugin_name() ),
			$this->plugin->get_version(),
			false
		);

	}

	/**
	 * Add a WordLift section in the user profile which lets
	 * the admin to associate a wordpress user with a person entity.
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
			<th><label for="wl_person"><?php _e( 'Schema.org Person', 'wordlift' ); ?></label></th>
			<td><?php $this->person_element->render();?>
			<p class="description"><?php _e( 'The Person entity to associate with this user.' ); ?></p></td>
		</tr>
		</table>
	<?php
	}

}
