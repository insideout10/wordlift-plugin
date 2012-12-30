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

?>