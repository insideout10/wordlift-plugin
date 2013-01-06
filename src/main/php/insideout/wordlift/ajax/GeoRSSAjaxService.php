<?php
/**
 * User: David Riccitelli
 * Date: 20/09/12 09:35
 */

class WordLift_GeoRssAjaxService {

    public $logger;

    /** @var WordLift_TripleStoreService $tripleStoreService */
    public $tripleStoreService;

    public $queryService;

    public function get() {

        $query = "SELECT DISTINCT ?name ?postID ?latitude ?longitude"; // ?latitude ?longitude \n";
        $query .= " WHERE { \n";
        $query .= "   ?ea a fise:Enhancement ; \n";
        $query .= "       wordlift:postID ?postID ; \n";
        $query .= "       wordlift:selected true ; \n";
        $query .= "       fise:entity-reference [ \n";
        $query .= "            a ?type ; \n";
        $query .= "            schema:name ?name ; \n";
        $query .= "            ?predicate [ \n";
        $query .= "              a schema:Place ; \n";
        $query .= "              schema:geo [ \n"; 
        $query .= "                schema:latitude ?latitude ; \n";
        $query .= "                schema:longitude ?longitude ] ] ] . \n";
        $query .= " FILTER regex( str(?type), \"http://schema.org/\" ) \n";
        $query .= "  } \n";

        // $query = "SELECT DISTINCT ?postID ?latitude ?longitude
        //           WHERE {
        //             ?textAnnotation a fise:TextAnnotation .
        //             ?textAnnotation wordlift:postID ?postID .
        //             ?entityAnnotation a fise:EntityAnnotation .
        //             ?entityAnnotation dcterms:relation ?textAnnotation .
        //             ?entityAnnotation fise:entity-reference ?entity .
        //             ?entityAnnotation wordlift:selected true .
        //             ?entity schema:location ?place .
        //             ?place schema:geo ?geo .
        //             ?geo schema:latitude ?latitude .
        //             ?geo schema:longitude ?longitude .
        //           }";

        $result = $this->queryService->query( $query, "raw", "", true );
        $rows = &$result[ "rows" ];

        // echo( $query );
        // var_dump( $result );
        // exit;


        $title = "";
        $subtitle = "";
        $link = "";
        $updated = "";

echo <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:georss="http://www.georss.org/georss">
    <title>$title</title>
    <subtitle>$subtitle</subtitle>
    <link href="$link"/>
    <updated>$updated</updated>
EOF;

        $coordinates = array();

        // group data by coordinates.
        foreach ( $rows as $row ) :
            $postId = $row[ "postID" ];
            $coordinates[ $row[ "latitude" ] . " " . $row[ "longitude" ] ][ "name" ] = $row[ "name" ];
            if ( ! in_array( $postId, $coordinates[ $row[ "latitude" ] . " " . $row[ "longitude" ] ][ "posts" ] ) )
                $coordinates[ $row[ "latitude" ] . " " . $row[ "longitude" ] ][ "posts" ][] = $row[ "postID" ];
        endforeach;

        foreach ( $coordinates as $point => $posts ) {

            $title = $posts[ "name" ]; // implode( ",", $posts[ "posts" ] );
            $id = "";
            $updated = "";
            $summary = "<ul class=\"posts\">";
            foreach ( $posts[ "posts" ] as $post )
                $summary .= "<li class=\"item\"><a href=\"" . get_permalink( $post ) . "\">" . get_the_title( $post ) . "</a></li>";
            $summary .= "</div>";

echo <<<EOF

    <entry>
        <title>$title</title>
        <link href="$link"/>
        <id>$id</id>
        <updated>$updated</updated>
        <summary><![CDATA[$summary]]></summary>
        <thumbnail url="" />
        <category term="" />
        <georss:point>$point</georss:point>
    </entry>
EOF;
        }

echo <<<EOF

</feed>
EOF;

        return WordPress_AjaxProxy::CALLBACK_RETURN_NULL;
    }

}

?>