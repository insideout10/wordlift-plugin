<?php
/**
 * User: david
 * Date: 15/07/12 21:23
 */

class WordLift_EntitiesBar {

    public $logger;
	public $queryService;

    public function get( $attributes, $content = NULL) {

    	// get the current post ID.
    	$postId = get_the_ID();

        if ( ! is_numeric( $postId ) )
            return "unknown post id " . $content;

    	// will containt the content fragment to send back to the client.
    	$fragment = "";

        // get the post languages.
        $languages = $this->getPostLanguages( $postId );

        $whereClause = <<<EOF

 [] a fise:Enhancement ;
    wordlift:postID "$postId" ;
    wordlift:selected true ;
    fise:entity-reference ?subject .
 ?subject a ?type;
   <http://schema.org/name> ?name ;
   <http://schema.org/description> ?description .
 OPTIONAL { ?subject <http://schema.org/image> ?image } .
 OPTIONAL { ?subject <http://schema.org/jobTitle> ?title } .
 OPTIONAL { ?subject <http://schema.org/url> ?homepage } .
EOF;

		$fields = array( "subject", "name", "type", "description", "image", "title", "homepage" );

        // public function execute( $fields, $whereClause = NULL, $limit = NULL, $offset = NULL, &$count = NULL, $groupBy = NULL ) {
        $results = $this->queryService->execute( "DISTINCT ?" . implode( " ?", $fields ) , $whereClause );
        $rows = &$results[ "result" ][ "rows" ];

        // var_dump($rows);
        // return;

        $index = array();
        // $this->logger->trace( "preparing index..." );
        foreach ( $rows as &$row )
            foreach ( $fields as &$field )
	            $this->addToIndex( $index, $row[ "subject" ], $row, $field );
        // $this->logger->trace( "index ready." );

		$fragment .= "<div class=\"entity-container\"><ul class=\"entity-list\">";

        while ( NULL !== ( $key = key( $index ) ) ) :

            $subject = &$index[ $key ];

            $fragment .= "<li itemscope class=\"entity-box";
            if ( NULL !== ( $type = $this->getFirstValue( $subject, "type" ) ) ) {
                $typeSimpleName = substr( $type, strrpos( $type, "/") + 1 ); 
                $htmlTypeSimpleName = htmlspecialchars( $typeSimpleName, ENT_COMPAT | ENT_HTML401, "UTF-8" );
                $fragment .= " $htmlTypeSimpleName\"";
                $fragment .= " itemtype=\"$type";
            }
            $fragment .= "\">";
            $fragment .= "<div class=\"type $htmlTypeSimpleName\"></div>";

            $fragment .= "<div class=\"names\">";
            if ( NULL !== ( $name = $this->getValueByLanguage( $subject, "name", $languages ) ) ) :
                $htmlName = htmlspecialchars( $name, ENT_COMPAT | ENT_HTML401, "UTF-8" );
                $htmlEntityLink = admin_url( "admin-ajax.php?action=wordlift.gotoentity&e=" . urlencode( $key ) );
                $fragment .= "<a class=\"name\" itemprop=\"name\" href=\"$htmlEntityLink\">$htmlName</a>\n";

            endif;
            $fragment .= "</div>";

            if ( NULL !== ( $title = $this->getValueByLanguage( $subject, "title", $languages ) ) ) :
                $htmlJobTitle = htmlspecialchars( $title, ENT_COMPAT | ENT_HTML401, "UTF-8" );
                $fragment .= "<div itemprop=\"jobTitle\" class=\"title\">$htmlJobTitle</div>\n";
            endif;

            if ( NULL !== ( $image = $this->getFirstValue( $subject, "image" ) ) ) {
                $htmlImage = htmlspecialchars( $image, ENT_COMPAT | ENT_HTML401, "UTF-8" );
                $fragment .= "<img class=\"image\" itemprop=\"image\" onerror=\"this.parentNode.removeChild( this );\" src=\"$htmlImage\" />";
            } 

            if ( NULL !== ( $description = $this->getValueByLanguage( $subject, "description", $languages ) ) ) {
                $htmlDescription = &$description; // htmlspecialchars( $description, ENT_COMPAT | ENT_HTML401, "UTF-8" );
                $fragment .= "<div class=\"description\" itemprop=\"description\">$htmlDescription<br/>";

                if ( array_key_exists( "homepage", $subject ) )
                    foreach ( $subject[ "homepage" ] as &$url ) {
                        $htmlURL = htmlspecialchars( $url[ "value" ], ENT_COMPAT | ENT_HTML401, "UTF-8" );
                        $fragment .= "<a itemprop=\"url\" href=\"$htmlURL\">$htmlURL</a><br/>\n";
                    }

                $fragment .= "</div>";

                next( $index );

            }

            $fragment .= "</li>";

        endwhile;

        $fragment .= "</ul></div>";
        // the following is executed once in the entities bar javascript.
        // $fragment .= "<script type=\"text/javascript\">jQuery( function() { jQuery('.entity-container').arrowscrollers({settings:{arrow:{width:36}}}) });</script>";

        return $fragment . $content; 
    }

    private function addToIndex( &$index, $subject, &$row, $field ) {

        // return if we have no data for that field.
        if ( ! array_key_exists( $field, $row ) || empty( $row[ $field ] ) )
            return;

    	// create an empty array for the field if it doesn't exist.
    	if ( ! array_key_exists( $subject, $index ) ) :
    		$index[ $subject ] = array( $field => array() );
        elseif ( ! array_key_exists( $field, $index[ $subject ] ) ) :
            $index[ $subject ][ $field ] = array();
        endif;

    	// if we're here, we've got data, then:
    	// set the value
        $var = array(
            "value" => $row[ $field ]
        );

        // set the language, if any
        if ( array_key_exists( "$field lang", $row ) )
            $var[ "lang" ] = $row[ "$field lang" ];

        // add the value to the array of values for the requested field
        if ( ! in_array( $var, $index[ $subject ][ $field ] ) )
            $index[ $subject ][ $field ][] = $var;
    }

    private function getFirstValue( &$array, $key ) {
        if ( 0 === count( $array[ $key ] ) )
            return NULL;

        return $array[ $key ][0][ "value" ];
    }

    private function getValueByLanguage( &$array, $key, $languages ) {
        if ( 0 === count( $array[ $key ] ) )
            return NULL;

        foreach ( $languages as &$language ) :
            foreach ( $array[ $key ] as &$object ) :
                if ( $language === $object[ "lang" ] )
                    return $object[ "value" ];
            endforeach;
        endforeach;

        // return $array[ $key ][0][ "value" ];
        return NULL;
    }

    private function getPostLanguages( $postId ) {

        $whereClause = <<<EOF

 [] a fise:Enhancement ;
    wordlift:postID "$postId" ;
    <http://purl.org/dc/terms/type> <http://purl.org/dc/terms/LinguisticSystem> ;
    <http://fise.iks-project.eu/ontology/confidence> ?confidence ;
    <http://purl.org/dc/terms/language> ?language .
EOF;

        $count = 0;
        $results = $this->queryService->execute( "DISTINCT ?language ?confidence" , $whereClause, NULL, NULL, $count, NULL, "DESC(?confidence)" );
        $rows = &$results[ "result" ][ "rows" ];

        $languages = array();
        foreach ( $rows as &$row ) :
            $languages[] = $row[ "language" ];
        endforeach;

        return $languages;

    }
}

?>