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
    /** @var WordLift_TripleStoreService $tripleStoreService */
    public $tripleStoreService;

    public function callback( $headers, $requestBody ) {

        $jobID = $headers[ "Proxy-Transaction-Id" ];
        $contentItemURI = $headers[ "Content-Item-Id" ];
        $this->logger->trace( "A message has been received [ jobId :: $jobID ][ contentItemURI :: $contentItemURI ][ requestBody :: $requestBody ]." );

        // get the posts for the specified job ID.
        $posts = $this->jobService->getPostByJobID( $jobID );

        // exit if the job ID does NOT exist.
        if ( 0 === count( $posts ) ) {
            $this->logger->error( "No job found for id [ jobID :: $jobID ][ posts :: " . var_export( $posts, true ) . " ]." );
            return;
        }

        // get the post ID.
        $postID = $posts[0]->ID;
        $this->logger->trace( "A post was found [ postID :: $postID ][ jobID :: $jobID ]." );

        // get a parser.
        $parser = $this->tripleStoreService->getRDFParser();
        $parser->parseData( $requestBody );
        $triples = $parser->getTriples();
        $this->logger->trace( count( $triples ) . " triple(s) found." );

        $this->logger->trace( "Removing existing enhancements [ postID :: $postID ]." );
        $this->tripleStoreService->query( "DELETE { ?s ?p ?o }
                        WHERE {
                            ?s a fise:Enhancement .
                            ?s wordlift:postID \"$postID\" .
                            ?s ?p ?o .
                        }" );


        $this->logger->trace( "Inserting new triples [ postID :: $postID ]." );
        $store = $this->tripleStoreService->getStore();
        $store->insert( $triples, "" );
        if ( $store->getErrors() ) {
            $this->logger->error( var_export( $store->getErrors(), true ) );
            return;
        }

        $this->logger->trace( "Setting the postID on the enhancements [ postID :: $postID ]." );
        $this->tripleStoreService->query( "INSERT INTO <> { ?subject wordlift:postID \"$postID\" }
                        WHERE { ?subject a fise:Enhancement .
                                ?subject fise:extracted-from $contentItemURI }" );

        $this->logger->trace( "Setting the job to completed [ postID :: $postID ][ jobID :: $jobID ]." );
        $this->jobService->setJob( $postID, $jobID, WordLift_JobService::COMPLETED );

    }

}

?>