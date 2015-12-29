<?php
/**
 * User: David Riccitelli
 * Date: 20/09/12 09:35
 */

class WordLift_GeoRssAjaxService {

    public $logger;

    public $queryService;

    public $defaultLanguage = "EN";

    public function get( $type = NULL ) {

        $whereClause = <<<EOF
            ?enhancement a fise:Enhancement ;
                wordlift:postID ?postID ;
                wordlift:selected true ;
                fise:entity-reference ?subject ;
                dcterms:references [
                    a dctype:Text ;
                    dcterms:accessRights ?rights . 
                ] . 
            ?subject a ?type ;
                schema:name ?name ;
                ?predicate [
                    a schema:Place ;
                    schema:geo [ 
                        schema:latitude ?latitude ;
                        schema:longitude ?longitude ] ] .
            OPTIONAL { ?subject schema:image ?image } .
            FILTER langMatches( lang(?name), "$this->defaultLanguage" ) .
            FILTER( ?rights = "publish" ) .
EOF;

        if ( NULL === $type ) :
            $whereClause .= "   FILTER regex( str(?type), \"http://schema.org/\" ) . \n";
        else :
            $escType = $this->queryService->escapeValue( $type );
            $whereClause .= "   FILTER ( str(?type) = \"http://schema.org/$escType\" ) . \n";
        endif;

        // public function execute( $fields, $whereClause = NULL, $limit = NULL, $offset = NULL, &$count = NULL, $groupBy = NULL, $orderBy = NULL ) {
        $count = 0;
        $result = $this->queryService->execute( "DISTINCT ?latitude ?longitude ?name ?type ?image ?postID", $whereClause, 999, 0, $count, "?latitude ?longitude ?name ?type ?image ?postID", "DESC(?postID)" );
        $rows = &$result[ "result" ][ "rows" ];

echo <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:georss="http://www.georss.org/georss">
EOF;

        $pins = array();

        // group data by coordinates.
        foreach ( $rows as &$row ) :
            $coordinates = $row[ "latitude" ] . " " . $row[ "longitude" ];
            $name = $row[ "name" ];
            $postId = $row[ "postID" ];
            $image = $row[ "image" ];
            $type = $row[ "type" ];

            if ( ! array_key_exists( $coordinates, $pins ) ) :
                $pins[ $coordinates ] = array(
                    "name" => $name, 
                    "image" => $image, 
                    "type" => $type, 
                    "posts" => array()
                );
            endif;

            if ( ! in_array( $postId, $pins[ $coordinates ][ "posts" ] ) )
                $pins[ $coordinates ][ "posts" ][] = $postId;
        endforeach;

        foreach ( $pins as $coordinates => $bag ) :
            $name = $bag[ "name" ];
            $htmlName = htmlspecialchars( $name, ENT_COMPAT | ENT_HTML401, "UTF-8" );

            $image = $bag[ "image" ];
            $htmlImage = htmlspecialchars( $image, ENT_COMPAT | ENT_HTML401, "UTF-8" );
            $type = $bag[ "type" ];
            $simpleTypeName = substr( $type, strrpos( $type, "/" ) + 1 );
            $htmlSimpleTypeName = htmlspecialchars( $simpleTypeName, ENT_COMPAT | ENT_HTML401, "UTF-8" );

            $summary = "<img src=\"$htmlImage\" onerror=\"this.parentNode.removeChild(this);\" class=\"image\" />";
            $summary .= "<div class=\"name\">$name</div><div class=\"type $htmlSimpleTypeName\"></div>"; 
            $summary .= "<ul class=\"posts\">";
            foreach ( $bag[ "posts" ] as &$post )
                $summary .= "<li class=\"item\"><a href=\"" . get_permalink( $post ) . "\">" . get_the_title( $post ) . "</a></li>";
            $summary .= "</ul>";

echo <<<EOF

    <entry>
        <title>$htmlName</title>
        <link href="$link"/>
        <id></id>
        <summary><![CDATA[$summary]]></summary>
        <thumbnail url="$htmlImage" />
        <category term="$htmlSimpleTypeName" />
        <georss:point>$coordinates</georss:point>
    </entry>
EOF;
        endforeach;

echo <<<EOF

</feed>
EOF;

        return WordPress_AjaxProxy::CALLBACK_RETURN_NULL;
    }

}

?>