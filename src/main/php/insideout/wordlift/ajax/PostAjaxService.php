<?php
/**
 * User: David Riccitelli
 * Date: 28/08/12 17:10
 */

class WordLift_PostAjaxService {

    public $logger;

    /** @var WordLift_TripleStoreService $tripleStoreService */
    public $tripleStoreService;

    public function createBinding( $textAnnotation, $postID, $entity ) {
        $this->logger->trace( "[ postID :: $postID ][ entity :: $entity ]." );

        /*
         * [text] --> [textAnnotation] --> [entityAnnotation] --> [entity]
         * erase any existing link between the textAnnotation, the entityAnnotation and the entity for this postID.
         */

        $this->deleteBinding( $textAnnotation, $postID );

        $query = "INSERT INTO <> { ?entityAnnotation wordlift:selected true }
                  WHERE {
                    ?entityAnnotation a fise:EntityAnnotation .
                    ?entityAnnotation wordlift:postID \"$postID\" .
                    ?entityAnnotation fise:entity-reference <$entity> .
                    ?entityAnnotation dcterms:relation <$textAnnotation> .
                  }";

        $results = $this->tripleStoreService->query( $query );

        if ( false === $results )
            return WordPress_AjaxProxy::CALLBACK_RETURN_ERROR;

        return "OK";
    }

    public function deleteBinding( $textAnnotation, $postID ) {

        $query = "DELETE { ?entityAnnotation wordlift:selected true }
                  WHERE {
                    ?entityAnnotation a fise:EntityAnnotation .
                    ?entityAnnotation wordlift:postID \"$postID\" .
                    ?entityAnnotation dcterms:relation <$textAnnotation> .
                  }";

        $results = $this->tripleStoreService->query( $query );

        if ( false === $results )
            return WordPress_AjaxProxy::CALLBACK_RETURN_ERROR;

        return "OK";
    }
}

?>