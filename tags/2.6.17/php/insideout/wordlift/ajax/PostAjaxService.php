<?php
/**
 * User: David Riccitelli
 * Date: 28/08/12 17:10
 */

class WordLift_PostAjaxService {

    public $logger;

    public $metaKeyJobStatus;

    /** @var WordLift_TripleStoreService $tripleStoreService */
    public $tripleStoreService;

    const JOB_STATUS_DISAMBIGUATED = "disambiguated";

    public function options() {
        // to enable CORS.
    }

    public function bind( $entity, $requestBody ) {

        $this->clear( $entity, $requestBody );

        $json = json_decode( $requestBody );
        $bind = &$json->bind;
        $postID = $json->postID;

        while ( 0 < count( $bind ) ) {
            $textAnnotation = array_shift( $bind );

            $query = "INSERT INTO <> {
                        ?entityAnnotation wordlift:selected true ;
                                          dcterms:references <urn:wordpress:$postID> 
                    }
                    WHERE {
                        ?entityAnnotation a fise:EntityAnnotation .
                        ?entityAnnotation fise:entity-reference <$entity> .
                        ?entityAnnotation dcterms:relation <$textAnnotation> .
                    }";

            $this->tripleStoreService->query( $query );
        }

        update_post_meta( $postID, $this->metaKeyJobStatus, self::JOB_STATUS_DISAMBIGUATED );
        // $this->logger->trace( "[ entity :: $entity ][ bind :: " . var_export( $bind, true ) . " ]." );

    }

    public function clear( $entity, $requestBody ) {

        $json = json_decode( $requestBody );
        $clear = &$json->clear;
        $postID = $json->postID;

        $this->logger->trace( "[ entity :: $entity ][ clear :: " . var_export( $clear, true ) . " ]." );

        while ( 0 < count( $clear ) ) {
            $textAnnotation = array_shift( $clear );

            $query = "DELETE {
                        ?entityAnnotation wordlift:selected true ; 
                                          dcterms:references <urn:wordpress:$postID> . 
                    }
                    WHERE {
                        ?entityAnnotation a fise:EntityAnnotation;
                            dcterms:relation <$textAnnotation->about>
                    }";

            $this->tripleStoreService->query( $query );
        }

    }
}

?>