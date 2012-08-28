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
        header( "Access-Control-Allow-Origin: *" );
        header( "Access-Control-Allow-Methods: GET, OPTIONS" );
        header( "Access-Control-Allow-Headers: origin, x-requested-with, accept" );

        $store = $this->tripleStoreService->getStore();

        $textAnnotations = $this->dumpTextAnnotations( $postID, $store );
        $entityAnnotations = $this->dumpEntityAnnotations( $postID, $store );
        $entities = $this->dumpEntities( $postID, $store );

        foreach ( $entityAnnotations as &$entityAnnotation ) {
            $entityReference = $entityAnnotation[ "entityReference" ];
            $entityAnnotation = array_merge(
                $entityAnnotation,
                $entities[ $entityReference ]
            );
        }

        return array(
            "annotations" => $textAnnotations,
            "entities" => $entityAnnotations
        );
    }

    public function dumpEntityAnnotations( $postID, &$store ) {

        $store = $this->tripleStoreService->getStore();

        $query = "SELECT ?relation ?confidence ?entityReference
                  WHERE {
                    ?subject a fise:EntityAnnotation .
                    ?subject wordlift:postID \"$postID\" .
                    ?subject fise:confidence ?confidence .
                    ?subject fise:entity-reference ?entityReference .
                    ?subject dcterms:relation ?relation .
                    ?entityReference schema:name ?name
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
                "relation" => $row[ "relation" ],
                "confidence" => (double) $row[ "confidence" ],
                "entityReference" => $row[ "entityReference" ]
            );

        return $entityAnnotations;
    }

    public function dumpTextAnnotations( $postID, &$store ) {

        $query = "SELECT DISTINCT ?subject ?selectionHead ?selectionPrefix ?selectionSuffix ?selectionTail ?selectedText
                  WHERE {
                    ?subject a fise:TextAnnotation .
                    ?subject wordlift:postID \"$postID\" .
                    ?subject fise:selection-head ?selectionHead .
                    ?subject fise:selection-prefix ?selectionPrefix .
                    ?subject fise:selection-suffix ?selectionSuffix .
                    ?subject fise:selection-tail ?selectionTail .
                    ?subject fise:selected-text ?selectedText .
                    ?entityAnnotation dcterms:relation ?subject .
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

        foreach ( $rows as $row ) {
            $subject = $row[ "subject" ];
            $textAnnotations[] = array(
                "subject" => $subject,
                "selectionHead" => $row[ "selectionHead" ],
                "selectionPrefix" => $row[ "selectionPrefix" ],
                "selectionSuffix" => $row[ "selectionSuffix" ],
                "selectionTail" => $row[ "selectionTail" ],
                "selectedText" => $row[ "selectedText" ]
            );
        }

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
            $entities[ $subject ] = array(
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