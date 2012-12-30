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
        // to enable CORS.
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

        // $this->logger->trace( "[ entity :: $entity ][ bind :: " . var_export( $bind, true ) . " ]." );

    }

    public function clear( $entity, $requestBody ) {

        $json = json_decode( $requestBody );
        $clear = &$json->clear;

        $this->logger->trace( "[ entity :: $entity ][ clear :: " . var_export( $clear, true ) . " ]." );

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
}

?>