<?php

class WordLift_EntityAjaxService {
	
	public $queryService;
	public $recordSetService;

	public function get( $subject ) {

		$whereClause = <<<EOF
					 	<http://dbpedia.org/resource/$subject> ?key ?value

EOF;

		$count = 0;
		$recordset = $this->queryService->execute( "?key ?value", $whereClause, $limit, $offset, $count );

		$this->recordSetService->write( $recordset, $limit, $offset, $count );

	}

}

?>