<?php
/*
Plugin Name: WordLift 2.0
Plugin URI: http://wordlift.insideout.io
Description: WordLift 2.0
Version: 0.2.0
Author: InSideOut10
Author URI: http://www.insideout.io
License: APL
*/

require_once('private/config/wordlift.php');
require_once('log4php.php');

require_once('classes/WordLiftSetup.php');
require_once('classes/TextJobRequest.php');
require_once('classes/EnhancerJobService.php');
require_once('classes/WordLift.php');
require_once('classes/EntityService.php');
require_once('classes/SlugService.php');

function display_the_content($content){
	global $logger, $entity_service, $slug_service;

	$post = $GLOBALS['post'];

	// we only add entities to posts.
	if ('post' != $post->post_type) return $content;

	$logger->debug('display_the_content');

	$terms = get_the_terms( $post->ID, WORDLIFT_20_TAXONOMY_NAME, 'Entities: ', ' <span style="color:#000">/</span> ', '' );

	$slugs 		= $slug_service->get_slugs_by_terms( $terms );

	$logger->debug('Found ['.count($slugs).'] slugs.');

	$entities 	= $entity_service->get_entities_by_slugs( $slugs );

	$terms_body = 'Entities: ';
	foreach ($entities as $entity) {
		$fields		= get_post_custom($entity->ID);
		$type 		= $fields[POST_META_ENTITY_TYPE][0];
		$terms_body .= '<span itemscope itemtype="http://schema.org/'.$type.'">';
		$terms_body .= '<span itemprop="name">'.$entity->post_title.'</span>';
		$terms_body .= '</span> / ';
	}

	return $content . $terms_body . '<br/><br/>'; ;
}

function register_meta_box_cb(){
	global $logger;

	$logger->debug('register_meta_box_cb');
}

function custom_box($post) {
	global $logger;

	$logger->debug('custom_box');	

	echo 'Entities for post id '.$post->ID;
}

function create_custom_box() {
	add_meta_box( 
        'wordlift_20_entities',
        _x( 'Entities', 'Entities (Custom-Box Title)' ),
        'custom_box',
        'post' 
    );
}

add_action('init', 		array('wordliftsetup', 'setup'));

add_action('edit_post', array('wordlift', 'analyze_text'));

add_filter('the_content', 'display_the_content' );

add_action( 'add_meta_boxes', 'create_custom_box' );
?>