<?php

const DB_HOST = "localhost";
const DB_NAME = "wordlift_dev";
const DB_USER = "wordlift";
const DB_PASSWORD = "wordliftpwd";

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
$changeSetService->storeService = $storeService;
$changeSetService->triplesUtils = $triplesUtils;

$index = $triplesUtils->getIndexFromFile( "src/test/resources/sample-4.rdf" );
$newIndex = $triplesUtils->bNodesToMD5( $index );

$changeSetService->applyChanges( $newIndex, CHANGE_CREATOR, CHANGE_FORCE, $reason = "none given" );

// foreach ( $newIndex as $subject => $predicates ) {

// 	// echo( "Getting resource predicates.\n" );
// 	$existingPredicates = $storeService->getResourcePredicates( $subject );

// 	if ( md5( serialize( $existingPredicates ) ) !== md5( serialize( $predicates ) ) ) {
		
// 		// echo( "Getting differences.\n" );
// 		$additions = $triplesUtils->getDifferences( $predicates, $existingPredicates );
// 		$removals = $triplesUtils->getDifferences( $existingPredicates, $predicates );

// 		// echo( "Getting new items.\n" );
// 		if ( ! CHANGE_FORCE && 0 < count( $removals ) )
// 			$removals = $changeSetService->getNewItems( WordLift_ChangeSetService::CHANGESET_ADDITION_URI, $subject, $removals, CHANGE_CREATOR );

// 		// we don't add something that has been deleted in the past (unless told so).
// 		if ( ! CHANGE_FORCE && 0 < count( $additions ) )
// 			$additions = $changeSetService->getNewItems( WordLift_ChangeSetService::CHANGESET_REMOVAL_URI, $subject, $additions, CHANGE_CREATOR );

// 		if ( 0 === count( $additions )
// 			&& 0 === count( $removals ) ) {

// 			continue;
// 		}

// 		echo( "changes detected [ subject :: $subject ][ additions # :: " . count( $additions ) . " ][ removals :: " . count( $removals ) . " ].\n" );

// 		// continue;

// 		// echo( "Inserting.\n" );
// 		if ( 0 < count( $additions ) ) {
// 			$insertStatement = $queryService->createStatement( array( $subject => $additions ), WordLift_QueryService::INSERT_COMMAND );

// 			// echo("======== INSERT ========\n");
// 			// echo( $insertStatement );
// 			// echo("======== /INSERT =======\n");

// 			$queryService->query( $insertStatement, "raw", "", true );
// 		}

// 		// echo( "Deleting.\n" );
// 		if ( 0 < count( $removals ) ) {
// 			$deleteStatement = $queryService->createStatement( array( $subject => $removals ), WordLift_QueryService::DELETE_COMMAND );

// 			echo("======== DELETE ========\n");
// 			echo( $deleteStatement );
// 			echo("======== /DELETE =======\n");

// 			$queryService->query( $deleteStatement, "raw", "", true );
// 		}

// 		// echo( "Getting last subject.\n" );
// 		$previousChangeSetSubject = $changeSetService->getLastChangeSetSubject( $subject );

// 		// echo( "Creating changeset.\n" );
// 		$changeSetStatement = $changeSetService->createStatement( $subject, date_create(), CHANGE_CREATOR, "tests", $removals, $additions, $previousChangeSetSubject );

// 		// echo( "======== CHANGESET =========\n" );
// 		// echo( $changeSetStatement );
// 		// echo( "======== /CHANGESET ========\n" );

// 		$queryService->query( $changeSetStatement, "raw", "", true );
// 	}
// }

?>