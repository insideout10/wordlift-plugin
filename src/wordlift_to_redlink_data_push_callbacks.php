<?php

add_action('save_post', 'wordlift_on_post_save_callback');
add_action('save_post', 'wordlift_update_entity_on_post_save_callback');

/**

 **/
function wordlift_update_entity_on_post_save_callback($post_id) {
	// If current post does not stay for an entity, nothing to do!
    if ('entity' != $post->post_type) {
        return true;
    } 

}
/**

 **/
function wordlift_on_post_save_callback($post_id) {
   write_log("Going to update post with ID ".$post_id);
 
   
    $client_id = 353;
    $dataset_id = 'wordlift';
    $post = get_post($post_id); 

    $redlink_post_url = "http://data.redlink.io/$client_id/$dataset_id/post/$post->ID";
    $sparql  = "\n<$redlink_post_url> rdfs:label '".$post->post_title."'."; 
    $sparql .= "\n<$redlink_post_url> a <http://schema.org/BlogPosting>."; 
    $sparql .= "\n<$redlink_post_url> schema:url <".get_permalink($post->ID).">."; 
    
    // Retrieve the post content and try to parse it
    $source = ($post->post_content) ? $post->post_content : '';
    $doc = new DOMDocument();
    $doc->loadHTML($source);
    // Find all span tags: a span tag could be a textAnnotation
    $tags = $doc->getElementsByTagName('span');
    // Loops on founded span tags
    foreach ($tags as $tag) {
        // If itemid attribute is set, then the node is a textAnnotation
    	if ($tag->attributes->getNamedItem('itemid')) {
    		
            $entity_attributes = array(
                'label' => addslashes($tag->nodeValue),
                'sameas' => $tag->attributes->getNamedItem('itemid')->value,
                'redlink_entity_url' => "http://data.redlink.io/$client_id/$dataset_id/resource/".end(explode('/', $tag->attributes->getNamedItem('itemid')->value)),                
                );
    		if($tag->attributes->getNamedItem('itemtype')) {
                $entity_attributes['type'] = $tag->attributes->getNamedItem('itemtype')->value;
            }
             
    		$sparql .= "\n\t<$redlink_post_url> dcterms:references <{$entity_attributes['redlink_entity_url']}>."; 
    		$sparql .= "\n\t<{$entity_attributes['redlink_entity_url']}> rdfs:label '{$entity_attributes['label']}."; 
            // Support type are only schema.org ones: it could by null
            if($entity_attributes['type']) {
                $sparql .= "\n\t<{$entity_attributes['redlink_entity_url']}> a <{$entity_attributes['type']}>.";  		
            }
            $sparql .= "\n\t<{$entity_attributes['redlink_entity_url']}> owl:sameAs <{$entity_attributes['sameas']}>.";

            add_or_update_related_entity_post($entity_attributes); 
    						
    	}

    }

    $sparql_query = <<<EOT
PREFIX dcterms: <http://purl.org/dc/terms/>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX owl: <http://www.w3.org/2002/07/owl#>
PREFIX schema: <http://schema.org/>

INSERT DATA

{
	$sparql
}
EOT;

    $sparql_delete_query = <<<EOT
PREFIX dcterms: <http://purl.org/dc/terms/>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX owl: <http://www.w3.org/2002/07/owl#>
PREFIX schema: <http://schema.org/>

DELETE WHERE {
    <{$redlink_post_url}> dcterms:references ?ref
}
EOT;
    wordlift_push_data_triple_store($sparql_delete_query);
    wordlift_push_data_triple_store($sparql_query);

}
function add_or_update_related_entity_post($attributes) {
    write_log($attributes);
    $params = array(
        'post_status' => 'draft',
        'post_type' => 'entity',
        'post_title' => $attributes['label'],
        'post_content' => '',
        'post_excerpt' => '', 
    );
    
    // Check if entity exists on wordpress database
    $the_query = new WP_Query( array(
        'numberposts' => 1,
        'post_type' => 'entity',
        'meta_key' => 'entity_url',
        'meta_value' => $attributes['redlink_entity_url']
        ) 
    );
    // If entity exists, adds entity ID to params:
    // wp_insert_post try to update an existing post if ID is specified
    if ($the_query->post_count > 0) {
       $posts = $the_query->get_posts(); 
       $entity = $posts[0];
       $params['ID'] = $entity->ID; 
    }
    // If type is defined, specifies entity_type taxonomy for the post

    if($attributes['type']) {
        $toxonomized_type = end(explode('/', $attributes['type']));
        $params['tax_input'] = array( 
            'entity_type' => array( $toxonomized_type )
            );
    }
    // Create or update the post
    $post_id = wp_insert_post($params, false);
    // On success, add custom fields values
    if ($post_id > 0) { 
        update_post_meta( $post_id, 'entity_url', $attributes['redlink_entity_url'] );
        update_post_meta( $post_id, 'entity_sameas', $attributes['sameas'] );
    }

    return true; 
         
}

function wordlift_push_data_triple_store($sparql_query) {

    $api_key = '5VnRvvkRyWCN5IWUPhrH7ahXfGCBV8N0197dbccf';
    $api_analysis_chain = 'wordlift';
    $api_url = "https://api.redlink.io/1.0-ALPHA/data/$api_analysis_chain/sparql/update?key=$api_key";

    $response = wp_remote_post($api_url, array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(
                'Content-type' => 'application/sparql-update',
            ),
            'body' => $sparql_query,
            'cookies' => array()
        )
    );

    if ( is_wp_error( $response ) ) {
        write_log("Something went wrong with sparql query\n\n$sparql_query\n\n$error_message");
        return false;
    } else {
        write_log("Sparql query done!!\n\n{$sparql_query}");
    }
    return true;
}
