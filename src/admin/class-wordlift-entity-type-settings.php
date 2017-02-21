<?php
/**
 * Admin UI: Wordlift_Admin_Entity_Type_settings
 *
 * The {@link Wordlift_Admin_Entity_Type_settings} class handles modifications
 * to the entity type list admin page
 *
 * @link       https://wordlift.io
 *
 * @package    Wordlift
 * @subpackage Wordlift/admin
 * @since      3.11.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Entity taxonomy list admin page controller.
 *
 * Methods to manipulate whatever is displayed on the admin list page
 * for the entity taxonomy
 *
 * @package    Wordlift
 * @subpackage Wordlift/admin
 * @author     WordLift <hello@wordlift.io>
 */
class Wordlift_Admin_Entity_Type_Settings {

	/**
	 * Handle menu registration
	 *
	 * The registration is required, although we do not want to actually to add
	 * an item to the menu, in order to "whitelist" the access to the settings page in
	 * the admin.
	 *
	 * @since 3.11.0
	 */
	public function admin_menu() {

		/*
		 * Before anything else check if an settings form was submitted.
		 * This has to be done before any output happens in order to be able to
		 * display proper "die" error messages and redirect.
		 */

		if ( isset( $_GET['page'] ) && ( 'wl_entity_type_settings' == $_GET['page'] ) ) {

			// Validate inputs. Do not return on invalid parameters or capabilities.
			$this->validate_proper_term();

			// If proper form submission, handle it and redirect back to the settings page.
			if ( isset( $_POST['action'] ) && ( 'wl_edit_entity_type_term' == $_POST['action'] ) ) {
				$this->handle_form_submission();
			}

			// Register admin notices handler.
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );
		}

		/*
		 * Use a null parent slug to prevent the menu from actually appearing
		 * in the admin menu.
		 */

		add_submenu_page(
			null,
			__( 'Edit Entity term', 'wordlift' ),
			__( 'Edit Entity term', 'wordlift' ),
			'manage_options',
			'wl_entity_type_settings',
			array( $this, 'settings_page' )
		);
	}

	/**
	 * Output admin notices if needed, based on the message url parameter.
	 * A value of 1 indicates that a successful save was done.
	 *
	 * @since 3.11.0
	 */
	function admin_notice() {
		if ( isset( $_GET['message'] ) && ('1' == $_GET['message'] ) ) {
	?>
		<div class="notice notice-success is-dismissible">
			<p><?php _e( 'Settings saved', 'wordlift' )?></p>
		</div>
	<?php
		}
	}

	/**
	 * Validate the existence of the the entity type indicated by the tag_ID
	 * url parameter before doing any processing. Done before any output to mimic
	 * the way WordPress handles same situation with "normal" term editing screens.
	 *
	 * @since 3.11.0
	 */
	function validate_proper_term() {

		// Validate capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die(
				'<h1>' . __( 'Cheatin&#8217; uh?' ) . '</h1>' .
				'<p>' . __( 'Sorry, you are not allowed to edit this item.' ) . '</p>',
				403
			);
		}

		$term_id = (int) $_REQUEST['tag_ID'];
		$term    = get_term( $term_id, 'wl_entity_type' );

		if ( ! $term instanceof WP_Term ) {
			wp_die( __( 'You attempted to edit an entity type term that doesn&#8217;t exist.', 'wordlift' ) );
		}

	}

	/**
	 * Handle the form submission of the settings form. On successful
	 * handling redirect tp the setting edit page.
	 *
	 * @since 3.11.0
	 */
	function handle_form_submission() {

		$term_id = (int) $_POST['tag_ID'];
		check_admin_referer( 'update-entity_type_term_' . $term_id );

		$term    = get_term( $term_id, 'wl_entity_type' );

		$settings = get_option( 'wl_entity_type_settings', array() );
		$settings[ $term_id ] = array(
			'title' => trim( wp_unslash( $_POST['title'] ) ),
			'description' => wp_unslash( $_POST['description'] ),
		);
		update_option( 'wl_entity_type_settings', $settings );

		// Redirect back to the term settings page and indicate a save was done.
		$url = admin_url( "admin.php?page=wl_entity_type_settings&tag_ID=$term->term_id&message=1" );
		wp_redirect( $url );
		exit;
	}

	/**
	 * Render the settings page for the term.
	 *
	 * Access and parameter validity is assumed to be done earlier.
	 *
	 * @since 3.11.0
	 */
	function settings_page() {

		$term_id = absint( $_REQUEST['tag_ID'] );
		$term    = get_term( $term_id, 'wl_entity_type' );

		$settings = get_option( 'wl_entity_type_settings', array() );
		$term_settings = isset( $settings[ $term_id ] ) ? $settings[ $term_id ] : array(
			'title' => '',
			'description' => '',
		);

		?>
		<div class="wrap">
			<h1><?php _e( 'Edit Entity Type', 'wordlift' ) ?></h1>

			<form name="edittag" id="edittag" method="post" action="<?php echo admin_url( 'admin.php?page=wl_entity_type_settings' )?>" class="validate">
			<input type="hidden" name="tag_ID" value="<?php echo esc_attr( $term_id ) ?>"/>
			<input type="hidden" name="action" value="wl_edit_entity_type_term"/>
			<?php
			wp_nonce_field( 'update-entity_type_term_' . $term_id );

			?>
				<table class="form-table">
					<tr class="form-field form-required term-name-wrap">
						<th scope="row"><label><?php _e( 'Name', 'wordlift' ); ?></label></th>
						<td><?php echo esc_html( $term->name )?></td>
					</tr>
					<tr class="form-field form-required term-name-wrap">
						<th scope="row"><label for="title"><?php _e( 'SEO Title', 'wordlift' ); ?></label></th>
						<td><input name="title" id="title" type="text" value="<?php echo esc_attr( $term_settings['title'] )?>" size="40" />
						<p class="description"><?php _e( 'The HTML title to be used at the entity type achieve page.' ); ?></p></td>
					</tr>
					<tr class="form-field term-description-wrap">
						<th scope="row"><label for="description"><?php _e( 'SEP Description' ); ?></label></th>
						<td><textarea name="description" id="description" rows="5" cols="50" class="large-text"><?php echo esc_html( $term_settings['description'] )  ?></textarea>
						<p class="description"><?php _e( 'The description meta used in the entity type achieve page.', 'wordlift' ); ?></p></td>
					</tr>
				</table>
			<?php
			submit_button( __( 'Update' ) );
			?>
			</form>
		</div>

		<?php
	}

}
