<?php

class WordLift_ChangeSetService {

	public $queryService;

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

	/**
	 * @param type CHANGESET_REMOVAL_URI or CHANGESET_ADDITION_URI
	 */
	public function getNewItems( $subject, $differences, $type ) {

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
				$statement .= " FILTER( ?creator != \"" . self::CHANGE_CREATOR . "\" ) . \n";
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

}

?>