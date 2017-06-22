<?php
/**
 * Globals: SPARQL Prefixes.
 *
 * @since   3.0.0
 * @package Wordlift
 */

/**
 * Get the list of prefixes.
 *
 * @since   3.0.0
 *
 * @return array An array of prefixes, each made of a *prefix* and *namespace* key-values.
 */
function wl_prefixes_list() {

	// If the parameter is false, default prefixes have never been installed.
	if ( false === ( $prefixes = get_option( 'wl_option_prefixes' ) ) ) {

		$prefixes = array(
			array(
				'prefix'    => 'geo',
				'namespace' => 'http://www.w3.org/2003/01/geo/wgs84_pos#',
			),
			array(
				'prefix'    => 'dct',
				'namespace' => 'http://purl.org/dc/terms/',
			),
			array(
				'prefix'    => 'rdfs',
				'namespace' => 'http://www.w3.org/2000/01/rdf-schema#',
			),
			array(
				'prefix'    => 'owl',
				'namespace' => 'http://www.w3.org/2002/07/owl#',
			),
			array( 'prefix' => 'schema', 'namespace' => 'http://schema.org/' ),
			array(
				'prefix'    => 'xsd',
				'namespace' => 'http://www.w3.org/2001/XMLSchema#',
			),
		);
		add_option( 'wl_option_prefixes', $prefixes );
	}

	return $prefixes;
}
