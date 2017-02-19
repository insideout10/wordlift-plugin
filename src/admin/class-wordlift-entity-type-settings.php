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
	 * The registration is required, although we do not want to actually add_action
	 * an item to the menu, to "whitelist" the access to the settings page in
	 * the admin.
	 *
	 * @since 3.11.0
	 */
	public function admin_menu() {

		// use a NULL parent slug to prevent the menu from actually appearing
		// in the admin menu.

		add_submenu_page(
			null,
			__( 'Edit Entity term', 'wordlift' ),
			__( 'Edit Entity term', 'wordlift' ),
			'manage_options',
			'wl_entity_type_edit',
			array( $this, 'settings_page' )
		);

		function settings_page() {

			// validate and prepare variables.

			// if no term id was supplied, redirect to the list page
			if ( empty( $_REQUEST['tag_ID'] ) ) {
				$sendback = admin_url( 'edit-tags.php?taxonomy=wl_entity_type&post_type=entity' );
				wp_redirect( esc_url( $sendback ) );
				exit;
			}

			// If a term id of a term that do not exists was supplied, just show a message
			$term_id = absint( $_REQUEST['tag_ID'] );
			$term    = get_term( $term_id, $taxnow, OBJECT, 'edit' );

			if ( ! $term instanceof WP_Term ) {
				wp_die( __( 'You attempted to edit an entity type that doesn&#8217;t exist.', 'wordlift' ) );
			}
			?>
			<div class="wrap">
				<h1><?php _e( 'Edit Entity Type', 'wordlift' ) ?></h1>

				<form name="edittag" id="edittag" method="post" action="" class="validate">
				<input type="hidden" name="tag_ID" value="<?php echo esc_attr( $term_id ) ?>"/>
				<input type="hidden" name="action" value="editedtag"/>
				<?php
				wp_nonce_field( 'update-entity_type_term_' . $term_id );

				?>
					<table class="form-table">
						<tr class="form-field form-required term-name-wrap">
							<th scope="row"><label for="title"><?php _e( 'SEO Title', 'wordlift' ); ?></label></th>
							<td><input name="title" id="title" type="text" value="<?php echo ( isset( $term->title ) ) ? esc_attr( $tag->name ) : '' ?>" size="40" aria-required="true" />
							<p class="description"><?php _e( 'The HTML title to be used at the entity type achieve page.' ); ?></p></td>
						</tr>
						<tr class="form-field term-description-wrap">
							<th scope="row"><label for="description"><?php _e( 'SEP Description' ); ?></label></th>
							<td><textarea name="description" id="description" rows="5" cols="50" class="large-text"><?php echo $term->description; // textarea_escaped ?></textarea>
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
}
