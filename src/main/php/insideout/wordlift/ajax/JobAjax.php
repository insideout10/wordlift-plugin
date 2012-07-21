<?php

class WordLift_JobAjax {

    public $logger;

    public $jobService;

    public function progress( $requestBody ) {
        $this->logger->trace( "A message has been received (" . strlen($requestBody) . " bytes)." );


//        // delete existing bindings.
//        $entity_service->unbind_all_entities_from_post($job->post_id);
//
//        $slugs = array();
//        foreach ($jobResult->entities as $e) {
//            $entity 	  = $entity_service->create($e);
//
//            if (null === $entity) {
//                $this->logger->warn("The entityService returned null instead of an entity, when trying to create an entity from an array of values.");
//                continue;
//            }
//
//            $this->logger->debug("A new entity [about:" . $entity->about . "] has been created.");
//
//            $entityPostId = $entity_service->save($entity);
//
//            $entity_service->bind_entity_to_post($entityPostId, $job->post_id);
//
//            $slugs[] 		= $entity->slug;
//
//            $this->logger->debug("An entity has been saved with [entityPostId:" . $entityPostId . "]");
//        }
//
//        $job->set_completed();
//        $job_service->save($job);

    }

    public function complete( $requestBody ) {

        $jsonBody = json_decode( $requestBody );
        if ( !property_exists( $jsonBody, "id") )
            throw new Exception( "The request is missing the job id parameter." );

        $job = $this->jobService->getJobByUUID( $jsonBody->id );

        if ( NULL === $job )
            throw new Exception( "The job id [$jsonBody->id] does not exist." );

        $this->logger->trace("Received [" . count( $jsonBody->entities ) . "] entities for job [$job->id][post-id :: $job->postID].");

        $this->jobService->markCompleted( $job );

    }

}

?>