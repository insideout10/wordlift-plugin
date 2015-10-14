<?php

class WordLift_LanguagesAjaxService {

	public $queryService;
	public $recordSetService;

	public function get() {

		$whereClause = <<<EOF

	[] a fise:Enhancement ;
		wordlift:selected true ;
		fise:entity-reference ?subject .
	?subject a ?a ;
		schema:name ?name .
EOF;

		if ( ! empty( $name ) ) :
			$escNameFilter = $this->queryService->escapeValue( $name );
			$whereClause .= " FILTER regex( str(?name), \"$escNameFilter\", \"i\" )";
		endif;

		// public function execute( $fields, $whereClause = NULL, $limit = NULL, $offset = NULL, &$count = NULL, $groupBy = NULL, $orderBy = NULL ) {
		$count = 0;
		$recordset = $this->queryService->execute( "DISTINCT lang(?name) AS ?language", $whereClause, $limit, $offset, $count, "?name", $order );
		// var_export( $recordset );

		$this->recordSetService->write( $recordset, $limit, $offset, $count );

	}

}

?>