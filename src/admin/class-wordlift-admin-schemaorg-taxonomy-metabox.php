<?php
/**
 * Metaboxes: Schema.org Taxonomy Metabox.
 *
 * A customized metabox for the Entity Types taxonomy, supporting the treeview for the Schema.org
 * taxonomy.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the {@link Wordlift_Admin_Schemaorg_Taxonomy_Metabox} class.
 *
 * @since 3.20.0
 */
class Wordlift_Admin_Schemaorg_Taxonomy_Metabox {

	/**
	 * Render the metabox.
	 *
	 * @since 3.20.0
	 */
	public static function render() {

		self::post_categories_meta_box(
			get_post(),
			array(
				'args' =>
					array( 'taxonomy' => Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME ),
			)
		);

	}

	/**
	 * A function which resembles WordPress' own to display a metabox, but which customizes the output
	 * to display the Schema.org classes tree.
	 *
	 * @param WP_Post $post The {@link WP_Post} being edited.
	 * @param array   $box An array of arguments.
	 *
	 * @since 3.20.0
	 */
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
		<div id="taxonomy-<?php echo esc_attr( $tax_name ); ?>" class="categorydiv">
			<ul id="<?php echo esc_attr( $tax_name ); ?>-tabs" class="category-tabs">
				<li class="tabs"><a
							href="#<?php echo esc_attr( $tax_name ); ?>-all"><?php echo esc_html( $taxonomy->labels->all_items ); ?></a>
				</li>
				<li>
					<a href="#<?php echo esc_attr( $tax_name ); ?>-pop"><?php echo esc_html__( 'Most Used', 'wordlift' ); ?></a>
				</li>
				<li><a href="#<?php echo esc_attr( $tax_name ); ?>-legacy">
						<?php echo esc_html_x( 'A-Z', 'Entity Types metabox', 'wordlift' ); ?></a>
				</li>
			</ul>

			<div id="<?php echo esc_attr( $tax_name ); ?>-all" class="tabs-panel">
				<div id="wl-schema-class-tree"></div>
			</div>

			<div id="<?php echo esc_attr( $tax_name ); ?>-pop" class="tabs-panel" style="display: none;">
				<ul id="<?php echo esc_attr( $tax_name ); ?>checklist-pop" class="categorychecklist form-no-clear">
					<?php $popular_ids = wp_popular_terms_checklist( $tax_name ); ?>
				</ul>
			</div>

			<div id="<?php echo esc_attr( $tax_name ); ?>-legacy" class="tabs-panel" style="display: none;">
				<?php
				$name = ( 'category' === $tax_name ) ? 'post_category' : 'tax_input[' . $tax_name . ']';
				echo wp_kses(
					sprintf(
						"<input type='hidden' name='%s[]' value='0' />",
						esc_attr( $name )
					),
					array(
						'input' =>
							array(
								'type'  => array(),
								'name'  => array(),
								'value' => array(),
							),
					)
				); // Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.
				?>
				<ul id="<?php echo esc_attr( $tax_name ); ?>checklist"
					data-wp-lists="list:<?php echo esc_attr( $tax_name ); ?>"
					class="categorychecklist form-no-clear">
					<?php
					wp_terms_checklist(
						$post->ID,
						array(
							'taxonomy'     => $tax_name,
							'popular_cats' => $popular_ids,
						)
					);
					?>
				</ul>
			</div>
			<?php if ( current_user_can( $taxonomy->cap->edit_terms ) ) : ?>
				<div id="<?php echo esc_attr( $tax_name ); ?>-adder" class="wp-hidden-children">
					<a id="<?php echo esc_attr( $tax_name ); ?>-add-toggle"
					   href="#<?php echo esc_attr( $tax_name ); ?>-add"
					   class="taxonomy-add-new">
						<?php
						/* translators: %s: add new taxonomy label */
						echo esc_html( sprintf( __( '+ %s', 'default' ), $taxonomy->labels->add_new_item ) );
						?>
					</a>
					<p id="<?php echo esc_attr( $tax_name ); ?>-add" class="category-add wp-hidden-child">
						<label class="screen-reader-text"
							   for="new<?php echo esc_attr( $tax_name ); ?>"><?php echo esc_html( $taxonomy->labels->add_new_item ); ?></label>
						<input type="text" name="new<?php echo esc_html( $tax_name ); ?>"
							   id="new<?php echo esc_attr( $tax_name ); ?>"
							   class="form-required form-input-tip"
							   value="<?php echo esc_attr( $taxonomy->labels->new_item_name ); ?>"
							   aria-required="true"/>
						<label class="screen-reader-text" for="new<?php echo esc_attr( $tax_name ); ?>_parent">
							<?php echo esc_html( $taxonomy->labels->parent_item_colon ); ?>
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
						 * @param array $parent_dropdown_args {
						 *     Optional. Array of arguments to generate parent dropdown.
						 *
						 * @type string $taxonomy Name of the taxonomy to retrieve.
						 * @type bool $hide_if_empty True to skip generating markup if no
						 *                                      categories are found. Default 0.
						 * @type string $name Value for the 'name' attribute
						 *                                      of the select element.
						 *                                      Default "new{$tax_name}_parent".
						 * @type string $orderby Which column to use for ordering
						 *                                      terms. Default 'name'.
						 * @type bool|int $hierarchical Whether to traverse the taxonomy
						 *                                      hierarchy. Default 1.
						 * @type string $show_option_none Text to display for the "none" option.
						 *                                      Default "&mdash; {$parent} &mdash;",
						 *                                      where `$parent` is 'parent_item'
						 *                                      taxonomy label.
						 * }
						 * @since 4.4.0
						 */
						$parent_dropdown_args = apply_filters( 'post_edit_category_parent_dropdown_args', $parent_dropdown_args );

						wp_dropdown_categories( $parent_dropdown_args );
						?>
						<input type="button" id="<?php echo esc_attr( $tax_name ); ?>-add-submit"
							   data-wp-lists="add:<?php echo esc_html( $tax_name ); ?>checklist:<?php echo esc_attr( $tax_name ); ?>-add"
							   class="button category-add-submit"
							   value="<?php echo esc_attr( $taxonomy->labels->add_new_item ); ?>"/>
						<?php wp_nonce_field( 'add-' . $tax_name, '_ajax_nonce-add-' . $tax_name, false ); ?>
						<span id="<?php echo esc_attr( $tax_name ); ?>-ajax-response"></span>
					</p>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

}
