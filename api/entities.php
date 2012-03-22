<?php
require_once('../wordlift.php');

$post_id = $_GET['id'];

// return all the entities.
if (NULL == $post_id) {

	$limit 		= (is_numeric( $_GET['limit'] ) 	? $_GET['limit'] 	: -1 );
	$offset 	= (is_numeric( $_GET['offset'] ) 	? $_GET['offset'] 	:  0 );
	$entities 	= $entity_service->get_all($limit, $offset);
	$entities_count = $entity_service->get_count();
	echo json_encode(array('total' => $entities_count, 'entities' => $entities));	

	return;
}

if (false == is_numeric($post_id)) {
	$logger->warn('The entities.php end-point has been called with an invalid id [id:'.$post_id.']');
	return;
}


// return the entities for a specific post ID.
$job 		= $job_service->get_job_by_post_id($post_id);
$entities 	= $entity_service->get_entities_by_post_id($post_id);

echo json_encode(array( 'job' => $job, 'entities' => $entities));

?>