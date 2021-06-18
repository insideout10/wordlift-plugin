<?php

namespace Wordlift\Common\Term_Checklist;
/**
 * @since 3.31.6
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * `wp_terms_checklist` generates duplicates on the list when we pass a list of selected category,
 * this class is used for rendering checklist without duplicates.
 */
class Term_Checklist {

	/**
	 * @param $input_name string The name of the input field assigned to checkbox.
	 * @param $terms array<\WP_Term>
	 * @param $selected_term_ids array<string> The list of selected term slugs.
	 *
	 * @return string Html string to be rendered.
	 */
	public static function render( $input_name, $terms, $selected_term_ids ) {


		$terms_html = "";

		$input_name = esc_html( $input_name );

		foreach ( $terms as $term ) {

			/**
			 * @var $term \WP_Term
			 */
			$term_name  = esc_html( $term->name );
			$checked    = in_array( $term->term_id, $selected_term_ids ) ? 'checked' : '';
			$terms_html .= <<<EOF
<li id="wl_entity_type-{$term->term_id}">
	<label class="selectit">
	<input value="{$term->slug}" type="checkbox" name="{$input_name}[]" id="in-wl_entity_type-{$term->term_id}" $checked>
		${term_name}
	</label>
</li>
EOF;

		}

		return $terms_html;
	}


}