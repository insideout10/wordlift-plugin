<?php
/**
 * Pages: Entity Type Term Settings Page.
 *
 * Assumes $term_id is set to the entity type term id, and $setting is set
 * to its overriding settings.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
$current_term = get_term( $term_id, 'wl_entity_type' );

// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
$term_settings = $settings !== null ? $settings : array(
	'title'       => '',
	'description' => '',
);

?>
<div class="wrap">
	<h1><?php esc_html_e( 'Edit Entity Type', 'wordlift' ); ?></h1>

	<form name="edittag" id="edittag" method="post"
		  action="<?php echo esc_html( admin_url( 'admin.php?page=wl_entity_type_settings' ) ); ?>"
		  class="validate">
		<input type="hidden" name="tag_ID"
			   value="
			   <?php
			   // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
				echo esc_attr( $term_id );
				?>
			   " />
		<input type="hidden" name="action" value="wl_edit_entity_type_term" />
		<?php
		// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
		wp_nonce_field( 'update-entity_type_term_' . $term_id );
		?>
		<table class="form-table">
			<tr class="form-field form-required term-name-wrap">
				<th scope="row">
					<label>
					<?php
						esc_html_e( 'Name', 'wordlift' );
					?>
						</label>
				</th>
				<td>
				<?php
					echo esc_html( $current_term->name )
				?>
					</td>
			</tr>
			<tr class="form-field form-required term-name-wrap">
				<th scope="row">
					<label for="title">
					<?php
						esc_html_e( 'Title', 'wordlift' );
					?>
						</label>
				</th>
				<td><input name="title" id="title" type="text"
						   value="<?php echo esc_attr( $term_settings['title'] ); ?>"
						   size="40" />
					<p class="description">
					<?php
						esc_html_e( 'The HTML title to be used in the entity type archive page.', 'wordlift' );
					?>
						</p>
				</td>
			</tr>
			<tr class="form-field term-description-wrap">
				<th scope="row"><label for="description">
				<?php
						esc_html_e( 'Description', 'wordlift' );
				?>
						</label>
				</th>
				<td><textarea name="description" id="description" rows="5"
							  cols="50" class="large-text"><?php echo esc_html( $term_settings['description'] ); ?></textarea>
					<p class="description">
					<?php
						esc_html_e( 'The description to be used in the entity type archive page.', 'wordlift' );
					?>
						</p>
				</td>
			</tr>
		</table>
		<?php submit_button( __( 'Update', 'default' ) ); ?>
	</form>
</div>
