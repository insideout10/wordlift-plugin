<?php

class WordLift_EntityAjaxService {
	
	public $queryService;
	public $recordSetService;

	const DEFAULT_NAMESPACE = "http://dbpedia.org/resource/";

	public function get( $subject ) {

		if ( ! strpos( $subject, ":" ) )
			$subject = self::DEFAULT_NAMESPACE . $subject;

		$whereClause = <<<EOF
					 	<$subject> ?key ?value

EOF;

		$count = 0;
		$recordset = $this->queryService->execute( "?key ?value", $whereClause, $limit, $offset, $count );

		$this->recordSetService->write( $recordset, $limit, $offset, $count );

	}

}

?>