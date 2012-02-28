<?php
require_once('private/config/wordlift.php');
require_once('log4php.php');

$post_id = $_GET['id'];

if (false == is_numeric($post_id)) {
	$logger->warn('The entities.php end-point has been called with an invalid id [id:'.$post_id.']');

	return;
}

$entities = $entity_service->get_entities_by_post_id( $post_id );

echo json_encode($entities);

?>