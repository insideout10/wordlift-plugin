<?php
/**
 * Adapters: Term Adapter.
 *
 * Hooks to the Term edit screen to extend it with an autocomplete select to bind entities to entity terms.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the Wordlift_Admin_Term_Adapter class.
 *
 * @since 3.20.0
 */
class Wordlift_Admin_Term_Adapter {

	public function __construct() {

		add_action( 'registered_taxonomy', array( $this, 'add_action', ) );

	}

	/**
	 * @param object $tag Current taxonomy term object.
	 * @param string $taxonomy Current taxonomy slug.
	 */
	public function edit_form_fields( $tag, $taxonomy ) {
?>
		<tr class="form-field term-name-wrap">
			<th scope="row"><label for="wl-entity-id"><?php _ex( 'Entity', 'term entity', 'wordlift' ); ?></label></th>
			<td><input name="wl_entity_id" id="wl-entity-id" type="text" value="" />
				<p class="description"><?php _e('The entity bound to the term.', 'wordlift'); ?></p></td>
		</tr>
<?php
	}

	public function add_action( $taxonomy ) {

		add_action( "{$taxonomy}_edit_form_fields", array( $this, 'edit_form_fields' ), 10, 2 );
	}

}