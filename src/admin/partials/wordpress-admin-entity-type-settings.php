<?php
/**
 * Pages: Entity Type Term Settings Page.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/admin/partials
 */

// Get the term id and the the term.
$term_id = absint( $_REQUEST['tag_ID'] );
$term    = get_term( $term_id, 'wl_entity_type' );

// Get the settings array.
$settings      = get_option( 'wl_entity_type_settings', array() );
$term_settings = isset( $settings[ $term_id ] ) ? $settings[ $term_id ] : array(
	'title'       => '',
	'description' => '',
);

?>
<div class="wrap">
	<h1><?php echo esc_html_x( 'Edit Entity Type', 'wordlift' ) ?></h1>

	<form name="edittag" id="edittag" method="post"
	      action="<?php echo admin_url( 'admin.php?page=wl_entity_type_settings' ) ?>"
	      class="validate">
		<input type="hidden" name="tag_ID"
		       value="<?php echo esc_attr( $term_id ) ?>" />
		<input type="hidden" name="action" value="wl_edit_entity_type_term" />
		<?php wp_nonce_field( 'update-entity_type_term_' . $term_id ); ?>
		<table class="form-table">
			<tr class="form-field form-required term-name-wrap">
				<th scope="row">
					<label><?php
						echo esc_html_x( 'Name', 'wordlift' ); ?></label>
				</th>
				<td><?php
					echo esc_html( $term->name ) ?></td>
			</tr>
			<tr class="form-field form-required term-name-wrap">
				<th scope="row">
					<label for="title"><?php
						echo esc_html_x( 'SEO Title', 'wordlift' ); ?></label>
				</th>
				<td><input name="title" id="title" type="text"
				           value="<?php echo esc_attr( $term_settings['title'] ) ?>"
				           size="40" />
					<p class="description"><?php
						echo esc_html_x( 'The HTML title to be used at the entity type achieve page.', 'wordlift' ); ?></p>
				</td>
			</tr>
			<tr class="form-field term-description-wrap">
				<th scope="row"><label for="description"><?php
						echo esc_html_x( 'SEP Description', 'wordlift' ); ?></label>
				</th>
				<td><textarea name="description" id="description" rows="5"
				              cols="50" class="large-text"><?php
						echo esc_html( $term_settings['description'] ) ?></textarea>
					<p class="description"><?php
						echo esc_html_x( 'The description meta used in the entity type achieve page.', 'wordlift' ); ?></p>
				</td>
			</tr>
		</table>
		<?php submit_button( _x( 'Update' ) ); ?>
	</form>
</div>
