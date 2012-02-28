<?php
require_once('private/config/wordlift.php');
require_once('log4php.php');

require_once('classes/WordLiftSetup.php');
require_once('classes/EntityService.php');
require_once('classes/Entity.php');
require_once('classes/SlugService.php');

WordLiftSetup::init();

$logger = Logger::getLogger("complete.php");
$logger->debug("received job results: ".var_export(file_get_contents("php://input"),true));

$job_result = json_decode( file_get_contents("php://input") );

$posts = get_posts(array(
	'numberposts' => 1,
	'meta_key'    => POST_META_JOB_ID,
	'meta_value'  => $job_result->id
	));

$logger->debug("found post [id:".$posts[0]->ID."].");

$slugs = array();
foreach ($job_result->entities as $e) {
	$entity = $entity_service->create($e);
	$entity_post_id = $entity_service->save($entity);

	$slugs[] = $entity->slug;

	$logger->debug('An entity as been saved [entity_post_id:'.$entity_post_id.'].');
}

$logger->debug('Saving ['.count($slugs).'] entities to [entity_post_id:'.$entity_post_id.'].');
$result = wp_set_object_terms($posts[0]->ID, $slugs, WORDLIFT_20_TAXONOMY_NAME);

if ($result instanceof WP_Error)
	$logger->error('An error occurred: '.var_export($result, true));

?>