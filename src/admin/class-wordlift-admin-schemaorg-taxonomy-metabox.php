<?php

class Wordlift_Admin_Schemaorg_Taxonomy_Metabox {

	public function __construct() {

		add_action( 'wp_ajax_wl_schemaorg', array( $this, 'schemaorg' ) );
		add_action( 'wp_ajax_wl_schemaorg_term_for_post', array( $this, 'schemaorg_term_for_post' ) );

	}

	public function schemaorg_term_for_post() {

		$args = $_POST;

		if ( ! wp_verify_nonce( $args['nonce'], 'wl_schemaorg_term_for_post' ) ) {
			wp_send_json_error( 'nonce_failure' );
		}

		$next_nonce = wp_create_nonce( 'wl_schemaorg_term_for_post' );

		if ( empty( $_POST['type'] )
		     || ( 'add' !== $_POST['type'] && 'remove' !== $_POST['type'] ) ) {
			wp_send_json_error( array(
				'nonce'   => $next_nonce,
				'message' => '`type` is required.',
			) );
		}

		if ( ! isset( $_POST['post_id'] ) || ! is_numeric( $_POST['post_id'] ) ) {
			wp_send_json_error( array(
				'nonce'   => $next_nonce,
				'message' => '`post_id` is required.',
			) );
		}

		if ( empty( $_POST['slug'] ) ) {
			wp_send_json_error( array(
				'nonce'   => $next_nonce,
				'message' => '`slug` is required.',
			) );
		}

		$type    = $_POST['type'];
		$post_id = (int) $_POST['post_id'];
		$slug    = $_POST['slug'];


		$result = 'add' === $type
			? wp_add_object_terms( $post_id, $slug, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME )
			: wp_remove_object_terms( $post_id, $slug, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'nonce' => $next_nonce, 'message' => $result->get_error_message() ) );
		}

		wp_send_json_success( array( 'nonce' => $next_nonce ) );

	}

	public function schemaorg() {

		header( 'Content-Type: application/json; charset=UTF-8' );

		echo( '{"success":true, "data":' );
		include( plugin_dir_path( __FILE__ ) . 'partials/schemaorg.min.json' );
		echo( '}' );

		if ( wp_doing_ajax() ) {
			wp_die( '', '', array(
				'response' => null,
			) );
		} else {
			die;
		}

	}

	public static function render() {

		Wordlift_Admin_Schemaorg_Taxonomy_Metabox::post_categories_meta_box( get_post(), array( 'args' => array( 'taxonomy' => Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME ) ) );

	}

	private static function post_categories_meta_box( $post, $box ) {
		$defaults = array( 'taxonomy' => 'category' );
		if ( ! isset( $box['args'] ) || ! is_array( $box['args'] ) ) {
			$args = array();
		} else {
			$args = $box['args'];
		}
		$r        = wp_parse_args( $args, $defaults );
		$tax_name = esc_attr( $r['taxonomy'] );
		$taxonomy = get_taxonomy( $r['taxonomy'] );
		?>
        <div id="taxonomy-<?php echo $tax_name; ?>" class="categorydiv">
            <ul id="<?php echo $tax_name; ?>-tabs" class="category-tabs">
                <li class="tabs"><a href="#<?php echo $tax_name; ?>-all"><?php echo $taxonomy->labels->all_items; ?></a>
                </li>
                <li class="hide-if-no-js"><a
                            href="#<?php echo $tax_name; ?>-pop"><?php echo esc_html( $taxonomy->labels->most_used ); ?></a>
                </li>
                <li class="hide-if-no-js"><a
                            href="#<?php echo $tax_name; ?>-legacy"><?php echo $taxonomy->labels->all_items; ?></a>
                </li>
            </ul>

            <div id="<?php echo $tax_name; ?>-all" class="tabs-panel">
                <div id="wl-schema-class-tree"></div>
            </div>

            <div id="<?php echo $tax_name; ?>-pop" class="tabs-panel" style="display: none;">
                <ul id="<?php echo $tax_name; ?>checklist-pop" class="categorychecklist form-no-clear">
					<?php $popular_ids = wp_popular_terms_checklist( $tax_name ); ?>
                </ul>
            </div>

            <div id="<?php echo $tax_name; ?>-legacy" class="tabs-panel" style="display: none;">
				<?php
				$name = ( $tax_name == 'category' ) ? 'post_category' : 'tax_input[' . $tax_name . ']';
				echo "<input type='hidden' name='{$name}[]' value='0' />"; // Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.
				?>
                <ul id="<?php echo $tax_name; ?>checklist" data-wp-lists="list:<?php echo $tax_name; ?>"
                    class="categorychecklist form-no-clear">
					<?php wp_terms_checklist( $post->ID, array(
						'taxonomy'     => $tax_name,
						'popular_cats' => $popular_ids,
					) ); ?>
                </ul>
            </div>
			<?php if ( current_user_can( $taxonomy->cap->edit_terms ) ) : ?>
                <div id="<?php echo $tax_name; ?>-adder" class="wp-hidden-children">
                    <a id="<?php echo $tax_name; ?>-add-toggle" href="#<?php echo $tax_name; ?>-add"
                       class="hide-if-no-js taxonomy-add-new">
						<?php
						/* translators: %s: add new taxonomy label */
						printf( __( '+ %s' ), $taxonomy->labels->add_new_item );
						?>
                    </a>
                    <p id="<?php echo $tax_name; ?>-add" class="category-add wp-hidden-child">
                        <label class="screen-reader-text"
                               for="new<?php echo $tax_name; ?>"><?php echo $taxonomy->labels->add_new_item; ?></label>
                        <input type="text" name="new<?php echo $tax_name; ?>" id="new<?php echo $tax_name; ?>"
                               class="form-required form-input-tip"
                               value="<?php echo esc_attr( $taxonomy->labels->new_item_name ); ?>"
                               aria-required="true"/>
                        <label class="screen-reader-text" for="new<?php echo $tax_name; ?>_parent">
							<?php echo $taxonomy->labels->parent_item_colon; ?>
                        </label>
						<?php
						$parent_dropdown_args = array(
							'taxonomy'         => $tax_name,
							'hide_empty'       => 0,
							'name'             => 'new' . $tax_name . '_parent',
							'orderby'          => 'name',
							'hierarchical'     => 1,
							'show_option_none' => '&mdash; ' . $taxonomy->labels->parent_item . ' &mdash;',
						);

						/**
						 * Filters the arguments for the taxonomy parent dropdown on the Post Edit page.
						 *
						 * @since 4.4.0
						 *
						 * @param array   $parent_dropdown_args {
						 *     Optional. Array of arguments to generate parent dropdown.
						 *
						 * @type string   $taxonomy Name of the taxonomy to retrieve.
						 * @type bool     $hide_if_empty True to skip generating markup if no
						 *                                      categories are found. Default 0.
						 * @type string   $name Value for the 'name' attribute
						 *                                      of the select element.
						 *                                      Default "new{$tax_name}_parent".
						 * @type string   $orderby Which column to use for ordering
						 *                                      terms. Default 'name'.
						 * @type bool|int $hierarchical Whether to traverse the taxonomy
						 *                                      hierarchy. Default 1.
						 * @type string   $show_option_none Text to display for the "none" option.
						 *                                      Default "&mdash; {$parent} &mdash;",
						 *                                      where `$parent` is 'parent_item'
						 *                                      taxonomy label.
						 * }
						 */
						$parent_dropdown_args = apply_filters( 'post_edit_category_parent_dropdown_args', $parent_dropdown_args );

						wp_dropdown_categories( $parent_dropdown_args );
						?>
                        <input type="button" id="<?php echo $tax_name; ?>-add-submit"
                               data-wp-lists="add:<?php echo $tax_name; ?>checklist:<?php echo $tax_name; ?>-add"
                               class="button category-add-submit"
                               value="<?php echo esc_attr( $taxonomy->labels->add_new_item ); ?>"/>
						<?php wp_nonce_field( 'add-' . $tax_name, '_ajax_nonce-add-' . $tax_name, false ); ?>
                        <span id="<?php echo $tax_name; ?>-ajax-response"></span>
                    </p>
                </div>
			<?php endif; ?>
        </div>
		<?php
	}

}
