<?php

/**
 * Create triples about the entity being saved.
 * @param int $post_id The post ID (posts that are not entities are ignored).
 * @return null
 */
function wordlift_update_entity($post_id) {

    // don't do anything if it's an autosave.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;

    // get the post.
    $post = get_post($post_id);

    // get the entity label.
    $title  = addslashes($post->post_title);
    // get the entity URL.
    $url    = get_post_meta( $post->ID, 'entity_url', true );
    // get the entity 'same as' URLs.
    $sameas = get_post_meta( $post->ID, 'entity_sameas', true );
    // get the description.
    $descr  = addslashes(strip_tags($post->post_content));
    // get the types.
    $terms  = wp_get_post_terms( $post->ID, 'entity_type' );
    // get the post link.
    $link   = get_permalink($post->ID);

    // build the sparql query.
    $sparql  = "<{$url}> rdfs:label '{$title}' ; ";
    foreach ($terms as $term) {
    	$sparql .= " rdf:type <http://schema.org/{$term->name}> ; ";
    }
    $sparql .= "<http://schema.org/url> <$link> ; ";
 	$sparql .= "<http://schema.org/description> '{$descr}' . ";

    // get the ns prefixes.
    $ns     = wordlift_get_ns_prefixes();

    $query = $ns . <<<EOF
    DELETE {
        <$url> rdfs:label ?o ;
            schema:url ?o ;
            schema:description ?o ;
            a ?o
     }
     INSERT { $sparql }
     WHERE { OPTIONAL { <$url> ?p ?o } }
EOF;

    wordlift_push_data_triple_store($query);
}

/**
 * Get a string representing the NS prefixes for a SPARQL query.
 * @return string The PREFIX lines.
 */
function wordlift_get_ns_prefixes() {

    return <<<EOF
PREFIX dcterms: <http://purl.org/dc/terms/>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX owl: <http://www.w3.org/2002/07/owl#>
PREFIX schema: <http://schema.org/>

EOF;

}

/**
 * @param $post_id
 */
function wordlift_update_post($post_id) {

    // ignore autosaves
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;

    // read the user id and dataset name from the options.
    $user_id    = wordlift_configuration_user_id();
    $dataset_id = wordlift_configuration_dataset_id();

    // get the current post.
    $post = get_post($post_id); 

    // set the post URI in the triple store.
    $post_uri = "http://data.redlink.io/$user_id/$dataset_id/post/$post->ID";

    // create the SPARQL query.
    $sparql  = "<$post_uri> rdfs:label '" . wordlift_esc_sparql($post->post_title) . "' . ";
    $sparql .= "<$post_uri> a          <http://schema.org/BlogPosting> . ";
    $sparql .= "<$post_uri> schema:url <" . wordlift_esc_sparql(get_permalink($post->ID)) . "> . ";
    
    // Retrieve the post content and try to parse it
    $source = ($post->post_content) ? $post->post_content : '';
    $doc    = new DOMDocument();
    $doc->loadHTML($source);
    // Find all span tags: a span tag could be a textAnnotation
    $tags   = $doc->getElementsByTagName('span');

    // Loops on founded span tags
    foreach ($tags as $tag) {
        // If itemid attribute is set, then the node is a textAnnotation
    	if ($tag->attributes->getNamedItem('itemid')) {

            // the item id is the URL to the entity in DBpedia or Freebase.
            $item_id   = $tag->attributes->getNamedItem('itemid')->value;
            // the name is the final fragment of the URL.
            $fragments = explode('/', $item_id); // this intermediate line is to avoid a 'Strict Standards' error.
            $item_name = end($fragments);

            $entity_attributes = array(
                'label' => addslashes($tag->nodeValue),
                'sameas' => $tag->attributes->getNamedItem('itemid')->value,
                'redlink_entity_url' => "http://data.redlink.io/$user_id/$dataset_id/resource/$item_name",
            );

            // set the type only if available.
    		if ($tag->attributes->getNamedItem('itemtype')) {
                $entity_attributes['type'] = $tag->attributes->getNamedItem('itemtype')->value;
            }

            // create references to the entity data.
            $entity_uri     = $entity_attributes['redlink_entity_url'];
            $entity_label   = $entity_attributes['label'];
            $entity_same_as = $entity_attributes['sameas'];
            $entity_type    = $entity_attributes['type'];

            // create the sparql query.
    		$sparql         .= "<$post_uri>   dcterms:references <$entity_uri> . ";
            $sparql         .= "<$entity_uri> owl:sameAs         <$entity_same_as> . ";
    		$sparql         .= "<$entity_uri> rdfs:label         '" . wordlift_esc_sparql($entity_label) . "' . ";

            // Support type are only schema.org ones: it could by null
            if(!empty($entity_type)) {
                $sparql .= "<$entity_uri>     a <$entity_type>.";
            }

            add_or_update_related_entity_post($entity_attributes); 
    						
    	}

    }

    // create the query:
    //  - remove existing references to entities.
    //  - set the new post information (including references).
    $query = wordlift_get_ns_prefixes() . <<<EOF
            DELETE {
                <{$post_uri}> dcterms:references ?o .
                <{$post_uri}> schema:url         ?o .
                <{$post_uri}> a                  ?o .
                <{$post_uri}> rdfs:label         ?o .
            }
            INSERT { $sparql }
            WHERE { OPTIONAL { <$post_uri> ?p ?o } }
EOF;

    // execute the query.
    wordlift_push_data_triple_store($query);
}

/**
 * Escape a sparql literal.
 * @param string $string The string to escape.
 * @return string The escaped string.
 */
function wordlift_esc_sparql($string) {
    // see http://www.w3.org/TR/rdf-sparql-query/
    //    '\t'	U+0009 (tab)
    //    '\n'	U+000A (line feed)
    //    '\r'	U+000D (carriage return)
    //    '\b'	U+0008 (backspace)
    //    '\f'	U+000C (form feed)
    //    '\"'	U+0022 (quotation mark, double quote mark)
    //    "\'"	U+0027 (apostrophe-quote, single quote mark)
    //    '\\'	U+005C (backslash)

    $string = str_replace('\\', '\\\\', $string);
    $string = str_replace('\'', '\\\'', $string);
    $string = str_replace('"', '\\"', $string);
    $string = str_replace('\f', '\\f', $string);
    $string = str_replace('\b', '\\b', $string);
    $string = str_replace('\r', '\\r', $string);
    $string = str_replace('\n', '\\n', $string);
    $string = str_replace('\t', '\\t', $string);

    return $string;
}

function add_or_update_related_entity_post($attributes) {
    write_log($attributes);
    $params = array(
        'post_status'  => 'draft',
        'post_type'    => 'entity',
        'post_title'   => $attributes['label'],
        'post_content' => '',
        'post_excerpt' => '', 
    );
    
    // Check if entity exists on wordpress database
    // Check is on entity url ...
    // TODO We shoud consider both entity url and entity sameas collection
    $the_query = new WP_Query( array(
        'numberposts' => 1,
        'post_type'   => 'entity',
        'meta_key'    => 'entity_url',
        'meta_value'  => $attributes['redlink_entity_url']
        ) 
    );
    // If entity exists, adds entity ID to params:
    // wp_insert_post try to update an existing post if ID is specified
    if ($the_query->post_count > 0) {
       $posts        = $the_query->get_posts();
       $entity       = $posts[0];
       $params['ID'] = $entity->ID; 
    }
    // If type is defined, specifies entity_type taxonomy for the post

    if($attributes['type']) {
        $toxonomized_type = end(explode('/', $attributes['type']));
        $params['tax_input'] = array( 
            'entity_type' => array( $toxonomized_type )
            );
    }

    // remove the save post hook to avoid processing for entities being saved (see http://codex.wordpress.org/Plugin_API/Action_Reference/save_post).
    remove_action('save_post', 'wordlift_save_post');

    // create or update the post.
    $post_id = wp_insert_post($params, false);

    // on success, add custom fields values
    if ($post_id > 0) {
        update_post_meta( $post_id, 'entity_url'   , $attributes['redlink_entity_url'] );
        update_post_meta( $post_id, 'entity_sameas', $attributes['sameas'] );
    }

    // add back the save post hook.
    add_action('save_post', 'wordlift_save_post');

    return true;
}

/**
 * Execute the query against the triple store.
 * @param string $query A SPARQL query.
 * @return bool
 */
function wordlift_push_data_triple_store($query) {

    // get the configuration.
    $api_version = '1.0-ALPHA';
    $dataset_id  = wordlift_configuration_dataset_id();
    $app_key     = wordlift_configuration_application_key();

    // construct the API URL.
    $api_url = "https://api.redlink.io/$api_version/data/$dataset_id/sparql/update?key=$app_key";

    // post the request.
    $response = wp_remote_post($api_url, array(
            'method'      => 'POST',
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true, // do we need blocking?
            'headers'     => array(
                'Content-type' => 'application/sparql-update',
            ),
            'body' => $query,
            'cookies'     => array()
        )
    );

    write_log('== API URL      ===============');
    write_log($api_url);
    write_log('== SPARQL QUERY ===============');
    write_log($query);
    write_log('===============================');
    write_log(var_export($response, true));

    // TODO: handle errors.
//    if ( is_wp_error( $response ) ) {
//        write_log("Something went wrong with sparql query\n\n$sparql_query\n\n$error_message");
//        return false;
//    } else {
//        write_log("Sparql query done!!\n\n{$sparql_query}");
//    }
    return true;
}

/**
 * Receive events from post saves, and split them according to the post type.
 * @param int $post_id The post id.
 */
function wordlift_save_post($post_id) {

    // get the post.
    $post = get_post($post_id);

    // if it's an entity, raise the *wordlift_save_entity* event.
    if ('entity' === $post->post_type) {
        do_action('wordlift_save_entity', $post_id);
    } else {
        // raise the *wordlift_save_post* event.
        do_action('wordlift_save_post', $post_id);
    }

}

// hook save events.
add_action('save_post', 'wordlift_save_post');
add_action('wordlift_save_post', 'wordlift_update_post');
add_action('wordlift_save_entity', 'wordlift_update_entity');


