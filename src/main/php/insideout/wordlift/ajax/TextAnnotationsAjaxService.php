<?php

class WordLift_TextAnnotationsAjaxService {

	public $logger;
	public $queryService;
	public $recordSetService;

	public function getTextAnnotations( $p ) {
		
		$filter = "";
		foreach ( explode( ",", $p ) as $postId ) :
			if ( ! empty( $filter ) )
				$filter .= " || ";
			$filter .= "?postId = \"$postId\"";
		endforeach;

		$postId = $this->queryService->escapeValue( $p );

		$whereClause = <<<EOF

		[] a fise:EntityAnnotation ;
			wordlift:postID ?postId ;
			wordlift:selected true ;
			<http://purl.org/dc/terms/relation> ?textAnnotation ;
			fise:entity-reference [
				a ?entityType .
			]
		FILTER( $filter )
EOF;

		// public function execute( $fields, $whereClause = NULL, $limit = NULL, $offset = NULL, &$count = NULL, $groupBy = NULL, $orderBy = NULL ) {
		$count = 0;
		$recordset = $this->queryService->execute( "DISTINCT ?textAnnotation ?entityType", $whereClause, 999, 0, $count );

		$this->recordSetService->write( $recordset, 999, 0, $count );
	}

}

?>