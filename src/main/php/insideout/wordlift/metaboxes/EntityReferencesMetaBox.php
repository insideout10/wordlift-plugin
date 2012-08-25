<?php
/**
 * User: david
 * Date: 25/08/12 16:12
 */

class WordLift_EntityReferencesMetaBox implements WordPress_IMetaBox {

    public $logger;

    /** @var WordLift_EntityService $entityService */
    public $entityService;

    public function getHtml( $post ) {

        $references = $this->entityService->getByPostID( $post->ID );
        $referencesCount = count( $references );

        $this->logger->trace( "$referencesCount entity reference(s) found for post [ postID :: $post->ID ]." );

        echo "$referencesCount reference(s) found:<br/>";


        /** @var ARC2_Store $store */
        $store = ARC2::getStore(array(
            "ns" => array(
                "rdf" => "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
                "rdfs" => "http://www.w3.org/2000/01/rdf-schema#",
                "dbpedia" => "http://dbpedia.org/ontology/",
                "schema" => "http://schema.org/"
            ),
            "db_host" => DB_HOST,
            "db_name" => DB_NAME,
            "db_user" => DB_USER,
            "db_pwd" => DB_PASSWORD,
            "store_name" => "wordlift"
        ));

        echo "<ul>";

        foreach ( $references as $reference ) {

            $this->logger->trace( "Getting properties for reference [ reference :: $reference ]." );

            $query = "SELECT ?name ?url ?type ?image
                      WHERE {
                        <$reference> schema:name ?name .
                        <$reference> rdf:type ?type .
                        OPTIONAL { <$reference> schema:url ?url }
                        OPTIONAL { <$reference> schema:image ?image }
                      }";

            $this->logger->trace( "[ query :: $query ]." );

            $result = $store->query( $query );

            if ( $store->getErrors() ) {
                $this->logger->error( var_export( $store->getErrors(), true ) );
                return;
            }

            $queryTime = $result[ "query_time" ];
            $rows = &$result[ "result" ][ "rows" ];
            $rowsCount = count( $rows );

            $name = $rows[0][ "name" ];
            $type = $rows[0][ "type" ];
            $url = $rows[0][ "url" ];
            $image = $rows[0][ "image" ];

            $this->logger->trace( "[ rowsCount :: $rowsCount ][ queryTime :: $queryTime ]." );

            echo "<li style=\"border: 1px solid steelBlue; background: lightSteelBlue;\">";
            if ( $image )
                echo "<img width=\"40\" src=\"" . htmlentities( $image ) . "\" onerror=\"this.parentNode.removeChild( this );\" />";
            echo "<div>$name</div><div>[ type :: $type ]</div></li>";
        }

        echo "</ul>";
    }

}

?>