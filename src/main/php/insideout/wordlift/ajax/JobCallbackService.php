<?php
/**
 * User: david
 * Date: 23/08/12 16:12
 */


class WordLift_JobCallbackService {

    public $logger;

    /** @var WordLift_EntityService $entityService */
    public $entityService;
    /** @var WordLift_JobService $jobService */
    public $jobService;

    public function callback( $jobID, $requestBody ) {
        $this->logger->trace( "A message has been received [ jobID :: $jobID ][ requestBody :: $requestBody ]." );

        // exit if the job ID does NOT exist.
        $posts = $this->jobService->getPostByJobID( $jobID );

        if ( 0 === count( $posts ) ) {
            $this->logger->error( "No job found for id [ jobID :: $jobID ]." );
            return;
        }

        $postID = $posts[0]->ID;
        $this->logger->trace( "A post was found [ postID :: $postID ][ jobID :: $jobID ]." );

        $parser = ARC2::getRDFParser();
        $parser->parseData( $requestBody );
        $triples = $parser->getTriples();


        /** @var ARC2_Store $store */
        $store = ARC2::getStore(array(
            "db_host" => DB_HOST,
            "db_name" => DB_NAME,
            "db_user" => DB_USER,
            "db_pwd" => DB_PASSWORD,
            "store_name" => "wordlift"
        ));

        if (!$store->isSetUp()) {
            $store->setUp();
        }

        $store->insert( $triples, "" );

        $this->logger->trace( count( $triples ) . " triple(s) found." );

        $index = $parser->getSimpleIndex(0);

        // list all the subjects.
        foreach ( $index as $subject => $predicates ) {
            $predicatesCount = count( $predicates );

            // check if the subject is blank node.
            $isBlankNode = ( 1 === preg_match( '/^\_:/', $subject ) );

            $this->logger->trace( "[ subject :: $subject ][ predicatesCount :: $predicatesCount ][ isBlankNode :: " . ( $isBlankNode ? "yes" : "no" ) . " ]." );

            if ( $isBlankNode ) continue;

            $posts = $this->entityService->getBySubject( $subject );
            $postsCount = count( $posts );

            $this->logger->trace( "$postsCount post(s) found with subject [ subject :: $subject ]." );

        // ##### E N T I T I E S #####
        // load the existing entity if the subject is not anonymous.

        // update the existing entity if found.

        // create a new entity if not found.

            if ( 0 === $postsCount )
                $this->entityService->create( $subject );

            $this->entityService->bindPostToSubjects( $postID, $subject );


        // ##### A N O N Y M O U S  E N T I T I E S #####
        // create a checksum for the anonymous entity.

        // load an existing entity by checksum.

        // create an entity by checksum if not found.

        // save the properties.

        // create a referenced entity if the property is a bnode reference.



            foreach ( $predicates as $predicate => $objects ) {
                $objectsCount = count( $objects );

                $this->logger->trace( "   [ predicate :: $predicate ][ objectsCount :: $objectsCount ]" );

                foreach ( $objects as $object ) {
                    $type = $object[ "type" ];
                    $value = $object[ "value" ];

                    $this->logger->trace( "      [ value :: $value ][ type :: $type ]" );
                }
            }
        }
    }

}

?>