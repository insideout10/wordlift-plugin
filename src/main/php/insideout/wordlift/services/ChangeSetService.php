<?php

class WordLift_ChangeSetService {

	public $queryService;
	public $storeService;
	public $triplesUtils;

	const CHANGESET_TYPE_URI = "http://purl.org/vocab/changeset/schema#ChangeSet";
	const CHANGESET_SUBJECT_OF_CHANGE_URI = "http://purl.org/vocab/changeset/schema#subjectOfChange";
	const CHANGESET_CREATED_DATE_URI = "http://purl.org/vocab/changeset/schema#createdDate";
	const CHANGESET_CREATOR_NAME_URI = "http://purl.org/vocab/changeset/schema#creatorName";
	const CHANGESET_CHANGE_REASON_URI = "http://purl.org/vocab/changeset/schema#changeReason";
	const CHANGESET_REMOVAL_URI = "http://purl.org/vocab/changeset/schema#removal";
	const CHANGESET_ADDITION_URI = "http://purl.org/vocab/changeset/schema#addition";
	const CHANGESET_PRECEDING_CHANGE_SET_URI = "http://purl.org/vocab/changeset/schema#precedingChangeSet";
	const RDF_STATEMENT_URI = "http://www.w3.org/1999/02/22-rdf-syntax-ns#Statement";
	const RDF_SUBJECT_URI = "http://www.w3.org/1999/02/22-rdf-syntax-ns#subject";
	const RDF_PREDICATE_URI = "http://www.w3.org/1999/02/22-rdf-syntax-ns#predicate";
	const RDF_OBJECT_URI = "http://www.w3.org/1999/02/22-rdf-syntax-ns#object";
	const RDFS_DATE_TIME_DATATYPE_URI = "http://www.w3.org/2001/XMLSchema#dateTime";

	const CHANGE_ADDITION_NAME = "addition";
	const CHANGE_REMOVAL_NAME = "removal";

	const BNODE_PREFIX = "_:";

	/**
	 * @param type CHANGESET_REMOVAL_URI or CHANGESET_ADDITION_URI
	 */
	public function getNewItems( $type, $subject, $differences, $creatorName ) {

		$results = array();

		foreach ( $differences as $predicate => $objects ) {
			foreach ( $objects as $object ) {
				$statement = "ASK WHERE {\n";
				$statement .= " ?changeSet a <" . self::CHANGESET_TYPE_URI . "> ; \n";
				$statement .= "            <" . self::CHANGESET_SUBJECT_OF_CHANGE_URI . "> <$subject> ; \n";
				$statement .= "            <" . self::CHANGESET_CREATOR_NAME_URI . "> ?creator ; \n";
				$statement .= "            <$type> [ \n";
				$statement .= "            <" . self::RDF_SUBJECT_URI . "> <$subject>; \n";
				$statement .= "            <" . self::RDF_PREDICATE_URI . "> <$predicate>; \n";
				$statement .= "            " . $this->queryService->getPredicateAndObject( self::RDF_OBJECT_URI , $object ) . " ] . \n";
				$statement .= " FILTER( ?creator != \"$creatorName\" ) . \n";
				$statement .= "} \n";

				// if true, that statement has been already removed in the past by a different user.
				$exists = $this->queryService->query( $statement, "raw", "", true );

				if ( ! $exists ) {
					$results[ $predicate ][] = $object;
				}
			}
		}

		return $results;
	}

	public function getLastChangeSetSubject( $subject ) {
		$statement = "SELECT ?changeSetSubject \n";
		$statement .= " WHERE { \n";
		$statement .= "		?changeSetSubject a <" . self::CHANGESET_TYPE_URI . "> ; \n";
		$statement .= "		                  <" . self::CHANGESET_SUBJECT_OF_CHANGE_URI . "> <$subject> ; \n";
		$statement .= "		                  <" . self::CHANGESET_CREATED_DATE_URI . "> ?createdDate } \n";
		$statement .= "	ORDER BY DESC(?createdDate) LIMIT 1 \n";

		$recordset = $this->queryService->query( $statement, "raw", "", true );
		
		$previousChangeSetSubject = NULL;
		if ( array_key_exists( "rows", $recordset )
			&& 1 === count( $recordset[ "rows" ] ) ) {

			$previousChangeSetSubject = $recordset[ "rows" ][ 0 ][ "changeSetSubject" ];
		}

		return $previousChangeSetSubject;
	}

	public function createStatement( $subjectOfChange, $createdDate, $creatorName, $changeReason, $removals, $additions, $precedingChangeSet, $namespace = NULL ) {

		if ( NULL === $namespace)
			$namespace = $this->queryService->defaultGraphURI;

		$subjectOfChange = $this->queryService->escapeValue( $subjectOfChange );
		$creatorName = $this->queryService->escapeValue( $creatorName );
		$changeReason = $this->queryService->escapeValue( $changeReason );
		
		$createdDate = date_format( $createdDate, "Y-m-d\TH:i:s\Z" );

		$subject = self::BNODE_PREFIX . uniqid( "changeset-" );
		$statement = "INSERT INTO <$namespace> {\n";
		$statement .= "<$subject> a <" . self::CHANGESET_TYPE_URI . "> ; \n";
		$statement .= "           <" . self::CHANGESET_SUBJECT_OF_CHANGE_URI . "> <$subjectOfChange> ; \n";
		if ( NULL !== $precedingChangeSet )
			$statement .= "           <" . self::CHANGESET_PRECEDING_CHANGE_SET_URI . "> <$precedingChangeSet> ; \n";

		$statement .= "           <" . self::CHANGESET_CREATED_DATE_URI . "> \"$createdDate\"^^<" . self::RDFS_DATE_TIME_DATATYPE_URI . "> ; \n";
		$statement .= "           <" . self::CHANGESET_CREATOR_NAME_URI . "> \"$creatorName\" ; \n";
		$statement .= "           <" . self::CHANGESET_CHANGE_REASON_URI . "> \"$changeReason\" . \n";


		if ( 0 < count( $additions ) )
			$statement .= $this->getChanges( $subject, $subjectOfChange, $additions, self::CHANGE_ADDITION_NAME );

		if ( 0 < count( $removals ) )
			$statement .= $this->getChanges( $subject, $subjectOfChange, $removals, self::CHANGE_REMOVAL_NAME ); 

		$statement .= "}";

		return $statement;
	}

	public function getChanges( $subject, $subjectOfChange, $changes, $changeType ) {

		switch ( $changeType ) {
			case self::CHANGE_ADDITION_NAME:
				$type = self::CHANGESET_ADDITION_URI;
				break;
			case self::CHANGE_REMOVAL_NAME:
				$type = self::CHANGESET_REMOVAL_URI;
				break;
		}

		$statement = "";
		if ( 0 < count( $changes ) ) {
			foreach ( $changes as $predicate => $objects ) {
				foreach ( $objects as $object ) {
					$changeSubject = self::BNODE_PREFIX . uniqid( "changeset-$changeType-" );
					$statement .= "<$subject> <$type> <$changeSubject> . \n";
					$statement .= "<$changeSubject> a <" . self::RDF_STATEMENT_URI . "> ; \n";
					$statement .= "                   <" . self::RDF_SUBJECT_URI . "> <$subjectOfChange> ; \n";
					$statement .= "                   <" . self::RDF_PREDICATE_URI . "> <$predicate> ; \n";
					$statement .= $this->queryService->getPredicateAndObject( self::RDF_OBJECT_URI, $object ) . " . \n";
				}
			}
		}	

		return $statement;
	}

	public function applyChanges( &$newIndex, $creator, $force, $reason = "none given" ) {

		foreach ( $newIndex as $subject => $predicates ) {

			// echo( "Getting resource predicates.\n" );
			$existingPredicates = $this->storeService->getResourcePredicates( $subject );

			if ( md5( serialize( $existingPredicates ) ) === md5( serialize( $predicates ) ) )
				continue;
				
			// echo( "Getting differences.\n" );
			$additions = $this->triplesUtils->getDifferences( $predicates, $existingPredicates );
			$removals = $this->triplesUtils->getDifferences( $existingPredicates, $predicates );

			// echo( "Getting new items.\n" );
			if ( ! $force && 0 < count( $removals ) )
				$removals = $this->getNewItems( self::CHANGESET_ADDITION_URI, $subject, $removals, $creator );

			// we don't add something that has been deleted in the past (unless told so).
			if ( ! $force && 0 < count( $additions ) )
				$additions = $this->getNewItems( self::CHANGESET_REMOVAL_URI, $subject, $additions, $creator );

			if ( 0 === count( $additions )
				&& 0 === count( $removals ) ) {

				continue;
			}

			echo( "changes detected [ subject :: $subject ][ additions # :: " . count( $additions ) . " ][ removals :: " . count( $removals ) . " ].\n" );

			// continue;

			// echo( "Inserting.\n" );
			if ( 0 < count( $additions ) ) {
				$insertStatement = $this->queryService->createStatement( array( $subject => $additions ), WordLift_QueryService::INSERT_COMMAND );

				// echo("======== INSERT ========\n");
				// echo( $insertStatement );
				// echo("======== /INSERT =======\n");

				$this->queryService->query( $insertStatement, "raw", "", true );
			}

			// echo( "Deleting.\n" );
			if ( 0 < count( $removals ) ) {
				$deleteStatement = $this->queryService->createStatement( array( $subject => $removals ), WordLift_QueryService::DELETE_COMMAND );

				// echo("======== DELETE ========\n");
				// echo( $deleteStatement );
				// echo("======== /DELETE =======\n");

				$this->queryService->query( $deleteStatement, "raw", "", true );
			}

			// echo( "Getting last subject.\n" );
			$previousChangeSetSubject = $this->getLastChangeSetSubject( $subject );

			// echo( "Creating changeset.\n" );
			$changeSetStatement = $this->createStatement( $subject, date_create(), $creator, $reason, $removals, $additions, $previousChangeSetSubject );

			// echo( "======== CHANGESET =========\n" );
			// echo( $changeSetStatement );
			// echo( "======== /CHANGESET ========\n" );

			$this->queryService->query( $changeSetStatement, "raw", "", true );

		}
	}
}

?>