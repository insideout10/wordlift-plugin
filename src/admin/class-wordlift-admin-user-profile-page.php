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
	 * Create the {@link Wordlift_Admin_User_Profile_Page} instance.
	 *
	 * @since 3.12.0
	 *
	 * @param \Wordlift $plugin The {@link Wordlift} plugin instance.
	 */
	function __construct( $plugin ) {

		add_action( 'edit_user_profile', array( $this, 'edit_user_profile' ) );
		$this->plugin = $plugin;
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
	?>
		<h2><?php esc_html_e( 'Wordlift', 'wordlift' ); ?></h2>

		<table class="form-table">
		<tr class="user-description-wrap">
			<th><label for="wl_person"><?php _e( 'Schema.org Person', 'wordlift' ); ?></label></th>
			<td><textarea name="wl_person" id="wl_person" rows="5" cols="30"><?php echo $profileuser->description; // textarea_escaped ?></textarea>
			<p class="description"><?php _e( 'The Person entity to associate with this user.' ); ?></p></td>
		</tr>
		</table>
	<?php
	}

}
