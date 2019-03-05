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

	/**
	 * The meta key holding the entity id.
	 *
	 * @since 3.20.0
	 */
	const META_KEY = '_wl_entity_id';

	/**
	 * Create a Wordlift_Admin_Term_Adapter instance.
	 *
	 * @since 3.20.0
	 */
	public function __construct() {

		add_action( 'registered_taxonomy', array( $this, 'add_action', ) );
		add_action( 'edit_term', array( $this, 'edit_term', ), 10, 3 );

	}

	/**
	 * Add the form fields to the entity edit screen.
	 *
	 * @since 3.20.0
	 *
	 * @param object $tag Current taxonomy term object.
	 * @param string $taxonomy Current taxonomy slug.
	 */
	public function edit_form_fields( $tag, $taxonomy ) {

		// Enqueue the JavaScript app.
		wp_enqueue_script( 'wl-term', plugin_dir_url( dirname( __FILE__ ) ) . 'js/dist/term.js', array( 'wp-util', ), Wordlift::get_instance()->get_version(), true );
		wp_enqueue_style( 'wl-term', plugin_dir_url( dirname( __FILE__ ) ) . 'js/dist/term.css', array(), Wordlift::get_instance()->get_version() );

		$values = get_term_meta( $tag->term_id, self::META_KEY );

		?>
        <tr class="form-field term-name-wrap">
            <th scope="row"><label for="wl-entity-id"><?php _ex( 'Entity', 'term entity', 'wordlift' ); ?></label></th>
            <td>
				<?php foreach ( $values as $value ) { ?>
                    <input type="text" name="wl_entity_id[]" value="<?php echo esc_attr( $value ); ?>"/>
				<?php } ?>
                <div id="wl-term-entity-id"></div>
                <p class="description"><?php _e( 'The entity bound to the term.', 'wordlift' ); ?></p>
            </td>
        </tr>
		<?php
	}

	/**
	 * Bind the new fields to the edit term screen.
	 *
	 * @since 3.20.0
	 *
	 * @param string $taxonomy The taxonomy name.
	 */
	public function add_action( $taxonomy ) {

		add_action( "{$taxonomy}_edit_form_fields", array( $this, 'edit_form_fields' ), 10, 2 );
	}

	/**
	 * Hook to the edit term to handle our own custom fields.
	 *
	 * @since 3.20.0
	 *
	 * @param int    $term_id The term id.
	 * @param int    $tt_id The term taxonomy id.
	 * @param string $taxonomy The taxonomy.
	 */
	public function edit_term( $term_id, $tt_id, $taxonomy ) {

		// Bail if the action isn't related to the term currently being edited.
		if ( ! isset( $_POST['tag_ID'] ) || $term_id !== (int) (int) $_POST['tag_ID'] ) {
			return;
		}

		// Delete.
		if ( ! isset( $_POST['wl_entity_id'] ) || ! is_array( $_POST['wl_entity_id'] ) || empty( $_POST['wl_entity_id'] ) ) {
			delete_term_meta( $term_id, self::META_KEY );

			return;
		}

		// Update.
		//
		// Only use mb_* functions when mbstring is available.
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/693.
		if ( extension_loaded( 'mbstring' ) ) {
			mb_regex_encoding( 'UTF-8' );

			$merged = array_reduce( (array) $_POST['wl_entity_id'], function ( $carry, $item ) {
				return array_merge( $carry, mb_split( "\x{2063}", wp_unslash( $item ) ) );
			}, array() );
		} else {
			$merged = array_reduce( (array) $_POST['wl_entity_id'], function ( $carry, $item ) {
				return array_merge( $carry, preg_split( "/\x{2063}/u", wp_unslash( $item ) ) );
			}, array() );
		}

		delete_term_meta( $term_id, self::META_KEY );
		foreach ( array_unique( array_filter( $merged ) ) as $single ) {
			add_term_meta( $term_id, self::META_KEY, $single );
		}

	}

}