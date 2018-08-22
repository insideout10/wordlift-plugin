<?php
/**
 * Services: Schema Class Service.
 *
 * This service provides an Ajax hook to load the list of classes in the `wl_entity_type` taxonomy. The
 * output is similar to the GraphQL output, but we also output the terms' ids which are required to be
 * compatible with WordPress' entity types metabox in the post edit screen.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/includes/schemaorg
 */

/**
 * Define the Wordlift_Schema_Class_Service class.
 *
 * @since 3.20.0
 */
class Wordlift_Schemaorg_Class_Service {

	/**
	 * The term meta key holding the CamelCase name for the term. The term has also a WP_Term->name
	 * property which however is to be considered a customizable label (especially for languages other
	 * than English).
	 *
	 * @since 3.20.0
	 */
	const NAME_META_KEY = '_wl_name';

	/**
	 * The term meta key holding the list of children terms ids.
	 *
	 * @since 3.20.0
	 */
	const PARENT_OF_META_KEY = '_wl_parent_of';

	/**
	 * Create a {@link Wordlift_Schema_Class} instance.
	 *
	 * @since 3.20.0
	 */
	public function __construct() {

		add_action( 'wp_ajax_wl_schemaorg_class', array( $this, 'schemaorg_class' ) );

	}

	/**
	 * The end-point to output the list of terms from the `wl_entity_taxonomy`.
	 *
	 * Example output:
	 * ```
	 * {
	 *  "name": "AMRadioChannel",
	 *  "dashname": "am-radio-channel",
	 *  "description": "A radio channel that uses AM.",
	 *  "children": []
	 * }
	 * ```
	 *
	 * @since 3.20.0
	 */
	public function schemaorg_class() {

		// Since we want to be compatible with WP 4.4, we use the pre-4.5.0 style when
		// calling `get_terms`.
		$terms = get_terms( Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, array(
			'hide_empty' => false,
		) );

		$json = array_map(
			function ( $term ) {
				// Do not change the following, the `name` is used to reference the correct
				// Schema.org class (CamelCase name). Do not use WP_Term->name.
				$camel_case_name = get_term_meta( $term->term_id, self::NAME_META_KEY, true );

				return array(
					'id'          => $term->term_id,
					// Do not change the following, the `name` is used to reference the correct
					// Schema.org class (CamelCase name). Do not use WP_Term->name.
					'name'        => $camel_case_name,
					'dashname'    => $term->slug,
					'description' => $term->description,
					'children'    => array_map( function ( $child ) {
						// Map the slug to the CamelCase name.
						$term            = get_term_by( 'slug', $child, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
						$camel_case_name = get_term_meta( $term->term_id, self::NAME_META_KEY, true );

						return array( 'name' => $camel_case_name );
					}, get_term_meta( $term->term_id, self::PARENT_OF_META_KEY ) ),
				);

			}, $terms );

		// Finally send the data.
		wp_send_json_success( array( 'schemaClasses' => $json ) );

	}

}
