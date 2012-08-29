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

        $textAnnotations = $this->dumpTextAnnotations( $postID );
        $entityAnnotations = $this->dumpEntityAnnotations( $postID );
        $entities = $this->dumpEntities( $postID );

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

    public function dumpEntityAnnotations( $postID ) {

        $query = "SELECT ?relation ?confidence ?entityReference ?selected
                  WHERE {
                    ?subject a fise:EntityAnnotation .
                    ?subject wordlift:postID \"$postID\" .
                    ?subject fise:confidence ?confidence .
                    ?subject fise:entity-reference ?entityReference .
                    ?subject dcterms:relation ?relation .
                    ?entityReference schema:name ?name .
                    OPTIONAL { ?subject wordlift:selected ?selected }
                  }";

        $result = $this->tripleStoreService->query( $query );
        $rows = &$result[ "result" ][ "rows" ];

        $entityAnnotations = array();

        foreach ( $rows as $row )
            $entityAnnotations[] = array(
                "relation" => $row[ "relation" ],
                "confidence" => (double) $row[ "confidence" ],
                "entityReference" => $row[ "entityReference" ],
                "selected" => ( "true" === $row[ "selected" ] ? true : false )
            );

        return $entityAnnotations;
    }

    public function dumpTextAnnotations( $postID ) {

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

        $result = $this->tripleStoreService->query( $query );
        $rows = &$result[ "result" ][ "rows" ];

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

    public function dumpEntities( $postID ) {

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

        $result = $this->tripleStoreService->query( $query );
        $rows = &$result[ "result" ][ "rows" ];

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