<?php
require_once('private/config/wordlift.php');
require_once('log4php.php');

require_once('classes/EntityService.php');

$post_id 	= $_GET['post_id'];
$entity_id 	= $_GET['entity_id'];

if (false == is_numeric($post_id) || false == is_numeric($entity_id)) {
	$logger->warn('The accept.php end-point has been called with an invalid post_id or entity_id [post_id:'.$post_id.'][entity_id:'.$entity_id.']');

	header("HTTP/1.0 400 Bad Request");
	return;
}

$entity_service->accept_entity_for_post($entity_id, $post_id);

?>