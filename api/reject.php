<?php
require_once('../wordlift.php');


$post_id 	= $_GET['post_id'];
$entity_id 	= $_GET['entity_id'];

if (false == is_numeric($post_id) || false == is_numeric($entity_id)) {
	$logger->warn('The '.__FILE__.' end-point has been called with an invalid post_id or entity_id [post_id:'.$post_id.'][entity_id:'.$entity_id.']');

	return;
}

$entity_service->reject_entity_for_post($entity_id, $post_id);

?>