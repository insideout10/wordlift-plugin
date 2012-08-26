<?php
/**
 * User: david
 * Date: 26/08/12 16:34
 */

class WordLift_DumpAjaxService {

    public $logger;

    /** @var WordLift_TripleStoreService $tripleStoreService */
    public $tripleStoreService;

    public function dump( $postID ) {
        $store = $this->tripleStoreService->getStore();

        return array(
            "textAnnotations" => $this->dumpTextAnnotations( $postID, $store ),
            "entityAnnotations" => $this->dumpEntityAnnotations( $postID, $store ),
            "entities" => $this->dumpEntities( $postID, $store )
        );
    }

    public function dumpEntityAnnotations( $postID, &$store ) {

        $store = $this->tripleStoreService->getStore();

        $query = "SELECT ?confidence ?entityReference
                  WHERE {
                    ?subject a <http://fise.iks-project.eu/ontology/EntityAnnotation> .
                    ?subject <http://purl.org/insideout/wordpress/postID> \"$postID\" .
                    ?subject <http://fise.iks-project.eu/ontology/confidence> ?confidence .
                    ?subject <http://fise.iks-project.eu/ontology/entity-reference> ?entityReference .
                  }";

        $this->logger->trace( $query );
        $result = $store->query( $query );

        if ( $store->getErrors() ) {
            $this->logger->error( var_export( $store->getErrors(), true ) );
            return;
        }

        $queryTime = $result[ "query_time" ];
        $rows = &$result[ "result" ][ "rows" ];
        $rowsCount = count( $rows );

        $entityAnnotations = array();

        foreach ( $rows as $row )
            $entityAnnotations[] = array(
                "confidence" => $row[ "confidence" ],
                "entityReference" => $row[ "entityReference" ]
            );

        return $entityAnnotations;
    }

    public function dumpTextAnnotations( $postID, &$store ) {

        $query = "SELECT ?selectionHead ?selectionPrefix ?selectionSuffix ?selectionTail
                  WHERE {
                    ?subject a <http://fise.iks-project.eu/ontology/TextAnnotation> .
                    ?subject <http://purl.org/insideout/wordpress/postID> \"$postID\" .
                    ?subject <http://fise.iks-project.eu/ontology/selection-head> ?selectionHead .
                    ?subject <http://fise.iks-project.eu/ontology/selection-prefix> ?selectionPrefix .
                    ?subject <http://fise.iks-project.eu/ontology/selection-suffix> ?selectionSuffix .
                    ?subject <http://fise.iks-project.eu/ontology/selection-tail> ?selectionTail .
                  }";

        $this->logger->trace( $query );
        $result = $store->query( $query );

        if ( $store->getErrors() ) {
            $this->logger->error( var_export( $store->getErrors(), true ) );
            return;
        }

        $queryTime = $result[ "query_time" ];
        $rows = &$result[ "result" ][ "rows" ];
        $rowsCount = count( $rows );

        $textAnnotations = array();

        foreach ( $rows as $row )
            $textAnnotations[] = array(
                "selectionHead" => $row[ "selectionHead" ],
                "selectionPrefix" => $row[ "selectionPrefix" ],
                "selectionSuffix" => $row[ "selectionSuffix" ],
                "selectionTail" => $row[ "selectionTail" ]
            );

        return $textAnnotations;

    }

    public function dumpEntities( $postID, &$store ) {

        $query = "SELECT ?subject ?name ?type ?image ?url ?abstract
                  WHERE {
                    ?entityAnnotation fise:entity-reference ?subject .
                    ?entityAnnotation wordlift:postID \"$postID\" .
                    ?subject schema:name ?name .
                    ?subject a ?type .
                    OPTIONAL { ?subject schema:url ?abstract } .
                    OPTIONAL { ?subject schema:url ?url } .
                    OPTIONAL { ?subject schema:image ?image }
                  }";

        $this->logger->trace( $query );
        $result = $store->query( $query );

        if ( $store->getErrors() ) {
            $this->logger->error( var_export( $store->getErrors(), true ) );
            return;
        }

        $queryTime = $result[ "query_time" ];
        $rows = &$result[ "result" ][ "rows" ];
        $rowsCount = count( $rows );

        $entities = array();

        $subject = null;
        foreach ( $rows as $row ) {
            // skip variations of the same subject.
            if ( $subject === $row[ "subject" ] ) continue;

            $subject = $row[ "subject" ];
            $entities[] = array(
                "subject" => $subject,
                "name" => $row[ "name" ],
                "type" => $row[ "type" ],
                "image" => $row[ "image" ],
                "url" => $row[ "url" ],
                "abstract" => $row[ "abstract" ]
            );

        }

        return $entities;
    }

}

?>