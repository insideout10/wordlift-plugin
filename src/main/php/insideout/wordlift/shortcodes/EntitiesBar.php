<?php
/**
 * User: david
 * Date: 15/07/12 21:23
 */

class WordLift_EntitiesBar {

	public $queryService;

    public function get( $attributes, $content = NULL) {

    	// get the current post ID.
    	$postId = get_the_ID();

    	// will containt the content fragment to send back to the client.
    	$fragment = "";

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
        $results = $this->queryService->execute( "?" . implode( " ?", $fields ) , $whereClause );
        $rows = &$results[ "result" ][ "rows" ];

        // var_dump($rows);

        $index = array();
        foreach ( $rows as &$row ) {
        	// echo( $row["subject"] . "<br/>" );
            foreach ( $fields as &$field )
	            $this->addToIndex( $index, $row[ "subject" ], $row, $field );
	    }

		$fragment .= "<div class=\"entity-container\"><ul class=\"entity-list\">";

        foreach ( $index as &$subject ) {

            $fragment .= "<li class=\"entity-box\" itemscope ";
            if ( NULL !== ( $type = $this->getFirstValue( $subject, "type" ) ) )
                $fragment .= " itemtype=\"$type\"";
            $fragment .= ">";

            $fragment .= "<div class=\"names\">";
            foreach ( $subject[ "name" ] as &$name ) {
                $htmlName = htmlspecialchars( $name[ "value" ], ENT_COMPAT | ENT_HTML401, "UTF-8" );
                $htmlNameLanguage = htmlspecialchars( $name[ "lang" ], ENT_COMPAT | ENT_HTML401, "UTF-8" );
                $fragment .= "<div class=\"name\" itemprop=\"name\" lang=\"$htmlNameLanguage\">$htmlName</div>\n";
            }
            $fragment .= "</div>";

            foreach ( $subject[ "title" ] as &$jobTitle ) {
                $htmlJobTitle = htmlspecialchars( $jobTitle[ "value" ], ENT_COMPAT | ENT_HTML401, "UTF-8" );
                $htmlJobTitleLanguage = htmlspecialchars( $jobTitle[ "lang" ], ENT_COMPAT | ENT_HTML401, "UTF-8" );
                $fragment .= "<div itemprop=\"jobTitle\" lang=\"$htmlJobTitleLanguage\">$htmlJobTitle</div>\n";
            }

            if ( NULL !== ( $image = $this->getFirstValue( $subject, "image" ) ) ) {
                $htmlImage = htmlspecialchars( $image, ENT_COMPAT | ENT_HTML401, "UTF-8" );
                // $fragment .= "<div style=\"width: 120px; height: 120px; background-size: contain; background-repeat: no-repeat; background-position: center; background-image: url( '$htmlImage' );\"></div>";
                $fragment .= "<img class=\"image\" itemprop=\"image\" onerror=\"this.parentNode.removeChild( this );\" src=\"$htmlImage\" />";
            } 

            if ( NULL !== ( $description = $this->getFirstValue( $subject, "description" ) ) ) {
            	$description = &$subject[ "description" ][0];
                $htmlDescription = htmlspecialchars( $description[ "value" ], ENT_COMPAT | ENT_HTML401, "UTF-8" );
                $htmlDescriptionLanguage = htmlspecialchars( $description[ "lang" ], ENT_COMPAT | ENT_HTML401, "UTF-8" );
                $fragment .= "<div class=\"description\" itemprop=\"description\" lang=\"$htmlDescriptionLanguage\">$htmlDescription<br/>";

                foreach ( $subject[ "homepage" ] as &$url ) {
                    $htmlURL = htmlspecialchars( $url[ "value" ], ENT_COMPAT | ENT_HTML401, "UTF-8" );
                    $fragment .= "<a itemprop=\"url\" href=\"$htmlURL\">$htmlURL</a><br/>\n";
                }

                $fragment .= "</div>";

            }

            $fragment .= "</li>";

        }

        $fragment .= "</ul></div>";
        $fragment .= "<script type=\"text/javascript\">jQuery( function() { jQuery('.entity-container').arrowscrollers({settings:{arrow:{width:36}}}) });</script>";

        return $fragment . $content; 
    }

    private function addToIndex( &$index, $subject, &$row, $field ) {

    	// create an empty array for the field if it doesn't exist.
    	if ( ! array_key_exists( $subject, $index ) )
    		$index[ $subject ] = array();

    	$item = &$index[ $subject ];
    	if ( ! array_key_exists( $field, $item ) )
    		$item[ $field ] = array();

    	// return if we have no data for that field.
    	if ( ! array_key_exists( $field, $row ) || empty( $row[ $field ] ) )
    		return;

    	// if we're here, we've got data, then:
    	// set the value
        $var = array(
            "value" => $row[ $field ]
        );

        // set the language, if any
        if ( array_key_exists( "$field lang", $row ) )
            $var[ "lang" ] = $row[ "$field lang" ];

        // add the value to the array of values for the requested field
        if ( ! in_array( $var, $item[ $field ] ) )
            $item[ $field ][] = $var;
    }

    private function getFirstValue( &$array, $key ) {
        if ( 0 === count( $array[ $key ] ) )
            return NULL;

        return $array[ $key ][0][ "value" ];
    }
}

?>