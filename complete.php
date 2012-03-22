<?php
require_once('private/config/wordlift.php');
require_once('log4php.php');

require_once('classes/WordLiftSetup.php');
require_once('classes/EntityService.php');
require_once('classes/Entity.php');
require_once('classes/SlugService.php');
require_once('classes/JobService.php');

WordLiftSetup::setup();

$logger 	= Logger::getLogger("complete.php");
// $logger->debug("received job results: ".var_export(file_get_contents("php://input"),true));

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

// $logger->debug('Saving ['.count($slugs).'] entities to [entity_post_id:'.$entity_post_id.'].');
// $result = wp_set_object_terms($job->post_id, $slugs, WORDLIFT_20_TAXONOMY_NAME);

// if ($result instanceof WP_Error)
// 	$logger->error('An error occurred: '.var_export($result, true));


$job->set_completed();
$job_service->save($job);

?>