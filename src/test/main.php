<?php

const RDF_TYPE = "http://www.w3.org/1999/02/22-rdf-syntax-ns#type";
const RDF_SEQ = "rdf:Seq";
const RDF_LI = "rdf:li";
const RDF_VALUE = "rdf:value";
const GRAPH_URI = "http://wordlift.it/graph";

const DB_HOST = "localhost";
const DB_NAME = "wordlift_dev";
const DB_USER = "wordlift";
const DB_PASSWORD = "wordliftpwd";

const BNODE_PREFIX = "_:";
const HASH_PREFIX = "_:md5-";
const LANGUAGE_NAME = "lang";
const VALUE_NAME = "value";
const TYPE_NAME = "type";
const DATATYPE_NAME = "datatype";
const TYPE_BNODE_NAME = "bnode";

const DELETE_FORM_NAME = "DELETE";
const INSERT_FORM_NAME = "INSERT";

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

// $store = getStore();

foreach ( $newIndex as $subject => $predicates ) {
	$existingPredicates = $storeService->getResourcePredicates( $subject );

	if ( md5( serialize( $existingPredicates ) ) !== md5( serialize( $predicates ) ) ) {
		
		$additions = $triplesUtils->getDifferences( $predicates, $existingPredicates );
		$removals = $triplesUtils->getDifferences( $existingPredicates, $predicates );

		if ( ! CHANGE_FORCE && 0 < count( $removals ) )
			$removals = $changeSetService->getNewItems( $subject, $removals, WordLift_ChangeSetService::CHANGESET_ADDITION_URI );


		// we don't add something that has been deleted in the past (unless told so).
		if ( ! CHANGE_FORCE && 0 < count( $additions ) )
			$additions = $changeSetService->getNewItems( $subject, $additions, WordLift_ChangeSetService::CHANGESET_REMOVAL_URI);

		if ( 0 === count( $additions )
			&& 0 === count( $removals ) ) {

			continue;
		}

		echo( "changes detected [ subject :: $subject ][ additions # :: " . count( $additions ) . " ][ removals :: " . count( $removals ) . " ].\n" );

		// continue;

		if ( 0 < count( $additions ) ) {


			$insertStatement = $queryService->createStatement( array( $subject => $additions ), WordLift_QueryService::INSERT_COMMAND );

			// echo("======== INSERT ========\n");
			// echo( $insertStatement );
			// echo("======== /INSERT =======\n");

			$queryService->query( $insertStatement, "raw", "", true );
		}

		if ( 0 < count( $removals ) ) {
			$deleteStatement = $queryService->createStatement( array( $subject => $removals ), WordLift_QueryService::DELETE_COMMAND );

			echo("======== DELETE ========\n");
			echo( $deleteStatement );
			echo("======== /DELETE =======\n");

			$queryService->query( $deleteStatement, "raw", "", true );
		}
		// continue;

		$statement = "SELECT ?changeSetSubject \n";
		$statement .= " WHERE { \n";
		$statement .= "		?changeSetSubject a <" . CHANGESET_TYPE_URI . "> ; \n";
		$statement .= "		                  <" . CHANGESET_SUBJECT_OF_CHANGE_URI . "> <$subject> ; \n";
		$statement .= "		                  <" . CHANGESET_CREATED_DATE_URI . "> ?createdDate } \n";
		$statement .= "	ORDER BY DESC(?createdDate) LIMIT 1 \n";

		$recordset = $queryService->query( $statement, "raw", "", true );
		$previousChangeSetSubject = NULL;
		if ( array_key_exists( "rows", $recordset )
			&& 1 === count( $recordset[ "rows" ] ) ) {

			$previousChangeSetSubject = $recordset[ "rows" ][ 0 ][ "changeSetSubject" ];
		}

		$changeSetStatement = createChangeSetStatement( $subject, date_create(), CHANGE_CREATOR, "tests", $removals, $additions, $previousChangeSetSubject, GRAPH_URI );

		// echo( "======== CHANGESET =========\n" );
		// echo( $changeSetStatement );
		// echo( "======== /CHANGESET ========\n" );

		$queryService->query( $changeSetStatement, "raw", "", true );

		// echo( "======== ADDITIONS =========\n" );
		// var_dump( $additions );
		// echo( "======== /ADDITIONS ========\n" );
		// echo( "======== REMOVALS ==========\n" );
		// var_dump( $removals );
		// echo( "======== /REMOVALS =========\n" );
	}
}

// /**
//  * @param type CHANGESET_REMOVAL_URI or CHANGESET_ADDITION_URI
//  */
// function getNewItems( $subject, $differences, $type, $queryService ) {

// 	$results = array();

// 	foreach ( $differences as $predicate => $objects ) {
// 		foreach ( $objects as $object ) {
// 			$statement = "ASK WHERE {\n";
// 			$statement .= " ?changeSet a <" . CHANGESET_TYPE_URI . "> ; \n";
// 			$statement .= "            <" . CHANGESET_SUBJECT_OF_CHANGE_URI . "> <$subject> ; \n";
// 			$statement .= "            <" . CHANGESET_CREATOR_NAME_URI . "> ?creator ; \n";
// 			$statement .= "            <$type> [ \n";
// 			$statement .= "            <" . RDF_SUBJECT_URI . "> <$subject>; \n";
// 			$statement .= "            <" . RDF_PREDICATE_URI . "> <$predicate>; \n";
// 			$statement .= "            " . getPredicateAndObject( RDF_OBJECT_URI , $object ) . " ] . \n";
// 			$statement .= " FILTER( ?creator != \"" . CHANGE_CREATOR . "\" ) . \n";
// 			$statement .= "} \n";

// 			// if true, that statement has been already removed in the past by a different user.
// 			$exists = $queryService->query( $statement, "raw", "", true );

// 			if ( ! $exists ) {
// 				$results[ $predicate ][] = $object;
// 			}
// 		}
// 	}

// 	return $results;
// }

function createChangeSetStatement( $subjectOfChange, $createdDate, $creatorName, $changeReason, $removals, $additions, $precedingChangeSet, $namespace ) {

	$subjectOfChange = escapeValue( $subjectOfChange );
	$creatorName = escapeValue( $creatorName );
	$changeReason = escapeValue( $changeReason );
	
	$createdDate = date_format( $createdDate, "Y-m-d\TH:i:s\Z" );

	$subject = BNODE_PREFIX . uniqid( "changeset-" );
	$statement = "INSERT INTO <$namespace> {\n";
	$statement .= "<$subject> <" . RDF_TYPE . "> <" . CHANGESET_TYPE_URI . "> ; \n";
	$statement .= "           <" . CHANGESET_SUBJECT_OF_CHANGE_URI . "> <$subjectOfChange> ; \n";
	if ( NULL !== $precedingChangeSet )
		$statement .= "           <" . CHANGESET_PRECEDING_CHANGE_SET_URI . "> <$precedingChangeSet> ; \n";

	$statement .= "           <" . CHANGESET_CREATED_DATE_URI . "> \"$createdDate\"^^<" . RDFS_DATE_TIME_DATATYPE_URI . "> ; \n";
	$statement .= "           <" . CHANGESET_CREATOR_NAME_URI . "> \"$creatorName\" ; \n";
	$statement .= "           <" . CHANGESET_CHANGE_REASON_URI . "> \"$changeReason\" . \n";


	if ( 0 < count( $additions ) )
		$statement .= getChangeSetChanges( $subject, $subjectOfChange, $additions, CHANGE_ADDITION_NAME );

	if ( 0 < count( $removals ) )
		$statement .= getChangeSetChanges( $subject, $subjectOfChange, $removals, CHANGE_REMOVAL_NAME ); 

	$statement .= "}";

	return $statement;
}

function getChangeSetChanges( $subject, $subjectOfChange, $changes, $changeType ) {

	switch ( $changeType ) {
		case CHANGE_ADDITION_NAME:
			$type = CHANGESET_ADDITION_URI;
			break;
		case CHANGE_REMOVAL_NAME:
			$type = CHANGESET_REMOVAL_URI;
			break;
	}

	$statement = "";
	if ( 0 < count( $changes ) ) {
		foreach ( $changes as $predicate => $objects ) {
			foreach ( $objects as $object ) {
				$changeSubject = BNODE_PREFIX . uniqid( "changeset-$changeType-" );
				$statement .= "<$subject> <$type> <$changeSubject> . \n";
				$statement .= "<$changeSubject> <" . RDF_TYPE . "> <" . RDF_STATEMENT_URI . "> ; \n";
				$statement .= "                   <" . RDF_SUBJECT_URI . "> <$subjectOfChange> ; \n";
				$statement .= "                   <" . RDF_PREDICATE_URI . "> <$predicate> ; \n";
				$statement .= getPredicateAndObject( RDF_OBJECT_URI, $object ) . " . \n";
			}
		}
	}	

	return $statement;
}

// function query( $statement , "raw", "", true) {
// 	$result = $store->query( $statement, "raw", "", true );

// 	foreach ( $store->getErrors() as $error ) {
// 		var_dump( $error );
// 	}

// 	return $result;
// }

function escapeValue( $value ) {
	$value = str_replace( "\\", "\u005c", $value );
	$value = str_replace( "\"", "\u0022", $value ); 
	$value = str_replace( "'", "\u0027", $value );

	return $value;
}

// function createStatement( $array, $form, $namespace = "" ) {

// 	$statement = "";

// 	switch ( $form ) {
// 		case DELETE_FORM_NAME:
// 			$statement .= "DELETE FROM <$namespace> {\n";
// 			break;
// 		case INSERT_FORM_NAME:
// 			$statement .= "INSERT INTO <$namespace> {\n";
// 			break;
// 		default:
// 	}

// 	foreach ( $array as $subject => &$predicates ) {
// 		foreach ( $predicates as $predicate => &$objects ) {
// 			foreach ( $objects as $object ) {
// 				$statement .= "<$subject> ";
// 				$statement .= getPredicateAndObject( $predicate, $object ) . ". \n";
// 			}
// 		}
// 	}

// 	switch ( $form ) {
// 		case DELETE_FORM_NAME:
// 			$statement .= "}\n";
// 			break;
// 		case INSERT_FORM_NAME:
// 			$statement .= "}\n";
// 			break;
// 		default:
// 	}

// 	return $statement;
// }

function getPredicateAndObject( $predicate, $object ) {
	$type = $object[ TYPE_NAME ];
	$datatype = ( array_key_exists( DATATYPE_NAME, $object ) ? escapeValue( $object[ DATATYPE_NAME ] ) : "" );
	$value = escapeValue( $object[ VALUE_NAME ] );
	$language = ( array_key_exists( LANGUAGE_NAME, $object ) ? "@" . $object[ LANGUAGE_NAME ] : "" );

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

// function getDifferences( &$predicates, &$referencePredicates ) {
// 	$differences = array();

// 	foreach ( $predicates as $predicate => &$objects ) {
// 		if ( ! array_key_exists( $predicate, $referencePredicates ) ) {
// 			$differences[ $predicate ] = $objects;
// 			continue;
// 		}

// 		foreach ( $objects as &$object ) {

// 			$found = false;
// 			foreach ( $referencePredicates[ $predicate ] as $referenceObject ) {
// 				if ( md5( serialize( $object ) ) ===  md5( serialize( $referenceObject ) ) ) {
// 					$found = true;
// 					break;
// 				}
// 			}

// 			if ( $found ) // on to the next object, if this one has been found.
// 				continue;
// 			else { // add the object if not found.
// 				$differences[ $predicate ][] = $object;
// 			}
// 		}
// 	}

// 	return $differences;
// }

// function getStore() {
// 	$store = ARC2::getStore( getConfig() );
// 	// $store->drop();

// 	if (!$store->isSetUp())
// 		$store->setUp();

// 	return $store;
// }

// function getConfig() {
// 	return array(
//         "ns" => array(
//             "rdf" => "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
//             "rdfs" => "http://www.w3.org/2000/01/rdf-schema#",
//             "dbpedia" => "http://dbpedia.org/ontology/",
//             "schema" => "http://schema.org/",
//             "fise" => "http://fise.iks-project.eu/ontology/",
//             "wordlift" => "http://purl.org/insideout/wordpress/",
//             "dcterms" => "http://purl.org/dc/terms/"
//         ),
//   		"bnode_prefix" => "bn",
//         "db_host" => DB_HOST,
//         "db_name" => DB_NAME,
//         "db_user" => DB_USER,
//         "db_pwd" => DB_PASSWORD,
//         "store_name" => "wordlift"
// 	);
// }

// function getResourcePredicates( $subject, &$store ) {
// 	$resource = ARC2::getResource();
// 	$resource->setStore( $store );
// 	$resource->setURI( $subject );

// 	$properties = $resource->getProps();

// 	return $properties;
// }

// function processNodes( $index ) {
// 	$newIndex = array();
// 	$bNodesMap = array();

// 	while ( NULL !== ( $subject = key( $index ) ) )
// 		processNode( $index, $subject, $newIndex, $bNodesMap );

// 	return $newIndex;
// }

// function processNode( &$index, $subject, &$newIndex, &$bNodesMap ) {
// 	// echo( "processing [ subject :: $subject ]...\n" );

// 	if ( array_key_exists( $subject, $bNodesMap ) ) {
// 		// echo( "node already processed [ subject :: $subject ].\n" );
// 		return $bNodesMap[ $subject ];
// 	} 

// 	if ( ! array_key_exists( $subject, $index ) ) {
// 		// echo( "subject not found [ subject :: $subject ].\n" );
// 		return;
// 	}

// 	$predicates = &$index[ $subject ];

// 	// check if the value references to a bnode.
// 	foreach ( $predicates as $predicate => &$objects ) {
// 		foreach ( $objects as &$object ) {
// 			if ( is_array( $object )
// 				&& array_key_exists( TYPE_NAME, $object)
// 				&& TYPE_BNODE_NAME === $object[ TYPE_NAME ]
// 				&& array_key_exists( VALUE_NAME, $object )
//  				// we don't take processed bnodes.
// 				&& ( 0 !== strpos( $object[ VALUE_NAME ], HASH_PREFIX ) ) ) {

// 				$bNodeName = $object[ VALUE_NAME ];
// 				// echo( "found a BNode [ bNodeName :: $bNodeName ].\n" );

// 				$object[ VALUE_NAME ] = processNode( $index, $bNodeName, $newIndex, $bNodesMap );
// 				// echo( "replacing [ nNodeName :: $bNodeName ] with [ new :: " . $object[ VALUE_NAME ] . " ].\n" );
// 			}
// 		}
// 	}

// 	$newSubject = $subject;
// 	if ( 0 === strpos( $subject, BNODE_PREFIX )
// 		&& 0 !== strpos( $subject, HASH_PREFIX ) ) {

// 		$newSubject = HASH_PREFIX . md5( serialize( $predicates ) );
// 		$bNodesMap[ $subject ] = $newSubject;

// 		// echo( "assigning new subject [ subject :: $subject ][ newSubject :: $newSubject ].\n" );
// 	}

// 	$newIndex[ $newSubject ] = $predicates;
// 	unset( $index[ $subject ] );

// 	return $newSubject;
// }


?>