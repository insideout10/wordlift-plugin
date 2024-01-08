<?php

/**
 * Filter the author object to add the co-authors.
 *
 * This filter checks if there are co-authors or a single author and
 * returns a JSON-LD fragment for the author(s).
 *
 * @param array $value {
 *
 * @type array $author The author JSON-LD structure.
 * @type int[] $references An array of post IDs.
 * }
 *
 * @param int   $post_id The post ID.
 *
 * @return array An array with the updated JSON-LD and references.
 *
 * @since 3.51.4
 *
 * @see https://www.geeklab.info/2010/04/wordpress-pass-variables-by-reference-with-apply_filter/
 */
function _wl_jsonld_author__author_filter( $args_arr, $post_id ) {

	$references = $args_arr['references'];

	$coauthor_plugin_path = 'co-authors-plus/co-authors-plus.php';

	// If the co-authors plugin is active.
	if ( ! is_plugin_active( $coauthor_plugin_path ) || ! function_exists( 'get_coauthors' ) ) {
		return $args_arr;
	}

	$coauthors = get_coauthors( $post_id );

	// And we have multiple authors on a post.
	if ( empty( $coauthors ) ) {
		return $args_arr;
	}

	// Clear the existing author.
	$author = array();

	// Build array of authors.
	$wordlift_post_to_jsonld_converter = Wordlift_Post_To_Jsonld_Converter::get_instance();
	foreach ( $coauthors as $coauthor ) {
		$author[] = $wordlift_post_to_jsonld_converter->get_author( $coauthor->ID, $references );
	}

	return array(
		'author'     => $author,
		'references' => $references,
	);
}

// Add the filter
add_filter( 'wl_jsonld_author', '_wl_jsonld_author__author_filter', 10, 2 );
