<?php

class WordLift_EntitiesAjaxService {
	
	public $queryService;
	public $recordSetService;

	const DEFAULT_LIMIT = 10;
	const DEFAULT_OFFSET = 0;


	public function get( $limit = self::DEFAULT_LIMIT, $offset = self::DEFAULT_OFFSET ) {

		$whereClause = <<<EOF
					 	?subject a ?a;
					 	   schema:name ?name

EOF;

		$count = 0;
		$recordset = $this->queryService->execute( "?subject ?a ?name", $whereClause, $limit, $offset, $count );

		$this->recordSetService->write( $recordset, $limit, $offset, $count );

	}

}

?>