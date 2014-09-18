<?php

function wl_ajax_sparql() {

	// Get the query slug.
	$slug    = $_GET['slug'];

	// TODO: define an output format.
	$output  = 'csv';
	$accept  = 'text/csv';

	// TODO: Get the dataset and the key to build the API URL.
	$dataset = 'events';
	$key     = wl_config_get_application_key();

	// Get the query.
	$query   = wl_get_sparql_query( $slug );

	$url     = WL_REDLINK_API_BASE_URI . WL_REDLINK_API_VERSION . "/data/$dataset/sparql/select?key=$key&out=$output";

	// Prepare the request.
	$args    = array_merge_recursive( unserialize( WL_REDLINK_API_HTTP_OPTIONS ) , array(
		'method'  => 'POST',
		'headers' => array( 'Accept' => $accept ),
		'body'    => array( 'query'  => $query )
	));

	// Send the request.
	$response = wp_remote_post( $url, $args );

	// If an error has been raised, return the error.
	if ( is_wp_error( $response ) || 200 !== (int)$response['response']['code'] ) {

		echo "wl_execute_sparql_query ================================\n";
//        echo "[ api url :: $api_url ]\n"; -- enabling this will print out the key.
		echo " request : \n";
		var_dump( $args );
		echo " response: \n";
		var_dump( $response );
		echo " response body: \n";
		echo $response['body'];
		echo "=======================================================\n";

		return false;
	}

	echo $response['body'];

	wp_die();

}
add_action( 'wp_ajax_nopriv_wl_sparql', 'wl_ajax_sparql' );
add_action( 'wp_ajax_wl_sparql', 'wl_ajax_sparql' );

/**
 * Get a SPARQL query given a slug.
 *
 * @param string The SPARQL query slug.
 *
 * @return string The SPARQL query.
 */
function wl_get_sparql_query( $slug ) {

	// TODO: read the SPARQL query from WordPress.
	return 'PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>PREFIX gn: <http://www.geonames.org/ontology#>PREFIX schema: <http://schema.org/>SELECT ?label ?latitude ?longitude WHERE { ?s a <http://schema.org/FoodEstablishment> ;      schema:geo ?geo ;      rdfs:label ?label . ?geo schema:latitude ?latitude ;      schema:longitude ?longitude . FILTER( langMatches( lang( ?label ), "EN") ) } LIMIT 500';

}