<?php

// const RDF_TYPE = "http://www.w3.org/1999/02/22-rdf-syntax-ns#type";
// const RDF_SEQ = "rdf:Seq";
// const RDF_LI = "rdf:li";
// const RDF_VALUE = "rdf:value";
// const GRAPH_URI = "http://wordlift.it/graph";

const DB_HOST = "localhost";
const DB_NAME = "wordlift_dev";
const DB_USER = "wordlift";
const DB_PASSWORD = "wordliftpwd";

// const BNODE_PREFIX = "_:";
// const HASH_PREFIX = "_:md5-";
// const LANGUAGE_NAME = "lang";
// const VALUE_NAME = "value";
// const TYPE_NAME = "type";
// const DATATYPE_NAME = "datatype";
// const TYPE_BNODE_NAME = "bnode";

// const DELETE_FORM_NAME = "DELETE";
// const INSERT_FORM_NAME = "INSERT";

// const CHANGESET_TYPE_URI = "http://purl.org/vocab/changeset/schema#ChangeSet";
// const CHANGESET_SUBJECT_OF_CHANGE_URI = "http://purl.org/vocab/changeset/schema#subjectOfChange";
// const CHANGESET_CREATED_DATE_URI = "http://purl.org/vocab/changeset/schema#createdDate";
// const CHANGESET_CREATOR_NAME_URI = "http://purl.org/vocab/changeset/schema#creatorName";
// const CHANGESET_CHANGE_REASON_URI = "http://purl.org/vocab/changeset/schema#changeReason";
// const CHANGESET_REMOVAL_URI = "http://purl.org/vocab/changeset/schema#removal";
// const CHANGESET_ADDITION_URI = "http://purl.org/vocab/changeset/schema#addition";
// const CHANGESET_PRECEDING_CHANGE_SET_URI = "http://purl.org/vocab/changeset/schema#precedingChangeSet";
// const RDF_STATEMENT_URI = "http://www.w3.org/1999/02/22-rdf-syntax-ns#Statement";
// const RDF_SUBJECT_URI = "http://www.w3.org/1999/02/22-rdf-syntax-ns#subject";
// const RDF_PREDICATE_URI = "http://www.w3.org/1999/02/22-rdf-syntax-ns#predicate";
// const RDF_OBJECT_URI = "http://www.w3.org/1999/02/22-rdf-syntax-ns#object";
// const RDFS_DATE_TIME_DATATYPE_URI = "http://www.w3.org/2001/XMLSchema#dateTime";

// const CHANGE_ADDITION_NAME = "addition";
// const CHANGE_REMOVAL_NAME = "removal";

const GRAPH_URI = "http://wordlift.it/graph";
const CHANGE_FORCE = false;
const CHANGE_CREATOR = "system7";

include_once( "src/main/php/log4php/Logger.php" );
include_once( "src/test/php/arc2/ARC2.php" );
include_once( "src/test/TriplesUtils.php" );
include_once( "src/test/ChangeSetService.php" );
include_once( "src/main/php/insideout/wordlift/services/TripleStoreService.php" );
include_once( "src/main/php/insideout/wordlift/services/QueryService.php" );

$logger = Logger::getLogger(__CLASS__);

$storeService = new WordLift_TripleStoreService();
$storeService->logger = Logger::getLogger( "WordLift_TripleStoreService" );
$storeService->tablePrefix = "wordlift";

$queryService = new WordLift_QueryService();
$queryService->logger = Logger::getLogger( "WordLift_QueryService" );
$queryService->storeService = $storeService;
$queryService->defaultGraphURI = GRAPH_URI;

$triplesUtils = new WordLift_TriplesUtils();
$changeSetService = new WordLift_ChangeSetService();
$changeSetService->queryService = $queryService;

$index = $triplesUtils->getIndexFromFile( "src/test/resources/sample-4.rdf" );
$newIndex = $triplesUtils->bNodesToMD5( $index );

foreach ( $newIndex as $subject => $predicates ) {

	// echo( "Getting resource predicates.\n" );
	$existingPredicates = $storeService->getResourcePredicates( $subject );

	if ( md5( serialize( $existingPredicates ) ) !== md5( serialize( $predicates ) ) ) {
		
		// echo( "Getting differences.\n" );
		$additions = $triplesUtils->getDifferences( $predicates, $existingPredicates );
		$removals = $triplesUtils->getDifferences( $existingPredicates, $predicates );

		// echo( "Getting new items.\n" );
		if ( ! CHANGE_FORCE && 0 < count( $removals ) )
			$removals = $changeSetService->getNewItems( WordLift_ChangeSetService::CHANGESET_ADDITION_URI, $subject, $removals, CHANGE_CREATOR );

		// we don't add something that has been deleted in the past (unless told so).
		if ( ! CHANGE_FORCE && 0 < count( $additions ) )
			$additions = $changeSetService->getNewItems( WordLift_ChangeSetService::CHANGESET_REMOVAL_URI, $subject, $additions, CHANGE_CREATOR );

		if ( 0 === count( $additions )
			&& 0 === count( $removals ) ) {

			continue;
		}

		echo( "changes detected [ subject :: $subject ][ additions # :: " . count( $additions ) . " ][ removals :: " . count( $removals ) . " ].\n" );

		// continue;

		// echo( "Inserting.\n" );
		if ( 0 < count( $additions ) ) {
			$insertStatement = $queryService->createStatement( array( $subject => $additions ), WordLift_QueryService::INSERT_COMMAND );

			// echo("======== INSERT ========\n");
			// echo( $insertStatement );
			// echo("======== /INSERT =======\n");

			$queryService->query( $insertStatement, "raw", "", true );
		}

		// echo( "Deleting.\n" );
		if ( 0 < count( $removals ) ) {
			$deleteStatement = $queryService->createStatement( array( $subject => $removals ), WordLift_QueryService::DELETE_COMMAND );

			echo("======== DELETE ========\n");
			echo( $deleteStatement );
			echo("======== /DELETE =======\n");

			$queryService->query( $deleteStatement, "raw", "", true );
		}

		// echo( "Getting last subject.\n" );
		$previousChangeSetSubject = $changeSetService->getLastChangeSetSubject( $subject );

		// echo( "Creating changeset.\n" );
		$changeSetStatement = $changeSetService->createStatement( $subject, date_create(), CHANGE_CREATOR, "tests", $removals, $additions, $previousChangeSetSubject );

		// echo( "======== CHANGESET =========\n" );
		// echo( $changeSetStatement );
		// echo( "======== /CHANGESET ========\n" );

		$queryService->query( $changeSetStatement, "raw", "", true );
	}
}

?>