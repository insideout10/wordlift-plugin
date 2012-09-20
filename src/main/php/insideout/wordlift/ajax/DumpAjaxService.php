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
        // enable CORS when the OPTIONS method is requested.
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

}

?>