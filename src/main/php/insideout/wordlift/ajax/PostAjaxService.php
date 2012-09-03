<?php
/**
 * User: David Riccitelli
 * Date: 28/08/12 17:10
 */

class WordLift_PostAjaxService {

    public $logger;

    /** @var WordLift_TripleStoreService $tripleStoreService */
    public $tripleStoreService;

    public function options() {
        // DO nothing.
    }

    public function bind( $entity, $requestBody ) {

        $this->clear( $entity, $requestBody );

        $json = json_decode( $requestBody );
        $bind = &$json->bind;

        while ( 0 < count( $bind ) ) {
            $textAnnotation = array_shift( $bind );

            $query = "INSERT INTO <> { ?entityAnnotation wordlift:selected true }
                      WHERE {
                        ?entityAnnotation a fise:EntityAnnotation .
                        ?entityAnnotation fise:entity-reference <$entity> .
                        ?entityAnnotation dcterms:relation <$textAnnotation> .
                      }";

            $this->tripleStoreService->query( $query );
        }

        $this->logger->trace( "[ entity :: $entity ][ bind :: " . var_export( $bind, true ) . " ]." );

    }

    public function clear( $entity, $requestBody ) {

        $json = json_decode( $requestBody );
        $clear = &$json->clear;

        $this->logger->trace( "[ entity :: $entity ][ clear :: " . var_export( $clear, true ) . " ]." );

        $textAnnotations = "";
        while ( 0 < count( $clear ) ) {
            $textAnnotation = array_shift( $clear );

            $query = "DELETE { ?entityAnnotation wordlift:selected true }
                      WHERE {
                        ?entityAnnotation a fise:EntityAnnotation;
                                        dcterms:relation <$textAnnotation->about>
                      }";

            $this->tripleStoreService->query( $query );
        }

    }

    public function createBinding( $postID, $entity, $requestBody ) {
        $this->logger->trace( "[ postID :: $postID ][ entity :: $entity ]." );

        $json = json_decode( $requestBody );
        $texts = &$json->texts;

        /*
         * [text] --> [textAnnotation] --> [entityAnnotation] --> [entity]
         * erase any existing link between the textAnnotation, the entityAnnotation and the entity for this postID.
         */

        foreach ( $texts as $text)
            $this->createSingleBinding( $text, $postID, $entity );

        return "OK";
    }

    public function deleteBinding( $postID, $entity, $requestBody ) {

        $this->logger->trace( "[ postID :: $postID ][ entity :: $entity ]." );

        $json = json_decode( $requestBody );
        $texts = &$json->texts;

        foreach ( $texts as $text)
            $this->deleteSingleBinding( $text, $postID );

        return "OK";
    }

    public function createSingleBinding( $textAnnotation, $postID, $entity ) {
        $this->logger->trace( "[ postID :: $postID ][ entity :: $entity ]." );

        /*
         * [text] --> [textAnnotation] --> [entityAnnotation] --> [entity]
         * erase any existing link between the textAnnotation, the entityAnnotation and the entity for this postID.
         */

        $this->deleteSingleBinding( $textAnnotation, $postID );

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

    public function deleteSingleBinding( $textAnnotation, $postID ) {

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