<?php
/**
 * This file contains functions related to Redlink.
 */

/**
 * Get the Redlink SPARQL Update URL.
 */
function wordlift_redlink_sparql_update_url()
{

    // get the configuration.
    $dataset_id = wl_config_get_dataset();
    $app_key = wl_config_get_application_key();

    // construct the API URL.
    return WL_REDLINK_API_BASE_URI . WL_REDLINK_API_VERSION . "/data/" . $dataset_id . "/sparql/update?key=" . $app_key;
}

/**
 * Get the Redlink dataset reindex url.
 * @return string The Redlink dataset reindex url.
 */
function wordlift_redlink_reindex_url()
{

    // get the configuration.
    $dataset_id = wl_config_get_dataset();
    $app_key = wl_config_get_application_key();

    // construct the API URL.
    return WL_REDLINK_API_BASE_URI . WL_REDLINK_API_VERSION . "/data/" . $dataset_id . "/release?key=" . $app_key;
}

/**
 * Get the Redlink API enhance URL.
 * @return string The Redlink API enhance URL.
 */
function wordlift_redlink_enhance_url()
{

    // remove configuration keys from here.
    $app_key = wl_config_get_application_key();
    $analysis_name = wl_config_get_analysis();

    return WL_REDLINK_API_BASE_URI . WL_REDLINK_API_VERSION . '/analysis/' . $analysis_name . '/enhance?key=' . $app_key; # . '&enhancer.engines.dereference.ldpath=' . urlencode('<http://www.w3.org/2002/12/cal#>;');
}


/**
 * Get the Redlink URL to delete a dataset data (doesn't delete the dataset itself).
 * @return string
 */
function rl_empty_dataset_url()
{

    // get the configuration.
    $dataset_id = wl_config_get_dataset();
    $app_key = wl_config_get_application_key();

    // construct the API URL.
    $url = sprintf('%s%s/data/%s?key=%s', WL_REDLINK_API_BASE_URI, WL_REDLINK_API_VERSION, $dataset_id, $app_key);
    return $url;
}

function rl_sparql_select_url()
{

    // get the configuration.
    $dataset_id = wl_config_get_dataset();
    $app_key = wl_config_get_application_key();

    // construct the API URL.
    $url = sprintf('%s%s/data/%s/sparql/select?key=%s', WL_REDLINK_API_BASE_URI, WL_REDLINK_API_VERSION, $dataset_id, $app_key);
    return $url;
}

/**
 * Empty the dataset bound to this WordPress install.
 * @return WP_Response|WP_Error A WP_Response in case of success, otherwise a WP_Error.
 */
function rl_empty_dataset()
{

    // Get the empty dataset URL.
    $url = rl_empty_dataset_url();

    // Prepare the request.
    $args = array_merge_recursive(unserialize(WL_REDLINK_API_HTTP_OPTIONS), array(
        'method' => 'DELETE'
    ));

    // Send the request.
    return wp_remote_request($url, $args);
}

/**
 * Count the number of triples in the dataset.
 * @return array|WP_Error|null An array if successful, otherwise WP_Error or NULL.
 */
function rl_count_triples()
{

    // Set the SPARQL query.
    $sparql = 'SELECT (COUNT(DISTINCT ?s) AS ?subjects) (COUNT(DISTINCT ?p) AS ?predicates) (COUNT(DISTINCT ?o) AS ?objects) ' .
        'WHERE { ?s ?p ?o }';

    // Send the request.
    $response = rl_sparql_select($sparql, 'text/csv');

    // Remove the key from the query.
    $scrambled_url = preg_replace('/key=.*$/i', 'key=<hidden>', rl_sparql_select_url());

    // Return the error in case of failure.
    if (is_wp_error($response) || 200 !== $response['response']['code']) {

        $body = (is_wp_error($response) ? $response->get_error_message() : $response['body']);

        write_log("rl_count_triples : error [ url :: $scrambled_url ][ response :: ");
        write_log("\n" . var_export($response, true));
        write_log("][ body :: ");
        write_log("\n" . $body);
        write_log("]");

        return $response;
    }

    // Get the body.
    $body = $response['body'];

    // Get the values.
    $matches = array();
    if (1 === preg_match('/(\d+),(\d+),(\d+)/im', $body, $matches) && 4 === count($matches)) {

        // Return the counts.
        return array(
            'subjects' => (int)$matches[1],
            'predicates' => (int)$matches[2],
            'objects' => (int)$matches[3]
        );
    }

    // No digits found in the response, return null.
    write_log("rl_count_triples : unrecognized response [ body :: $body ]");
    return null;
}

/**
 * Execute the provided query against the SPARQL SELECT Redlink end-point and return the response.
 * @param string $query A SPARQL query.
 * @param string $accept The mime type for the response format (default = 'text/csv').
 * @return WP_Response|WP_Error A WP_Response instance in successful otherwise a WP_Error.
 */
function rl_sparql_select($query, $accept = 'text/csv')
{

    // Get the SPARQL SELECT URL.
    $url = rl_sparql_select_url();

    // Prepare the SPARQL statement by prepending the default namespaces.
    $sparql = wordlift_get_ns_prefixes() . "\n" . $query;

    // Prepare the request.
    $args = array_merge_recursive(unserialize(WL_REDLINK_API_HTTP_OPTIONS), array(
        'headers' => array(
            'Accept' => $accept
        ),
        'body' => array(
            'query' => $sparql
        )
    ));

    // Send the request.
    return wp_remote_post($url, $args);
}

/**
 * Execute a query on Redlink.
 * @param string $query The query to execute.
 * @param bool $queue Whether to queue the update.
 * @return bool True if successful otherwise false.
 */
function rl_execute_sparql_update_query($query, $queue = WL_ENABLE_SPARQL_UPDATE_QUERIES_BUFFERING)
{

    write_log("rl_execute_sparql_update_query [ queue :: " . ($queue ? 'true' : 'false') . " ]");

    // Queue the update query.
    if ($queue) {
        return wl_queue_sparql_update_query($query);
    }

    // Get the update end-point.
    $url = wordlift_redlink_sparql_update_url();

    // Prepare the request.
    $args = array_merge_recursive(unserialize(WL_REDLINK_API_HTTP_OPTIONS), array(
        'method' => 'POST',
        'headers' => array(
            'Accept' => 'application/json',
            'Content-type' => 'application/sparql-update; charset=utf-8'
        ),
        'body' => $query
    ));

    // Send the request.
    $response = wp_remote_post($url, $args);

    // Remove the key from the query.
    $scrambled_url = preg_replace('/key=.*$/i', 'key=<hidden>', $url);

    // If an error has been raised, return the error.
    if (is_wp_error($response) || 200 !== $response['response']['code']) {

        $body = (is_wp_error($response) ? $response->get_error_message() : $response['body']);

        write_log("rl_execute_sparql_update_query : error [ url :: $scrambled_url ][ args :: ");
        write_log("\n" . var_export($args, true));
        write_log("[ response :: ");
        write_log("\n" . var_export($response, true));
        write_log("][ body :: ");
        write_log("\n" . $body);
        write_log("]");

        return false;
    }

    write_log("rl_execute_sparql_query [ url :: $scrambled_url ][ response code :: " . $response['response']['code'] . " ][ query :: ");
    write_log("\n" . $query);
    write_log("]");

    return true;
}

/**
 * Delete the specified post from relationships and from Redlink.
 * @param int $post_id The post ID.
 */
function rl_delete_post($post_id)
{

    write_log("rl_delete_post [ post id :: $post_id ]");

    // Remove all relations.

    // Delete post from RL.
    // Get the post URI.
    $uri = wordlift_esc_sparql(wl_get_entity_uri($post_id));

    // Create the SPARQL query, deleting triples where the URI is either subject or object.
    $sparql = wordlift_get_ns_prefixes();
    $sparql .= "DELETE { <$uri> ?p ?o . } WHERE { <$uri> ?p ?o . };";
    $sparql .= "DELETE { ?s ?p <$uri> . } WHERE { ?s ?p <$uri> . };";

    // Execute the query.
    rl_execute_sparql_update_query($sparql);
}

add_action('before_delete_post', 'rl_delete_post');