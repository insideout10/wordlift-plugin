<?php

class WordLift_QueryService {

	public $storeService;

	const RESULT_NAME = "result";
	const ROWS_NAME = "rows";

	public function create( $fields, $whereClause = NULL, $limit = NULL, $offset = NULL, $groupBy = NULL ) {

		$query = "SELECT $fields";

		if ( NULL != $whereClause )
			$query .= " WHERE { $whereClause }";

		if ( NULL != $groupBy )
			$query .= " GROUP BY $groupBy";

		if ( NULL != $limit && is_numeric( $limit ) )
			$query .= " LIMIT $limit";

		if ( NULL != $offset && is_numeric( $offset ))
			$query .= " OFFSET $offset";

		return $query;
	}

	public function execute( $fields, $whereClause = NULL, $limit = NULL, $offset = NULL, &$count = NULL, $groupBy = NULL ) {
		$store = $this->storeService->getStore();

		$query = $this->create( $fields, $whereClause, $limit, $offset, $groupBy );

		if ( NULL !== $count )
			$count = $this->getCount( $whereClause );

		return $store->query( $query );
	}

	public function query( $query ) {
		$store = $this->storeService->getStore();
		
		return $store->query( $query );	
	}

	private function getCount( $whereClause, $groupBy = NULL ) {
		$store = $this->storeService->getStore();

		$query = $this->create( "COUNT( * ) as ?count", $whereClause, NULL, NULL, $groupBy );

		$recordset = $store->query( $query );
		
		return $recordset[ self::RESULT_NAME ][ self::ROWS_NAME ][ 0 ][ "count" ];
	}

}

?>