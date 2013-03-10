<?php
/**
 * User: David Riccitelli
 * Date: 20/09/12 14:51
 */

class WordLift_TreeMap
{

    public $queryService;

    public $width;
    public $height;
    public $columnWidth;

    public $defaultLanguage = "EN";
    public $maxSide = 200;
    public $minSide = 100;

    public function get($attributes, $content)
    {

        // get the type filter.
        $types = array(
            "Person",
            "Organization",
            "Place",
            "CreativeWork",
            "Event",
            "Product"
        );
        $typeFilter = "";

        $fragment = "<div class=\"entity-treemap-toolbar\">"
            . "<div class=\"selector\" data-filter=\"\">All</div>";

        if (
            is_array( $attributes )
            && array_key_exists( "types", $attributes)
        ) {
            $types = explode( ",", $attributes[ "types" ] );
        }

        foreach ( $types as &$type ) :
            $htmlSimpleTypeName = htmlspecialchars( $type, ENT_COMPAT | ENT_HTML401, "UTF-8" );;
            $fragment .= "<div class=\"selector $htmlSimpleTypeName\" data-filter=\".$htmlSimpleTypeName\"><div class=\"symbol\"></div>$htmlSimpleTypeName</div>";

            $escType = $this->queryService->escapeValue( $type );
            if ( ! empty( $typeFilter ) )
                $typeFilter .= " || ";
            $typeFilter .= "?type = <http://schema.org/$type>";
        endforeach;

        $typeFilter = " FILTER( $typeFilter ) . ";
        $fragment .= "</div>";

        $whereClause = <<<EOF

        [] a fise:Enhancement ;
            wordlift:postID ?postID ;
            wordlift:selected true ;
            fise:entity-reference ?subject .
        ?subject a ?type ;
            schema:name ?name .
        OPTIONAL { ?subject schema:image ?image } .
        FILTER ( langMatches( lang( ?name ), "$this->defaultLanguage" ) ) .
        $typeFilter
EOF;

        // public function execute( $fields, $whereClause = NULL, $limit = NULL, $offset = NULL, &$count = NULL, $groupBy = NULL, $orderBy = NULL ) {
        $count = 0;
        $result = $this->queryService->execute( "DISTINCT ?subject COUNT(?postID) as ?count ?name ?type ?image", $whereClause, 999, 0, $count, "?subject ?name ?type ?image", "DESC(?count)" );
        $rows = &$result[ "result" ][ "rows" ];
        $variables = &$result[ "result" ][ "variables" ];
        // var_export($rows);
        // exit;

        // this block of code transforms a plain result table in a hierarchical array.
        $index = array();
        $variableCount = count( $variables );
        $lastVariable = $variables[ $variableCount - 1 ];
        foreach ( $rows as &$row ) :
            $current = &$index;
            foreach ( $variables as &$variable ) :
                $value = $row[ $variable ];

                if ( NULL === $value )
                    continue;

                // set the value if this is the last variable.
                if ( $lastVariable === $variable ) :
                    if ( ! in_array( $value, $current ) )
                        $current[] = $value;

                    continue;
                endif;

                // otherwise set the key.
                if ( ! array_key_exists( $value, $current ) ) :
                    $current[ $value ] = array();
                endif;

                $current = &$current[ $value ];
            endforeach;
        endforeach;

        $fragment .= "<div class=\"entity-treemap\">";
        $size = $this->maxSide - $this->minSide;
        $interval = NULL;
        foreach ( $index as $subject => &$bag ) :
            $count = key( $bag );

            if ( NULL === $interval )
                $interval = $size / ( $count - 1 );

            $name = key( $bag[ $count ] );
            $type = key( $bag[ $count ][ $name ] );
            $simpleTypeName = substr( $type, strrpos( $type, "/" ) + 1 );
            $htmlSimpleTypeName = htmlspecialchars( $simpleTypeName, ENT_COMPAT | ENT_HTML401, "UTF-8" );;

            $side = $this->minSide + ( $count - 1 ) * $interval;
            $htmlSubject = htmlspecialchars( $subject, ENT_COMPAT | ENT_HTML401, "UTF-8" );;
            $fragment .= "<a class=\"entity-box $htmlSimpleTypeName\""
                . "style=\"width: " . $side . "px; height: " . $side . "px\""
                . " href=\""
                . site_url("wp-admin/admin-ajax.php")
                . "?action=wordlift.gotoentity&e=$htmlSubject\">";

            $htmlName = htmlspecialchars( $name, ENT_COMPAT | ENT_HTML401, "UTF-8" );;
            $fragment .= "<div class=\"name\" style=\"font-size: " . ceil( $side * 0.1 ) . "px; line-height: " . ceil( $side * 0.1 ) . "px;\">$htmlName</div>";

            $fragment .= "<div class=\"type $htmlSimpleTypeName\"></div>";
            
            if ( 0 < count( $bag[ $count ][ $name ][ $type ] ) ) :
                $image = $bag[ $count ][ $name ][ $type ][ 0 ];
                $htmlImage = htmlspecialchars( $image, ENT_COMPAT | ENT_HTML401, "UTF-8" );;
                $fragment .= "<img class=\"image\" onerror=\"this.parentNode.removeChild(this);\" src=\"$htmlImage\" style=\"max-width: " . ceil( $side * 0.33 ) . "px; max-height: " . ceil( $side / 0.33 ) . "px;\" />";
            endif;

            $fragment .= "</a>";
        endforeach;
        $fragment .= "</div>";

        return $fragment . $content;
    }

}

?>