<?php
/**
 * User: david
 * Date: 26/08/12 16:34
 */

class WordLift_DumpAjaxService {

    public $logger;

    /** @var WordLift_TripleStoreService $tripleStoreService */
    public $tripleStoreService;

    public function options() {
        // DO nothing.
    }

    public function getDisambiguationOptions( $postID ) {

        $entitiesAndTextAnnotations = $this->getEntitiesAndTextAnnotations( $postID );

        $disambiguations = array();


        while ( 0 < count( $entitiesAndTextAnnotations[ "textAnnotations" ] ) ) {

            $textAnnotation = key( $entitiesAndTextAnnotations[ "textAnnotations" ] );
            $bag = array_shift( $entitiesAndTextAnnotations[ "textAnnotations" ] );
            $entities = &$bag[ "entities" ];

            $textAnnotations = array();
            $textAnnotations[ $textAnnotation ] = array(
                "about" => $textAnnotation,
                "selectionHead" => $bag[ "selectionHead" ],
                "selectionTail" => $bag[ "selectionTail" ],
//                "selectionStart" => $bag[ "selectionStart" ],
//                "selectionEnd" => $bag[ "selectionEnd" ],
                "selectedText" => $bag[ "selectedText" ]
            );

            foreach ( $entities as $entity => $bag ) {
                $entities[ $entity ] = array_merge( $bag, $entitiesAndTextAnnotations[ "entities" ][ $entity ] );

                $this->setConfidence( $entities[ $entity ] );

                foreach( $entitiesAndTextAnnotations[ "entities" ][ $entity ][ "textAnnotations" ] as $textAnnotation ) {
                    if ( !array_key_exists( $textAnnotation, $textAnnotations ) )
                        $textAnnotations[ $textAnnotation ] = array(
                            "about" => $textAnnotation,
                            "selectionHead" => $entitiesAndTextAnnotations[ "textAnnotations" ][ $textAnnotation ][ "selectionHead" ],
                            "selectionTail" => $entitiesAndTextAnnotations[ "textAnnotations" ][ $textAnnotation ][ "selectionTail" ],
//                            "selectionStart" => $entitiesAndTextAnnotations[ "textAnnotations" ][ $textAnnotation ][ "selectionStart" ],
//                            "selectionEnd" => $entitiesAndTextAnnotations[ "textAnnotations" ][ $textAnnotation ][ "selectionEnd" ],
                            "selectedText" => $entitiesAndTextAnnotations[ "textAnnotations" ][ $textAnnotation ][ "selectedText" ]
                        );

                    foreach ( $entitiesAndTextAnnotations[ "textAnnotations" ][ $textAnnotation ][ "entities" ] as $entity => $bag ) {
                        $entities[ $entity ] = array_merge( $bag, $entitiesAndTextAnnotations[ "entities" ][ $entity ] );
                        $this->setConfidence( $entities[ $entity ] );
                    }

                    unset( $entitiesAndTextAnnotations[ "textAnnotations" ][ $textAnnotation ] );
                }
            }

            $disambiguations[] = array(
                "textAnnotations" => array_values( $textAnnotations ),
                "entities" => array_values( $entities )
            );
        }

        return $disambiguations;
    }

    private function setConfidence( &$entity) {
        if ( !in_array( $entity, "lowestConfidence" )
            || $entity[ "confidence" ] < $entity[ "lowestConfidence" ] )
            $entity[ "lowestConfidence" ] = $entity[ "confidence" ];
        if ( !in_array( $entity, "highestConfidence" )
            || $entity[ "confidence" ] > $entity[ "highestConfidence" ] )
            $entity[ "highestConfidence" ] = $entity[ "confidence" ];

        unset( $entity[ "confidence" ] );

        return $entity;
    }

    private function getEntitiesAndTextAnnotations( $postID ) {

        $query = "SELECT ?textAnnotation ?confidence ?selectionHead ?selectionTail ?selectedText ?entity ?name ?type ?image ?url ?selected
                  WHERE {
                    ?textAnnotation a fise:TextAnnotation;
                                    wordlift:postID \"$postID\";
                                    fise:selection-head ?selectionHead;
                                    fise:selection-tail ?selectionTail;
                                    fise:selected-text ?selectedText .
                    ?entityAnnotation a fise:EntityAnnotation;
                                      dcterms:relation ?textAnnotation;
                                      fise:entity-reference ?entity;
                                      fise:confidence ?confidence .
                    ?entity a ?type;
                            schema:name ?name .
                    OPTIONAL { ?entity schema:image ?image } .
                    OPTIONAL { ?entity schema:url ?url } .
                    OPTIONAL { ?entityAnnotation wordlift:selected ?selected } .
                  } ORDER BY DESC( ?confidence )";

        $result = $this->tripleStoreService->query( $query );
        $rows = &$result[ "result" ][ "rows" ];

        $textAnnotations = array();
        $entities = array();

        foreach ( $rows as $row ) {
            $textAnnotation = $row[ "textAnnotation" ];
            $entity = $row[ "entity" ];
            $confidence = (double) $row[ "confidence" ];
            $name = $row[ "name" ];
            $type = $row[ "type" ];
            $image = $row[ "image" ];
            $url = $row[ "url" ];
            $selected = ( "true" === $row[ "selected" ] ? true : false );

            if ( !array_key_exists( $textAnnotation, $textAnnotations ) )
                $textAnnotations[ $textAnnotation ] = array(
                    "entities" => array(),
                    "selectionHead" => $row[ "selectionHead" ],
                    "selectionTail" => $row[ "selectionTail" ],
//                    "selectionStart" => $row[ "selectionStart" ],
//                    "selectionEnd" => $row[ "selectionEnd" ],
                    "selectedText" => $row[ "selectedText" ]
                );

            if ( !array_key_exists( $entity, $textAnnotations[ $textAnnotation ][ "entities" ] ) )
                $textAnnotations[ $textAnnotation ][ "entities" ][ $entity ] = array(
                    "confidence" => $confidence,
                    "selected" => $selected
                );

//            if ( $confidence > $textAnnotations[ $textAnnotation ][ "entities" ][ $entity ][ "highestConfidence" ] )
//                $textAnnotations[ $textAnnotation ][ "entities" ][ $entity ][ "highestConfidence" ] = $confidence;
//            if ( $confidence < $textAnnotations[ $textAnnotation ][ "entities" ][ $entity ][ "lowestConfidence" ] )
//                $textAnnotations[ $textAnnotation ][ "entities" ][ $entity ][ "lowestConfidence" ] = $confidence;
//            $textAnnotations[ $textAnnotation ][ "entities" ][ $entity ][ "selected" ] =
//                (boolean) $textAnnotations[ $textAnnotation ][ "entities" ][ $entity ][ "selected" ] && $selected;


            if ( !array_key_exists( $entity, $entities ) )
                $entities[ $entity ] = array(
                    "textAnnotations" => array(),
                    "about" => $entity,
                    "name" => $name,
                    "type" => $type,
                    "image" => array(),
                    "url" => array()
                );

            if ( NULL !== $image && !in_array( $image, $entities[ $entity ][ "image" ] ) )
                $entities[ $entity ][ "image"][] = $image;
            if ( NULL !== $url && !in_array( $url, $entities[ $entity ][ "url" ] ) )
                $entities[ $entity ][ "url"][] = $url;

            if ( !in_array( $textAnnotation, $entities[ $entity ][ "textAnnotations" ] ) )
                $entities[ $entity ][ "textAnnotations" ][] = $textAnnotation;
        }

        return array(
            "entities" => $entities,
            "textAnnotations" => $textAnnotations
        );
    }

    public function dump( $postID ) {
        $annotations = $this->getEntities( $postID );

        $entities = array();

        foreach ( $annotations as $annotation ) {

            $subject = $annotation[ "entity" ];
            $name = $annotation[ "name" ];
            $type = $annotation[ "type" ];
            $confidence = $annotation[ "confidence" ];
            $url = ( array_key_exists( "url", $annotation ) ? $annotation[ "url" ] : NULL );
            $image = ( array_key_exists( "image", $annotation ) ? $annotation[ "image" ] : NULL );
            $textAnnotation = ( array_key_exists( "textAnnotation", $annotation ) ? $annotation[ "textAnnotation" ] : NULL );
            $selected = ( array_key_exists( "selected", $annotation ) && "true" === $annotation[ "selected" ] );

            if ( !array_key_exists( $subject, $entities ) )
                $entities[ $subject ] = array(
                    "entity" => $subject,
                    "name" => $name,
                    "type" => $type,
                    "confidence" => (double) $confidence,
                    "url" => array(),
                    "image" => array(),
                    "texts" => array(),
                    "selected" => $selected
                );

            $entity = &$entities[ $subject ];
            if ( NULL !== $url && !in_array( $url, $entity[ "url" ] ) )
                $entity[ "url" ][] = $url;
            if ( NULL !== $image && !in_array( $image, $entity[ "image" ] ) )
                $entity[ "image" ][] = $image;
            if ( NULL !== $textAnnotation && !in_array( $textAnnotation, $entity[ "texts" ] ) )
                $entity[ "texts" ][] = $textAnnotation;

        }

        return array_values( $entities );
    }

    public function getEntities( $postID ) {

        $query = "SELECT ?entity ?name ?type ?confidence ?url ?image ?textAnnotation ?selected
                  WHERE {
                    ?entity schema:name ?name .
                    ?entity a ?type .
                    ?entityAnnotation a fise:EntityAnnotation .
                    ?entityAnnotation wordlift:postID \"$postID\" .
                    ?entityAnnotation fise:entity-reference ?entity .
                    ?entityAnnotation fise:confidence ?confidence .
                    ?entityAnnotation dcterms:relation ?textAnnotation .
                    ?textAnnotation a fise:TextAnnotation .
                    OPTIONAL { ?entity schema:description ?description } .
                    OPTIONAL { ?entity schema:url ?url } .
                    OPTIONAL { ?entity schema:image ?image } .
                    OPTIONAL { ?entityAnnotation wordlift:selected ?selected }
                  }
                  ORDER BY DESC( ?confidence )";

        $result = $this->tripleStoreService->query( $query );
        $rows = &$result[ "result" ][ "rows" ];

        return $rows;
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
                    ?entityAnnotation fise:entity-reference ?entity .
                    ?entity schema:name ?name .
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
                    OPTIONAL { ?subject schema:description ?abstract } .
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