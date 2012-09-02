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

    public function callback( $jobID, $contentItemURI, $requestBody ) {

        $this->logger->trace( "A message has been received [ jobID :: $jobID ][ contentItemURI :: $contentItemURI ][ requestBody :: $requestBody ]." );

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

    public function injectTextAnnotations( $postID, $requestBody ) {

        // get the posts for the specified job ID.
        $post = get_post( $postID );
        $content = &$post->post_content;

        $parser = $this->tripleStoreService->getRDFParser();
        $parser->parseData( $requestBody );
        $subjects = $parser->getSimpleIndex();

        $textAnnotations = array();
        foreach ( $subjects as $about => $subject ) {
            if ( array_key_exists( "http://www.w3.org/1999/02/22-rdf-syntax-ns#type", $subject )
                && in_array( "http://fise.iks-project.eu/ontology/TextAnnotation", $subject[ "http://www.w3.org/1999/02/22-rdf-syntax-ns#type" ] )
                && array_key_exists( "http://fise.iks-project.eu/ontology/selection-head", $subject )
                && array_key_exists( "http://fise.iks-project.eu/ontology/selection-prefix", $subject )
                && array_key_exists( "http://fise.iks-project.eu/ontology/selection-suffix", $subject )
                && array_key_exists( "http://fise.iks-project.eu/ontology/selection-tail", $subject ) ) {

                $textAnnotations[] = $subject;

                $selectionHead = $subject[ "http://fise.iks-project.eu/ontology/selection-head" ][0];
                $selectionHead = ( "" === $selectionHead ? "^" : $selectionHead );
                $selectionTail = $subject[ "http://fise.iks-project.eu/ontology/selection-tail" ][0];
                $selectionTail = ( "" === $selectionTail ? "$" : $selectionTail );

                $selectionPrefix = $subject[ "http://fise.iks-project.eu/ontology/selection-prefix" ][0];
                $selectionSuffix = $subject[ "http://fise.iks-project.eu/ontology/selection-suffix" ][0];

                $content = preg_replace( "/($selectionHead)(.*)($selectionTail)/", "$1<span about=\"$about\">$2</span>$3", $content );
            }
        }

        echo $content;
//        var_export( $textAnnotations );


    }

//    private function bindPostToSubjects( $postID, $subject, $predicates ) {
//        $this->logger->trace( "Binding the post to the subject [ postID :: $postID ][ subject :: $subject ]." );
//
//        $predicatesCount = count( $predicates );
//
//        // check if the subject is blank node.
//        $isBlankNode = ( 1 === preg_match( '/^\_:/', $subject ) );
//
//        $this->logger->trace( "[ subject :: $subject ][ predicatesCount :: $predicatesCount ][ isBlankNode :: " . ( $isBlankNode ? "yes" : "no" ) . " ]." );
//
//        if ( $isBlankNode ) return;
//
//        $posts = $this->entityService->getBySubject( $subject );
//        $postsCount = count( $posts );
//
//        $this->logger->trace( "$postsCount post(s) found with subject [ subject :: $subject ]." );
//
//        // create an entity post if it does not exist.
//        if ( 0 === $postsCount )
//            $this->entityService->create( $subject );
//
//        // bind the WordPress post to the entity.
//        $this->entityService->bindPostToSubjects( $postID, $subject );
//
////            foreach ( $predicates as $predicate => $objects ) {
////                $objectsCount = count( $objects );
////
////                $this->logger->trace( "   [ predicate :: $predicate ][ objectsCount :: $objectsCount ]" );
////
////                foreach ( $objects as $object ) {
////                    $type = $object[ "type" ];
////                    $value = $object[ "value" ];
////
////                    $this->logger->trace( "      [ value :: $value ][ type :: $type ]" );
////                }
////            }
//    }

}

?>