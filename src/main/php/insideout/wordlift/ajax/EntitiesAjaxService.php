<?php

class WordLift_EntitiesAjaxService {
	
	public $queryService;
	public $recordSetService;

	const DEFAULT_LIMIT = 10;
	const DEFAULT_OFFSET = 0;


	public function get( $limit = self::DEFAULT_LIMIT, $offset = self::DEFAULT_OFFSET ) {

		$whereClause = <<<EOF
					 	?s a ?type;
					 	   schema:name ?name

EOF;

		$count = 0;
		$recordset = $this->queryService->execute( "?type ?name", $whereClause, $limit, $offset, $count );

		$this->recordSetService->write( $recordset, $count, $offset );

	}

}

?>