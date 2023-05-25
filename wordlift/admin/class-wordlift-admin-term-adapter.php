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

		add_action( 'registered_taxonomy', array( $this, 'add_action' ) );
		add_action( 'edit_term', array( $this, 'edit_term' ), 10 );
		$this->add_settings();

	}

	/**
	 * Hook in to WordLift admin settings and add the term page specific
	 * settings.
	 *
	 * @since 3.26.1
	 */
	public function add_settings() {
		add_filter(
			'wl_admin_settings',
			function ( $params ) {
				$params['show_local_entities'] = true;

				return $params;
			}
		);
	}

	/**
	 * Add the form fields to the entity edit screen.
	 *
	 * @param object $tag Current taxonomy term object.
	 *
	 * @since 3.20.0
	 */
	public function edit_form_fields( $tag ) {

		// If disabled via filter, return;
		// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		if ( ! apply_filters( 'wl_feature__enable__term-entity', true ) ) {
			return;
		}

		global $wp_version;

		// Enqueue the JavaScript app.
		if ( version_compare( $wp_version, '5.0', '>=' ) ) {
			$term_asset = include plugin_dir_path( __DIR__ ) . 'js/dist/term.asset.php';
			wp_enqueue_script( 'wl-term', plugin_dir_url( __DIR__ ) . 'js/dist/term.js', array_merge( array( 'wp-util' ), $term_asset['dependencies'] ), Wordlift::get_instance()->get_version(), true );
		} else {
			wp_enqueue_script( 'wl-term', plugin_dir_url( __DIR__ ) . 'js/dist/term.full.js', array( 'wp-util' ), Wordlift::get_instance()->get_version(), true );
		}

		wp_enqueue_style( 'wl-term', plugin_dir_url( __DIR__ ) . 'js/dist/term.css', array(), Wordlift::get_instance()->get_version() );

		$values = get_term_meta( $tag->term_id, self::META_KEY );

		/**
		 * @since 3.31.3
		 * @see https://github.com/insideout10/wordlift-plugin/issues/1446
		 * This field should be hidden by default
		 */
		if ( ! $values ) {
			return;
		}

		?>
		<tr class="form-field term-name-wrap">
			<th scope="row"><label
						for="wl-entity-id"><?php echo esc_html_x( 'Entity', 'term entity', 'wordlift' ); ?></label></th>
			<td>
				<?php foreach ( $values as $value ) { ?>
					<input type="text" name="wl_entity_id[]" value="<?php echo esc_attr( $value ); ?>"/>
				<?php } ?>
				<div id="wl-term-entity-id"></div>
				<p class="description"><?php esc_html_e( 'The entity bound to the term.', 'wordlift' ); ?></p>
			</td>
		</tr>
		<?php wp_nonce_field( 'wordlift_term_entity_edit', 'wordlift_term_entity_edit_nonce' ); ?>
		<?php
	}

	/**
	 * Bind the new fields to the edit term screen.
	 *
	 * @param string $taxonomy The taxonomy name.
	 *
	 * @since 3.20.0
	 */
	public function add_action( $taxonomy ) {
		/**
		 * Filter wl_feature__enable__taxonomy_term_entity_mapping renamed to wl_feature__enable__term-entity.
		 */

		add_action( "{$taxonomy}_edit_form_fields", array( $this, 'edit_form_fields' ), 10 );

	}

	/**
	 * Hook to the edit term to handle our own custom fields.
	 *
	 * We turn off nonce verification here because we're responding to a WordPress hook.
	 *
	 * @param int $term_id The term id.
	 *
	 * @since 3.20.0
	 */
	public function edit_term( $term_id ) {

		// Bail if the action isn't related to the term currently being edited.
        // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['tag_ID'] ) || $term_id !== (int) $_POST['tag_ID'] ) {
			return;
		}

		// Delete.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['wl_entity_id'] ) || ! is_array( $_POST['wl_entity_id'] ) || empty( $_POST['wl_entity_id'] ) ) {
			delete_term_meta( $term_id, self::META_KEY );

			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$entity_ids = array_map( 'esc_url_raw', wp_unslash( $_POST['wl_entity_id'] ) );
		// Update.
		//
		// Only use mb_* functions when mbstring is available.
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/693.
		if ( extension_loaded( 'mbstring' ) ) {
			mb_regex_encoding( 'UTF-8' );

			$merged = array_reduce(
				$entity_ids,
				function ( $carry, $item ) {
					return array_merge( $carry, mb_split( "\x{2063}", wp_unslash( $item ) ) );
				},
				array()
			);
		} else {
			$merged = array_reduce(
				$entity_ids,
				function ( $carry, $item ) {
					return array_merge( $carry, preg_split( "/\x{2063}/u", wp_unslash( $item ) ) );
				},
				array()
			);
		}

		delete_term_meta( $term_id, self::META_KEY );
		foreach ( array_unique( array_filter( $merged ) ) as $single ) {
			add_term_meta( $term_id, self::META_KEY, $single );
		}

	}

}
