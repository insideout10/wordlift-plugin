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

    /*
     * array(
     'count' => 1,
     'relevance' => 0,
     'type' => 'Place',
     'reference' => 'http://dbpedia.org/resource/Pennsylvania_Station_%28New_York_City%29',
     'score' => NULL,
     'rank' => 0.47337243,
     'properties' =>
    stdClass::__set_state(array(
       'thumbnail' =>
      array (
        0 => 'http://upload.wikimedia.org/wikipedia/commons/thumb/4/41/Penn_Station_NYC_main_entrance.jpg/200px-Penn_Station_NYC_main_entrance.jpg',
      ),
       'subject' =>
      array (
        0 => 'http://dbpedia.org/resource/Category:Transit_hubs_serving_New_Jersey',
        1 => 'http://dbpedia.org/resource/Category:McKim,_Mead,_and_White_buildings',
        2 => 'http://dbpedia.org/resource/Category:Stations_along_New_York,_New_Haven_and_Hartford_Railroad_lines',
        3 => 'http://dbpedia.org/resource/Category:Beaux-Arts_architecture_in_New_York',
        4 => 'http://dbpedia.org/resource/Category:Former_buildings_and_structures_of_New_York_City',
        5 => 'http://dbpedia.org/resource/Category:Pennsylvania_Plaza',
        6 => 'http://dbpedia.org/resource/Category:Transit_centers_in_the_United_States',
        7 => 'http://dbpedia.org/resource/Category:Long_Island_Rail_Road_stations',
        8 => 'http://dbpedia.org/resource/Category:1910_architecture',
        9 => 'http://dbpedia.org/resource/Category:Demolished_railway_stations_in_the_United_States',
        10 => 'http://dbpedia.org/resource/Category:Transportation_in_Manhattan',
        11 => 'http://dbpedia.org/resource/Category:Stations_along_Pennsylvania_Railroad_lines',
        12 => 'http://dbpedia.org/resource/Category:Amtrak_stations_in_New_York',
        13 => 'http://dbpedia.org/resource/Category:Destroyed_landmarks_in_the_United_States',
        14 => 'http://dbpedia.org/resource/Category:New_Jersey_Transit_stations',
        15 => 'http://dbpedia.org/resource/Category:Railroad_terminals_in_New_York_City',
        16 => 'http://dbpedia.org/resource/Category:Railway_stations_opened_in_1910',
        17 => 'http://dbpedia.org/resource/Category:Union_stations_in_the_United_States',
      ),
       'description' =>
      array (
        0 => 'Pennsylvania Station — commonly known as Penn Station — is the major intercity train station and a major commuter rail hub in New York City. It is one of the busiest rail stations in the world, and a hub for inboard and outboard railroad traffic in New York City. The New York City Subway system also has multiple lines that connect to the station.',
      ),
       'longitude' =>
      array (
        0 => -73.9939,
      ),
       'label' =>
      array (
        0 => 'Pennsylvania Station',
        1 => 'Pennsylvania Station (New York City)',
        2 => 'New York',
      ),
       'latitude' =>
      array (
        0 => 40.750637,
      ),
    )),
     'text' => 'Pennsylvania Station',
  ))
     */

    public function complete( $requestBody ) {

        $jsonBody = json_decode( $requestBody );
        if ( !property_exists( $jsonBody, "id") )
            throw new Exception( "The request is missing the job id parameter." );

        $job = $this->jobService->getJobByUUID( $jsonBody->id );

        if ( NULL === $job )
            throw new Exception( "The job id [$jsonBody->id] does not exist." );

        $this->logger->trace("Received [" . count( $jsonBody->entities ) . "] entities for job [$job->id][post-id :: $job->postID].");

        $this->logger->trace( var_export( $jsonBody->entities, true ) );

        $this->jobService->markCompleted( $job );

    }

}

?>