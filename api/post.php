<?php
require_once('../wordlift.php');

/**
 * This file acts as a end-point for third party calls. His functions:
 *  a) add an entity to a post given the entity ID and the post ID.
 */

if(false == current_user_can('edit_posts')){
	echo 'The current user cannot access this end-point because he/she lacks the \'edit_posts\' capability.';
	return;
}

// get the ID of the entity and of the post
$action 		= $_GET['action'];
$entity_post_id = $_GET['entity'];
$post_id 		= $_GET['post'];


/**
 * Binds and entity to a post.
 */
if ('bind-entity' == $action) {
	// bind the entity to the post.
	$entity_service = new EntityService();
	$entity_service->bind_entity_to_post($entity_post_id, $post_id);
	$entity_service->accept_entity_for_post($entity_post_id, $post_id);
	
	return;
}

/**
 * Binds and entity to a post.
 */
if ('set-bogus' == $action) {
	$bogus 	= ('true' == $_GET['bogus'] ? true : false);
	
	// bind the entity to the post.
	$entity_service = new EntityService();
	$entity_service->setEntityBogus($entity_post_id, $bogus);

	echo '{\'result\': \'success\'}';
	
	return;
}

?>