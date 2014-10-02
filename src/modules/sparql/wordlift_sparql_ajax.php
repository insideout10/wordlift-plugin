<?php

function wl_ajax_sparql() {

    header( 'Access-Control-Allow-Origin: *' );

	// Get the query slug.
	$slug    = $_GET['slug'];

    // Get the output format: csv or geojson.
    $format  = ( empty( $_GET['format'] ) ? 'csv' : $_GET['format'] );

	// TODO: define an output format.
	$output  = 'csv';
	$accept  = 'text/csv';

	// TODO: Get the dataset and the key to build the API URL.
	$dataset = wl_config_get_dataset();
	$key     = wl_config_get_application_key();

	// Get the query.
	$query   = wl_sparql_replace_params( wl_sparql_get_query_by_slug( $slug ), $_GET );

    // Print out the query if the debug flag is set.
    if ( 1 == $_GET['_debug'] ) {
        wp_die( $query );
    }

    if ( empty( $query ) ) {
        wp_die( "Cannot find a SPARQL query [ slug :: $slug ]" );
    }

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
        // echo "[ api url :: $api_url ]\n"; -- enabling this will print out the key.
		echo " request : \n";
		var_dump( $args );
		echo " response: \n";
		var_dump( $response );
		echo " response body: \n";
		echo $response['body'];
		echo "=======================================================\n";

		return false;
	}

    // Output the data according to the chosen format.
    switch ( $format ) {
        case 'geojson':
            wl_csv_to_geojson( $response['body'] );
            break;
        case 'json':
            wl_csv_to_json( $response['body'] );
            break;
        default:
            echo $response['body'];
    }

	wp_die();

}
add_action( 'wp_ajax_nopriv_wl_sparql', 'wl_ajax_sparql' );
add_action( 'wp_ajax_wl_sparql', 'wl_ajax_sparql' );

/**
 * Replace the provided parameters map.
 *
 * @param string $query The SPARQL query.
 * @param array $params A key-value map of parameters.
 * @return string The query.
 */
function wl_sparql_replace_params( $query, $params ) {

    foreach ( $params as $key => $value ) {
        $query = str_ireplace( '{' . $key . '}', $value, $query );
    }

    return $query;

}


function wl_csv_to_json( $body ) {

header( 'Content-Type: application/json' );

    // Add the initial collection, will be closed at the end.
    echo  '[';

    // Parse the body.
    wl_csv_to_json_parse_body( $body );

    // Close the feature collection.
    echo ']';

}

/**
 * Parse a CSV body and output the JSON.
 *
 * @param string $body The body.
 */
function wl_csv_to_json_parse_body( $body ) {

    $line_count = -1;

    // The headers array will hold the CSV headers.
    $headers    = array();

    // Parse each row. The first row will be used to determine the field names. Empty lines will be ignored.
    foreach ( explode( "\n", $body) as $line ) {

        // Skip the first line.
        if ( 0 === ++$line_count  ) {
            $headers = str_getcsv( $line );
            continue;
        }

        // Skip empty lines.
        if ( empty( $line ) ) {
            continue;
        }

        // Add a comma separator.
        if ( 1 < $line_count ) {
            echo ",";
        };


        // Populate the item fields.
        $fields = str_getcsv( $line );
        $item   = array();
        for ( $i = 0; $i < sizeof( $headers ); $i++ ) {
            $item[$headers[$i]] = $fields[$i];
        }

        // Echo the item in JSON format.
        echo json_encode( $item );

    }

}

function wl_csv_to_geojson( $body ) {

    header( 'Content-Type: application/vnd.geo+json' );

    // Add the initial collection, will be closed at the end.
    echo  '{ "type": "FeatureCollection", "features": [';

    // Parse the body.
    wl_csv_to_geojson_parse_body( $body );

    // Close the feature collection.
    echo ']}';

}

/**
 * Parse a CSV body and output the GeoJSON features. The CSV must contain and header row. The result is output using echo.
 *
 * @param string $body The body.
 */
function wl_csv_to_geojson_parse_body( $body ) {

    $line_count = -1;
    foreach ( explode( "\n", $body) as $line ) {

        // Skip the first line.
        if ( 0 === ++$line_count || empty( $line ) ) {
            continue;
        }

        // Add a comma separator.
        if ( 1 < $line_count ) {
            echo ",";
        };

        // Get the fields.
        $fields    = str_getcsv( $line );
        $label_e   = json_encode( $fields[0] );
        $latitude  = floatval( $fields[1] );
        $longitude = floatval( $fields[2] );

        echo <<<EOF
{
    "type": "Feature",
    "geometry": { "type": "Point", "coordinates": [$longitude,$latitude] },
    "properties": { "name": $label_e, "popupContent": $label_e }
}
EOF;
    }

}
