<?php
/*
Plugin Name: WordLift
Plugin URI: http://wordlift.it
Description: Supercharge your WordPress Site with Smart Tagging and #Schemaorg support - a brand new way to write, organise and publish your contents to the Linked Data Cloud.
Version: 3.0.0-SNAPSHOT
Author: InSideOut10
Author URI: http://www.insideout.io
License: APL
*/

/**
 * Get the URL of the specified physical file.
 * @param string $file The path to the file from the plugin root folder.
 * @return string The URL to the file.
 */
function wordlift_get_url($file)
{

    // if WordLift is set into development mode, then provide a static URL, as development is done with symbolic link.
    if (defined('WORDLIFT_DEVELOPMENT')) {
        return '/wp-content/plugins/wordlift' . $file;
    }

    // use standard WP methods in production mode.
    return plugins_url($file, __FILE__);
}

/**
 * Load stylesheets for the administrative interface.
 */
function wordlift_load_admin_css()
{
    wp_register_style('wordlift_wp_admin_css', wordlift_get_url('/css/wordlift-admin.min.css'), false, '1.0.0');
    wp_enqueue_style('wordlift_wp_admin_css');
}

add_action('admin_enqueue_scripts', 'wordlift_load_admin_css');

/**
 * Add buttons hook for the TinyMCE editor. This method is called by the WP init hook.
 */
function wordlift_buttonhooks()
{

    // Only add hooks when the current user has permissions AND is in Rich Text editor mode
    if ((current_user_can('edit_posts') || current_user_can('edit_pages')) && get_user_option('rich_editing')) {
        add_filter('mce_external_plugins', 'wordlift_register_tinymce_javascript');
        add_filter('mce_buttons',          'wordlift_register_buttons');
    }
}

/**
 * Register the TinyMCE buttons. This method is called by the WP mce_buttons hook.
 * @param array $buttons The existing buttons array.
 * @return array The modified buttons array.
 */
function wordlift_register_buttons($buttons)
{
    // push the wordlift button the array.
    array_push($buttons, 'wordlift');
    return $buttons;
}

/**
 * Load the TinyMCE plugin. This method is called by the WP mce_external_plugins hook.
 * @param array $plugin_array The existing plugins array.
 * @return array The modified plugins array.
 */
function wordlift_register_tinymce_javascript($plugin_array)
{
    // add the wordlift plugin.
    $plugin_array['wordlift'] = wordlift_get_url('/js/wordlift-tinymce-plugin.min.js');
    return $plugin_array;
}

// init process for button control
add_action('init', 'wordlift_buttonhooks');


// Ajax Admin Section

add_action('wp_ajax_wordlift_analyze', 'wordlift_ajax_analyze_action');

// Analyze a text
function wordlift_ajax_analyze_action()
{
    if ((current_user_can('edit_posts') || current_user_can('edit_pages')) && get_user_option('rich_editing')) {

        global $wpdb; // this is how you get access to the database
        
            $api_key = '5VnRvvkRyWCN5IWUPhrH7ahXfGCBV8N0197dbccf';
            $api_analysis_chain = 'wordlift';
            $api_url = "https://api.redlink.io/1.0-ALPHA/analysis/$api_analysis_chain/enhance?key=$api_key";

            $response = wp_remote_post($api_url, array(
                    'method' => 'POST',
                    'timeout' => 45,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' => array(
                    	'Accept' => 'application/json',
                    	'Content-type' => 'text/plain',
                    	),
                    'body' => file_get_contents("php://input"),
                    'cookies' => array()
                )
            );

        if ( is_wp_error( $response ) ) {
           $error_message = $response->get_error_message();
           echo "Something went wrong: $error_message";
           die();
        } else {
           echo $response['body'];
           die();
        }
        



    }
}


/**
* Callback on post save
*/


if (!function_exists('write_log')) {
    function write_log ( $log )  {
        if ( true === WP_DEBUG ) {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
        }
    }
}

add_action('save_post', 'wordlift_save_post');

/**
PREFIX dcterms: <http://purl.org/dc/terms/>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX owl: <http://www.w3.org/2002/07/owl#>
PREFIX schema: <http://schema.org/>

INSERT DATA
{ 
  <http://data.redlink.io/353/wordlift/p:1> rdfs:label 'Power Vacuum in Middle East Lifts Militants';
  											a <http://schema.org/BlogPosting>;
  											schema:url <http://wordpress380.localhost/power-vacuum-in-middle-east-lifts-militants>;
                                            dcterms:references <http://data.redlink.io/353/wordlift/e:Al-Qaeda> 
                                            .
  <http://data.redlink.io/353/wordlift/e:Al-Qaeda> rdfs:label 'Al Qaeda';
                                            a <http://schema.org/Organization>;
                                            owl:sameAs <http://dbpedia.org/resource/Al-Qaeda>
}
**/
function wordlift_save_post($post_id) {
    
    write_log("Going to update post with ID".$post_id);
    
    $client_id = 353;
    $dataset_id = 'wordlift';
    $post = get_post($post_id); 
    
    $sparql  = "\n<http://data.redlink.io/$client_id/$dataset_id/post/$post->ID> rdfs:label '".$post->post_title."'."; 
    $sparql .= "\n<http://data.redlink.io/$client_id/$dataset_id/post/$post->ID> a <http://schema.org/BlogPosting>."; 
    $sparql .= "\n<http://data.redlink.io/$client_id/$dataset_id/post/$post->ID> schema:url <".get_permalink($post->ID).">."; 
    
    $doc = new DOMDocument();
    $doc->loadHTML($post->post_content);
    $tags = $doc->getElementsByTagName('span');
    foreach ($tags as $tag) {
    	if ($tag->attributes->getNamedItem('itemid')) {
    		
    		$label = $tag->nodeValue;
    		$entity_slug = str_replace(' ', '_', $label);
    		$same_as = $tag->attributes->getNamedItem('itemid')->value;
    		$type = $tag->attributes->getNamedItem('itemtype')->value;  
            $toxonomized_type = end(explode('/', $type));
               
               // args
    $args = array(
        'numberposts' => 1,
        'post_type' => 'entity',
        'meta_key' => 'entity_url',
        'meta_value' => "http://data.redlink.io/$client_id/$dataset_id/resource/$entity_slug"
    );
 
    // get results
    $the_query = new WP_Query( $args );
    $params = array(
                'post_name' => $entity_slug,
                'post_status' => 'draft',
                'post_type' => 'entity',
                'post_title' => $label,
                'post_content' => '',
                'post_excerpt' => '',
                'entity_url' => '',
                'tax_input' => array( 'entity_type' => array( $toxonomized_type ) ) , 
                );
       
    if ($the_query->post_count > 0) {
       $posts = $the_query->get_posts(); 
       $entity = $posts[0];
       $params['ID'] = $entity->ID; 
    }
               // Push entity on wordpress side as a custom type post
               $new_post_id = wp_insert_post($params, false);
               if ($new_post_id > 0) { 
                    update_post_meta( $new_post_id, 'entity_url', "http://data.redlink.io/$client_id/$dataset_id/resource/$entity_slug" );
                    update_post_meta( $new_post_id, 'entity_sameas', $same_as );
                } 
            write_log();
    		$sparql .= "\n\t";
    		$sparql .= "\n\t<http://data.redlink.io/$client_id/$dataset_id/post/$post->ID> dcterms:references <http://data.redlink.io/$client_id/$dataset_id/resource/$entity_slug>."; 
    		$sparql .= "\n\t<http://data.redlink.io/$client_id/$dataset_id/resource/$entity_slug> rdfs:label '".$label."'."; 
    		$sparql .= "\n\t<http://data.redlink.io/$client_id/$dataset_id/resource/$entity_slug> a <$type>."; 
    		$sparql .= "\n\t<http://data.redlink.io/$client_id/$dataset_id/resource/$entity_slug> owl:sameAs <$same_as>."; 
    						
    	}
    }


    $sparql_delete_query = <<<EOT
PREFIX dcterms: <http://purl.org/dc/terms/>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX owl: <http://www.w3.org/2002/07/owl#>
PREFIX schema: <http://schema.org/>

DELETE WHERE {
	<http://data.redlink.io/$client_id/$dataset_id/post/$post->ID> dcterms:references ?ref
}
EOT;

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
		
		    write_log($sparql_query);

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
                    'body' => $sparql_delete_query,
                    'cookies' => array()
                )
            );

	     if ( is_wp_error( $response ) ) {
           write_log("Something went wrong: $error_message");
        } else {
           write_log("Yo");
        }  

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
           write_log("Something went wrong: $error_message");
        } else {
           write_log("Yo");
        }   


}
/**
 * Register additional scripts for the admin UI.
 */
function wordlift_admin_enqueue_scripts() {
    global $post;
    wp_enqueue_script( 'angularjs', wordlift_get_url('/bower_components/angular/angular.min.js') );
    wp_localize_script('angularjs', 'thePost', get_post($post->id, ARRAY_A));
}
add_action('admin_enqueue_scripts', 'wordlift_admin_enqueue_scripts');

// add editor related methods.
require_once('wordlift_editor.php');
// add configuratiokn-related methods.
require_once('wordlift_configuration.php');
// add the WordLift admin bar.
require_once('wordlift_admin_bar.php');
// add the WordLift admin menu. - the entity admin menu is handled as a custom post type.
//require_once('wordlift_admin_menu.php');
// add the WordLift entity custom type.
require_once('wordlift_entity_custom_type.php');
// filters the post content when saving posts.
require_once('wordlift_content_filter.php');

// load languages.
// TODO: the following call gives for granted that the plugin is in the wordlift directory,
//       we're currently doing this because wordlift is symbolic linked.
load_plugin_textdomain('wordlift', false, '/wordlift/languages' );
