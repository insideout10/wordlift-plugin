<?php

class WordLift_QueryService {

	public $logger;

	public $storeService;
	public $defaultGraphURI;

	const RESULT_NAME = "result";
	const ROWS_NAME = "rows";

	const DELETE_COMMAND = "DELETE";
	const INSERT_COMMAND = "INSERT";

	const TYPE_NAME = "type";
	const DATATYPE_NAME = "datatype";
	const VALUE_NAME = "value";
	const LANGUAGE_NAME = "lang";

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

	public function query( $query, $format = "rows", $queryBase = "", $keepBNodeIds = false ) {
		$store = $this->storeService->getStore();
		
		// return $store->query( $query );	

		$result = $store->query( $query, $format, $queryBase, $keepBNodeIds );

		foreach ( $store->getErrors() as $error ) {
			$this->logger->error( $error );
		}

		return $result;
	}

	public function createStatement( $array, $command, $namespace = NULL ) {
		if ( NULL === $namespace )
			$namespace = $this->defaultGraphURI;

		$statement = "";

		switch ( $command ) {
			case self::DELETE_COMMAND:
				$statement .= "DELETE FROM <$namespace> {\n";
				break;
			case self::INSERT_COMMAND:
				$statement .= "INSERT INTO <$namespace> {\n";
				break;
			default:
		}

		foreach ( $array as $subject => &$predicates ) {
			foreach ( $predicates as $predicate => &$objects ) {
				foreach ( $objects as $object ) {
					$statement .= "<$subject> ";
					$statement .= $this->getPredicateAndObject( $predicate, $object ) . ". \n";
				}
			}
		}

		switch ( $command ) {
			case self::DELETE_COMMAND:
				$statement .= "}\n";
				break;
			case self::INSERT_COMMAND:
				$statement .= "}\n";
				break;
			default:
		}

		return $statement;
	}

	public function getPredicateAndObject( $predicate, $object ) {
		$type = $object[ self::TYPE_NAME ];
		$datatype = ( array_key_exists( self::DATATYPE_NAME, $object ) ? $this->escapeValue( $object[ self::DATATYPE_NAME ] ) : "" );
		$value = $this->escapeValue( $object[ self::VALUE_NAME ] );
		$language = ( array_key_exists( self::LANGUAGE_NAME, $object ) ? "@" . $object[ self::LANGUAGE_NAME ] : "" );

		switch ( $type ) {
			case "bnode":
			case "uri":
				return "<$predicate> <$value> ";
				break;
			default:
				if ( empty( $datatype ) ) 
					return "<$predicate> \"$value\"$language ";
				else
					return "<$predicate> \"$value\"^^<$datatype> ";
				break;
		}

		return "";
	}

	public function escapeValue( $value ) {
		$value = str_replace( "\\", "\u005c", $value );
		$value = str_replace( "\"", "\u0022", $value ); 
		$value = str_replace( "'", "\u0027", $value );

		return $value;
	}


	private function getCount( $whereClause, $groupBy = NULL ) {
		$store = $this->storeService->getStore();

		$query = $this->create( "COUNT( * ) as ?count", $whereClause, NULL, NULL, $groupBy );

		$recordset = $store->query( $query );
		
		return $recordset[ self::RESULT_NAME ][ self::ROWS_NAME ][ 0 ][ "count" ];
	}

}

?>