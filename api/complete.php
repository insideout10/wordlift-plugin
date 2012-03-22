<?php
require_once('../wordlift.php');

WordLiftSetup::setup();

$logger 	= Logger::getLogger("complete.php");
$logger->debug('Receiving Job results.');

$job_result = json_decode( file_get_contents("php://input") );

$job 		= $job_service->get_job_by_id($job_result->id);

// delete existing bindings.
$entity_service->unbind_all_entities_from_post($job->post_id);

$slugs = array();
foreach ($job_result->entities as $e) {
	$entity 		= $entity_service->create($e);
	$entity_post_id = $entity_service->save($entity);

	$entity_service->bind_entity_to_post($entity_post_id, $job->post_id);

	$slugs[] 		= $entity->slug;

	// $logger->debug('An entity as been saved [entity_post_id:'.$entity_post_id.'].');
}

$job->set_completed();
$job_service->save($job);

?>