<?php

/**
 * @requires WordPressFramework
 */
class JobServices {

	private $logger;

	function __construct() {
		$this->logger 		= Logger::getLogger(__CLASS__);
	}

    /**
     * @service ajax
     * @action wordlift.job-complete
     * @authentication none
     */
    public function jobComplete() {
        
        global $job_service, $entity_service;
        
        $jobResult = json_decode( file_get_contents("php://input") );
        $job 	   = $job_service->get_job_by_id($jobResult->id);

        if (null === $jobResult->id) {
            $this->logger->error("The call to job-complete is missing the job id.");

            echo "No job id provided.";
            return AjaxService::CALLBACK_RETURN_ERROR;
        }

        if (null === $job) {
            $this->logger->error("Cannot find a job with [id:" . $jobResult->id . "].");

            echo "Cannot find a job with [id:" . $jobResult->id . "].";
            return AjaxService::CALLBACK_RETURN_ERROR;
        }

        if (null === $entity_service) {
            $this->logger->error("The entityService is null.");

            echo "The entityService is null.";
            return AjaxService::CALLBACK_RETURN_ERROR;
        }

        $this->logger->debug("Received [" . sizeof($jobResult->entities) . "] entities for [jobResult->Id:" . $jobResult->id . "].");        

        // delete existing bindings.
        $entity_service->unbind_all_entities_from_post($job->post_id);

        $slugs = array();
        foreach ($jobResult->entities as $e) {
        	$entity 	  = $entity_service->create($e);
        	
        	if (null === $entity) {
        	    $this->logger->warn("The entityService returned null instead of an entity, when trying to create an entity from an array of values.");
        	    continue;
        	}
        	
        	$this->logger->debug("A new entity [about:" . $entity->about . "] has been created.");

        	$entityPostId = $entity_service->save($entity);

        	$entity_service->bind_entity_to_post($entityPostId, $job->post_id);

        	$slugs[] 		= $entity->slug;

        	$this->logger->debug("An entity has been saved with [entityPostId:" . $entityPostId . "]");
        }

        $job->set_completed();
        $job_service->save($job);
        
    }
}

?>